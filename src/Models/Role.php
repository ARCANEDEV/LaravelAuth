<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Events\Roles\AttachedPermissionToRole;
use Arcanedev\LaravelAuth\Events\Roles\AttachedUserToRole;
use Arcanedev\LaravelAuth\Events\Roles\AttachingPermissionToRole;
use Arcanedev\LaravelAuth\Events\Roles\AttachingUserToRole;
use Arcanedev\LaravelAuth\Events\Roles\CreatedRole;
use Arcanedev\LaravelAuth\Events\Roles\CreatingRole;
use Arcanedev\LaravelAuth\Events\Roles\DeletedRole;
use Arcanedev\LaravelAuth\Events\Roles\DeletingRole;
use Arcanedev\LaravelAuth\Events\Roles\DetachedAllPermissionsFromRole;
use Arcanedev\LaravelAuth\Events\Roles\DetachedAllUsersFromRole;
use Arcanedev\LaravelAuth\Events\Roles\DetachedPermissionFromRole;
use Arcanedev\LaravelAuth\Events\Roles\DetachedUserFromRole;
use Arcanedev\LaravelAuth\Events\Roles\DetachingAllPermissionsFromRole;
use Arcanedev\LaravelAuth\Events\Roles\DetachingAllUsersFromRole;
use Arcanedev\LaravelAuth\Events\Roles\DetachingPermissionFromRole;
use Arcanedev\LaravelAuth\Events\Roles\DetachingUserFromRole;
use Arcanedev\LaravelAuth\Events\Roles\SavedRole;
use Arcanedev\LaravelAuth\Events\Roles\SavingRole;
use Arcanedev\LaravelAuth\Events\Roles\UpdatedRole;
use Arcanedev\LaravelAuth\Events\Roles\UpdatingRole;
use Arcanedev\LaravelAuth\Models\Traits\Activatable;
use Arcanesoft\Contracts\Auth\Models\Role as RoleContract;
use Arcanesoft\Contracts\Auth\Models\Permission as PermissionContract;
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
 *
 * @property  \Arcanedev\LaravelAuth\Models\Pivots\RoleUser|\Arcanedev\LaravelAuth\Models\Pivots\PermissionRole  pivot
 */
class Role extends AbstractModel implements RoleContract
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use Activatable;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description'];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'creating' => CreatingRole::class,
        'created'  => CreatedRole::class,
        'updating' => UpdatingRole::class,
        'updated'  => UpdatedRole::class,
        'saving'   => SavingRole::class,
        'saved'    => SavedRole::class,
        'deleting' => DeletingRole::class,
        'deleted'  => DeletedRole::class,
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'        => 'integer',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
    ];

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('laravel-auth.roles.table', 'roles'));
    }

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
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
                $this->getPrefix().config('laravel-auth.role-user.table', 'permission_role')
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
                $this->getPrefix().config('laravel-auth.permission-role.table', 'permission_role')
            )
            ->using(Pivots\PermissionRole::class)
            ->withTimestamps();
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
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
     * Activate the model.
     *
     * @param  bool  $save
     *
     * @return bool
     */
    public function activate($save = true)
    {
        return $this->switchActive(true, $save);
    }

    /**
     * Deactivate the model.
     *
     * @param  bool  $save
     *
     * @return bool
     */
    public function deactivate($save = true)
    {
        return $this->switchActive(false, $save);
    }

    /**
     * Attach a permission to a role.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User|int  $user
     * @param  bool                                        $reload
     */
    public function attachUser($user, $reload = true)
    {
        if ($this->hasUser($user)) return;

        event(new AttachingUserToRole($this, $user));
        $this->users()->attach($user);
        event(new AttachedUserToRole($this, $user));

        $this->loadUsers($reload);
    }

    // TODO: Adding attach multiple users to a role ?

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
        event(new DetachingUserFromRole($this, $user));
        $results = $this->users()->detach($user);
        event(new DetachedUserFromRole($this, $user, $results));

        $this->loadUsers($reload);

        return $results;
    }

    // TODO: Adding detach multiple users to a role ?

    /**
     * Detach all users from a role.
     *
     * @param  bool  $reload
     *
     * @return int
     */
    public function detachAllUsers($reload = true)
    {
        event(new DetachingAllUsersFromRole($this));
        $results = $this->users()->detach();
        event(new DetachedAllUsersFromRole($this, $results));

        $this->loadUsers($reload);

        return $results;
    }

    /**
     * Attach a permission to a role.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission|int  $permission
     * @param  bool                                              $reload
     */
    public function attachPermission($permission, $reload = true)
    {
        if ($this->hasPermission($permission)) return;

        event(new AttachingPermissionToRole($this, $permission));
        $this->permissions()->attach($permission);
        event(new AttachedPermissionToRole($this, $permission));

        $this->loadPermissions($reload);
    }

    // TODO: Adding attach multiple permissions to a role ?

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
        if ( ! $this->hasPermission($permission)) return 0;

        event(new DetachingPermissionFromRole($this, $permission));
        $results = $this->permissions()->detach($permission);
        event(new DetachedPermissionFromRole($this, $permission, $results));

        $this->loadPermissions($reload);

        return $results;
    }

    // TODO: Adding detach multiple permissions to a role ?

    /**
     * Detach all permissions from a role.
     *
     * @param  bool  $reload
     *
     * @return int
     */
    public function detachAllPermissions($reload = true)
    {
        if ($this->permissions->isEmpty()) return 0;

        event(new DetachingAllPermissionsFromRole($this));
        $results = $this->permissions()->detach();
        event(new DetachedAllPermissionsFromRole($this, $results));

        $this->loadPermissions($reload);

        return $results;
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if role has the given user (User Model or Id).
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User|int  $id
     *
     * @return bool
     */
    public function hasUser($id)
    {
        if ($id instanceof Eloquent) $id = $id->getKey();

        return $this->users->contains('id', $id);
    }

    /**
     * Check if role has the given permission (Permission Model or Id).
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission|int  $id
     *
     * @return bool
     */
    public function hasPermission($id)
    {
        if ($id instanceof Eloquent)
            $id = $id->getKey();

        return $this->permissions->contains('id', $id);
    }

    /**
     * Check if role is associated with a permission by slug.
     *
     * @param  string  $slug
     *
     * @return bool
     */
    public function can($slug)
    {
        if ( ! $this->isActive())
            return false;

        return $this->permissions->filter(function (PermissionContract $permission) use ($slug) {
            return $permission->hasSlug($slug);
        })->first() !== null;
    }

    /**
     * Check if a role is associated with any of given permissions.
     *
     * @param  \Illuminate\Support\Collection|array  $permissions
     * @param  \Illuminate\Support\Collection        &$failed
     *
     * @return bool
     */
    public function canAny($permissions, &$failed = null)
    {
        $permissions = is_array($permissions) ? collect($permissions) : $permissions;

        $failed = $permissions->reject(function ($permission) {
            return $this->can($permission);
        })->values();

        return $permissions->count() !== $failed->count();
    }

    /**
     * Check if role is associated with all given permissions.
     *
     * @param  \Illuminate\Support\Collection|array  $permissions
     * @param  \Illuminate\Support\Collection        &$failed
     *
     * @return bool
     */
    public function canAll($permissions, &$failed = null)
    {
        $this->canAny($permissions, $failed);

        return $failed->isEmpty();
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
    public function hasSlug($value)
    {
        return $this->slug === $this->slugify($value);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
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
