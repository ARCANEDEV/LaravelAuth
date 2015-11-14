<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Bases\Model;
use Arcanedev\LaravelAuth\Exceptions\UserConfirmationException;
use Arcanedev\LaravelAuth\Services\UserConfirmator;
use Arcanedev\LaravelAuth\Traits\AuthUserTrait;
use Arcanesoft\Contracts\Auth\Models\User as UserContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

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
 * @property  \Carbon\Carbon                            created_at
 * @property  \Carbon\Carbon                            updated_at
 * @property  \Carbon\Carbon                            deleted_at
 * @property  \Illuminate\Database\Eloquent\Collection  roles
 * @property  \Illuminate\Database\Eloquent\Collection  permissions
 *
 * @method  static  bool                                   insert(array $values)
 * @method          \Illuminate\Database\Eloquent\Builder  unconfirmed(string $code)
 */
class User
    extends Model
    implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, UserContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use Authenticatable,
        Authorizable,
        AuthUserTrait,
        CanResetPassword,
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
        'username', 'first_name', 'last_name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden   = [
        'password', 'remember_token', 'confirmation_code',
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
        'confirmed_at', 'deleted_at'
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
        $this->setTable(config('laravel-auth.users.table', 'users'));

        parent::__construct($attributes);
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

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the full name attribute.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Set the password attribute.
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
     * Activate the user.
     *
     * @return bool
     */
    public function activate()
    {
        return $this->switchActive(true);
    }

    /**
     * Deactivate the user.
     *
     * @return bool
     */
    public function deactivate()
    {
        return $this->switchActive(false);
    }

    /**
     * Activate/deactivate the user.
     *
     * @param  bool  $active
     *
     * @return bool
     */
    protected function switchActive($active)
    {
        $this->is_active = boolval($active);

        return $this->save();
    }

    /**
     * Confirm the unconfirmed user account by confirmation code.
     *
     * @param  string  $code
     *
     * @return \Arcanedev\LaravelAuth\Models\User
     *
     * @throws \Arcanedev\LaravelAuth\Exceptions\UserConfirmationException
     */
    public function findUnconfirmed($code)
    {
        $unconfirmedUser = self::unconfirmed($code)->first();

        if ( ! $unconfirmedUser instanceof self) {
            throw (new UserConfirmationException)->setModel(self::class);
        }

        return $unconfirmedUser;
    }

    /**
     * Confirm the new user account.
     *
     * @param  \Arcanedev\LaravelAuth\Models\User|string  $code
     *
     * @return \Arcanedev\LaravelAuth\Models\User
     */
    public function confirm($code)
    {
        if ($code instanceof self) {
            $code = $code->confirmation_code;
        }

        $user = $this->findUnconfirmed($code);

        return (new UserConfirmator)->confirm($user);
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
     * Check if user has an activated account.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->is_active;
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
}
