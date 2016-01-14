<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Bases\Model;
use Arcanedev\LaravelAuth\Traits\Activatable;
use Arcanedev\LaravelAuth\Traits\AuthRoleRelationships;
use Arcanedev\LaravelAuth\Traits\Slugable;
use Arcanesoft\Contracts\Auth\Models\Role as RoleContract;
use Arcanesoft\Contracts\Auth\Models\Permission as PermissionContract;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class     Role
 *
 * @package  Arcanedev\LaravelAuth\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                                       id
 * @property  string                                    name
 * @property  string                                    slug
 * @property  string                                    description
 * @property  bool                                      is_active
 * @property  bool                                      is_locked
 * @property  \Carbon\Carbon                            created_at
 * @property  \Carbon\Carbon                            updated_at
 * @property  \Illuminate\Database\Eloquent\Collection  users
 * @property  \Illuminate\Database\Eloquent\Collection  permissions
 */
class Role extends Model implements RoleContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use Activatable, AuthRoleRelationships, Slugable;

    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
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
        $this->setTable(config('laravel-auth.roles.table', 'roles'));

        parent::__construct($attributes);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Set the name attribute.
     *
     * @param  string  $name
     */
    public function setNameAttribute($name)
    {
        $this->attributes['name'] = $name;
        $this->setSlugAttribute($name);
    }

    /**
     * Set the slug attribute.
     *
     * @param  string  $slug
     */
    public function setSlugAttribute($slug)
    {
        $this->attributes['slug'] = $this->slugify($slug);
    }

    /* ------------------------------------------------------------------------------------------------
     |  CRUD Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Attach a permission to a role.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User|int  $user
     * @param  bool                                        $reload
     */
    public function attachUser($user, $reload = true)
    {
        if ( ! $this->hasUser($user)) {
            $this->users()->attach($user);
            $this->loadUsers($reload);
        }
    }

    /**
     * Detach a user from a role.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User|int  $user
     * @param  bool                                        $reload
     *
     * @return int
     */
    public function detachUser($user, $reload = true)
    {
        if ($user instanceof Eloquent) {
            $user = (array) $user->getKey();
        }

        $result = $this->users()->detach($user);
        $this->loadUsers($reload);

        return $result;
    }

    /**
     * Detach all users from a role.
     *
     * @param  bool  $reload
     *
     * @return int
     */
    public function detachAllUsers($reload = true)
    {
        $result = $this->users()->detach();
        $this->loadUsers($reload);

        return $result;
    }

    /**
     * Check if role has the given user (User Model or Id).
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User|int  $id
     *
     * @return bool
     */
    public function hasUser($id)
    {
        if ($id instanceof Eloquent) {
            $id = $id->getKey();
        }

        return $this->users->contains($id);
    }

    /**
     * Attach a permission to a role.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission|int  $permission
     * @param  bool                                              $reload
     */
    public function attachPermission($permission, $reload = true)
    {
        if ( ! $this->hasPermission($permission)) {
            $this->permissions()->attach($permission);
            $this->loadPermissions($reload);
        }
    }

    /**
     * Detach a permission from a role.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission|int  $permission
     * @param  bool                                              $reload
     *
     * @return int
     */
    public function detachPermission($permission, $reload = true)
    {
        if ($permission instanceof Eloquent) {
            $permission = (array) $permission->getKey();
        }

        $result = $this->permissions()->detach($permission);
        $this->loadPermissions($reload);

        return $result;
    }

    /**
     * Detach all permissions from a role.
     *
     * @param  bool  $reload
     *
     * @return int
     */
    public function detachAllPermissions($reload = true)
    {
        $result = $this->permissions()->detach();
        $this->loadPermissions($reload);

        return $result;
    }

    /**
     * Check if role has the given permission (Permission Model or Id).
     *
     * @param  mixed  $id
     *
     * @return bool
     */
    public function hasPermission($id)
    {
        if ($id instanceof Eloquent) {
            $id = $id->getKey();
        }

        return $this->permissions->contains($id);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if role is associated with a permission by slug.
     *
     * @param  string  $slug
     *
     * @return bool
     */
    public function can($slug)
    {
        $permissions = $this->permissions->filter(function(PermissionContract $permission) use ($slug) {
            return $permission->checkSlug($slug);
        });

        return $permissions->count() === 1;
    }

    /**
     * Check if a role is associated with any of given permissions.
     *
     * @param  array  $permissions
     * @param  array  &$failedPermissions
     *
     * @return bool
     */
    public function canAny(array $permissions, array &$failedPermissions = [])
    {
        foreach ($permissions as $permission) {
            if ( ! $this->can($permission)) {
                $failedPermissions[] = $permission;
            }
        }

        return count($permissions) !== count($failedPermissions);
    }

    /**
     * Check if role is associated with all given permissions.
     *
     * @param  array  $permissions
     * @param  array  &$failedPermissions
     *
     * @return bool
     */
    public function canAll(array $permissions, array &$failedPermissions = [])
    {
        $this->canAny($permissions, $failedPermissions);

        return count($failedPermissions) === 0;
    }

    /**
     * Check if the role is locked.
     *
     * @return bool
     */
    public function isLocked()
    {
        return $this->is_locked;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Load the users.
     *
     * @param  bool  $load
     *
     * @return self
     */
    protected function loadUsers($load = true)
    {
        return $load ? $this->load('users') : $this;
    }

    /**
     * Load the permissions.
     *
     * @param  bool  $load
     *
     * @return self
     */
    protected function loadPermissions($load = true)
    {
        return $load ? $this->load('permissions') : $this;
    }
}
