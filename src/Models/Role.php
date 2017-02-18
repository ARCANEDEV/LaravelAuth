<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Bases\Model;
use Arcanedev\LaravelAuth\Models\Traits\Activatable;
use Arcanesoft\Contracts\Auth\Models\Permission as PermissionContract;
use Arcanesoft\Contracts\Auth\Models\Role as RoleContract;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

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
 *
 * @property  \Illuminate\Database\Eloquent\Collection       users
 * @property  \Illuminate\Database\Eloquent\Collection       permissions
 * @property  \Arcanedev\LaravelAuth\Models\Pivots\RoleUser  pivot
 */
class Role extends Model implements RoleContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use Activatable;

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
     |  Relationships
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this
            ->belongsToMany(
                config('laravel-auth.users.model', User::class),
                config('laravel-auth.database.prefix').'role_user',
                'role_id',
                'user_id'
            )
            ->using(Pivots\RoleUser::class)
            ->withTimestamps();
    }

    /**
     * Role belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this
            ->belongsToMany(
                config('laravel-auth.permissions.model', Permission::class),
                config('laravel-auth.database.prefix').'permission_role',
                'role_id',
                'permission_id'
            )
            ->using(Pivots\PermissionRole::class)
            ->withTimestamps();
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
        if ($user instanceof Eloquent)
            $user = (array) $user->getKey();

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
        if ($id instanceof Eloquent)
            $id = $id->getKey();

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
        if ($permission instanceof Eloquent)
            $permission = (array) $permission->getKey();

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
        if ($id instanceof Eloquent)
            $id = $id->getKey();

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
            if ( ! $this->can($permission))
                $failedPermissions[] = $permission;
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

    /**
     * Check if slug is the same as the given value.
     *
     * @param  string  $value
     *
     * @return bool
     */
    public function checkSlug($value)
    {
        return $this->slug === $this->slugify($value);
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

    /**
     * Slugify the value.
     *
     * @param  string  $value
     *
     * @return string
     */
    protected function slugify($value)
    {
        return Str::slug($value, config('laravel-auth.roles.slug-separator', '-'));
    }
}
