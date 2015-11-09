<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Bases\Model;
use Arcanedev\LaravelAuth\Contracts\User as UserContract;
use Arcanedev\LaravelAuth\Services\UserConfirmator;
use Arcanedev\LaravelAuth\Traits\AuthUserRelationships;
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
 *
 * @method    \Illuminate\Database\Eloquent\Builder     unconfirmed(string $code)
 */
class User
    extends Model
    implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, UserContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use Authenticatable, Authorizable, AuthUserRelationships, CanResetPassword, SoftDeletes;

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
     * Attach a role to a user.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|int  $role
     */
    public function attachRole($role)
    {
        if ($this->hasRole($role)) {
            return;
        }

        $this->roles()->attach($role);
        $this->load('roles');
    }

    /**
     * Detach a role from a user.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|int  $role
     *
     * @return int
     */
    public function detachRole($role)
    {
        return $this->detachAllRoles($role);
    }

    /**
     * Detach all roles from a user.
     *
     * @param  array|int  $ids
     *
     * @return int
     */
    public function detachAllRoles($ids = [])
    {
        $results = $this->roles()->detach($ids);
        $this->load('roles');

        return $results;
    }

    /**
     * Check if user has the given role (Role Model or Id).
     *
     * @param  mixed  $id
     *
     * @return bool
     */
    public function hasRole($id)
    {
        if ($id instanceof Model) {
            $id = $id->getKey();
        }

        return $this->roles->contains($id);
    }

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
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findUnconfirmed($code)
    {
        return self::unconfirmed($code)->firstOrFail();
    }

    /**
     * Confirm the new user account.
     *
     * @param  string  $code
     *
     * @return \Arcanedev\LaravelAuth\Models\User
     */
    public function confirm($code)
    {
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
