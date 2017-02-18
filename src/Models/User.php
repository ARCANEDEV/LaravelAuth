<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Bases\User as Authenticatable;
use Arcanedev\LaravelAuth\Exceptions\UserConfirmationException;
use Arcanedev\LaravelAuth\Services\SocialAuthenticator;
use Arcanedev\LaravelAuth\Services\UserConfirmator;
use Arcanedev\LaravelAuth\Models\Traits\Activatable;
use Arcanedev\LaravelAuth\Models\Traits\AuthUserTrait;
use Arcanedev\Support\Traits\PrefixedModel;
use Arcanesoft\Contracts\Auth\Models\User as UserContract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Class     User
 *
 * @package  Arcanedev\LaravelAuth\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                                       id
 * @property  string                                    username
 * @property  string                                    first_name
 * @property  string                                    last_name
 * @property  string                                    full_name
 * @property  string                                    email
 * @property  string                                    password
 * @property  string                                    remember_token
 * @property  bool                                      is_admin
 * @property  bool                                      is_active
 * @property  bool                                      is_confirmed       (Optional)
 * @property  string                                    confirmation_code  (Optional)
 * @property  \Carbon\Carbon                            confirmed_at       (Optional)
 * @property  \Carbon\Carbon                            last_activity
 * @property  \Carbon\Carbon                            created_at
 * @property  \Carbon\Carbon                            updated_at
 * @property  \Carbon\Carbon                            deleted_at
 *
 * @property  \Illuminate\Database\Eloquent\Collection       roles
 * @property  \Illuminate\Database\Eloquent\Collection       permissions
 * @property  \Arcanedev\LaravelAuth\Models\Pivots\RoleUser  pivot
 *
 * @method  static  bool                                   insert(array $values)
 * @method          \Illuminate\Database\Eloquent\Builder  unconfirmed(string $code)
 * @method          \Illuminate\Database\Eloquent\Builder  lastActive(int $minutes = null)
 */
class User extends Authenticatable implements UserContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use AuthUserTrait,
        Activatable,
        SoftDeletes;

    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden   = [
        'password',
        'remember_token',
        'confirmation_code',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_admin'     => 'boolean',
        'is_active'    => 'boolean',
        'is_confirmed' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'confirmed_at',
        'last_activity',
        'deleted_at',
    ];

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setupModel();
    }

    /**
     * Setup the model.
     */
    protected function setupModel()
    {
        $this->setTable(config('laravel-auth.users.table', 'users'));

        if (SocialAuthenticator::isEnabled()) {
            $this->hidden   = array_merge($this->hidden, ['social_provider_id']);
            $this->fillable = array_merge($this->fillable, ['social_provider', 'social_provider_id']);
        }
    }

    /* ------------------------------------------------------------------------------------------------
     |  Relationships
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * User belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this
            ->belongsToMany(
                config('laravel-auth.roles.model', Role::class),
                config('laravel-auth.database.prefix').'role_user',
                'user_id',
                'role_id'
            )
            ->using(Pivots\RoleUser::class)
            ->withTimestamps();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Scopes
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Scope unconfirmed users by code.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string                                 $code
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnconfirmed($query, $code)
    {
        return $query->where('is_confirmed', false)
                     ->where('confirmation_code', $code)
                     ->whereNull('confirmed_at');
    }

    /**
     * Scope last active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int|null                               $minutes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastActive($query, $minutes = null)
    {
        $minutes = $minutes ?: config('laravel_auth.track-activity.minutes', 5);

        $date = Carbon::now()->subMinutes($minutes);

        return $query->where('last_activity', '>=', $date->toDateTimeString());
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Set the `username` attribute.
     *
     * @param  string  $username
     */
    public function setUsernameAttribute($username)
    {
        $this->attributes['username'] = $this->slugify($username);
    }

    /**
     * Get the `full_name` attribute.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Set the `password` attribute.
     *
     * @param  string  $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    /* ------------------------------------------------------------------------------------------------
     |  CRUD Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Confirm the unconfirmed user account by confirmation code.
     *
     * @param  string  $code
     *
     * @return \Arcanesoft\Contracts\Auth\Models\User
     *
     * @throws \Arcanedev\LaravelAuth\Exceptions\UserConfirmationException
     */
    public function findUnconfirmed($code)
    {
        $unconfirmedUser = self::unconfirmed($code)->first();

        if ( ! $unconfirmedUser instanceof self)
            throw (new UserConfirmationException)->setModel(self::class);

        return $unconfirmedUser;
    }

    /**
     * Confirm the new user account.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User|string  $code
     *
     * @return \Arcanesoft\Contracts\Auth\Models\User
     */
    public function confirm($code)
    {
        if ($code instanceof self)
            $code = $code->confirmation_code;

        $user = $this->findUnconfirmed($code);

        return (new UserConfirmator)->confirm($user);
    }

    /**
     * Update the user's last activity.
     *
     * @param  bool  $save
     */
    public function updateLastActivity($save = true)
    {
        $this->forceFill(['last_activity' => Carbon::now()]);

        if ($save) $this->save();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if user is an administrator.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Check if user is a moderator.
     *
     * @return bool
     */
    public function isModerator()
    {
        // Override this method to give more privileges than members.
        return false;
    }

    /**
     * Check if user is a member.
     *
     * @return bool
     */
    public function isMember()
    {
        return ! $this->isAdmin();
    }

    /**
     * Check if user has a confirmed account.
     *
     * @return bool
     */
    public function isConfirmed()
    {
        return $this->is_confirmed;
    }

    /**
     * Check if user can be impersonated.
     *
     * @return bool
     */
    public function canBeImpersonated()
    {
        return $this->isMember();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Slugify the value.
     *
     * @param  string  $value
     *
     * @return string
     */
    protected function slugify($value)
    {
        return Str::slug($value, config('laravel-auth.users.slug-separator', '.'));
    }
}
