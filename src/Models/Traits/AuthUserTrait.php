<?php namespace Arcanedev\LaravelAuth\Models\Traits;

use Arcanedev\LaravelAuth\Events\Users\AttachedRoleToUser;
use Arcanedev\LaravelAuth\Events\Users\AttachingRoleToUser;
use Arcanedev\LaravelAuth\Events\Users\DetachedRole;
use Arcanedev\LaravelAuth\Events\Users\DetachedRoles;
use Arcanedev\LaravelAuth\Events\Users\DetachingRole;
use Arcanedev\LaravelAuth\Events\Users\DetachingRoles;
use Arcanedev\LaravelAuth\Events\Users\SyncedUserWithRoles;
use Arcanedev\LaravelAuth\Events\Users\SyncingUserWithRoles;
use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\Role;
use Arcanesoft\Contracts\Auth\Models\Role as RoleContract;
use Illuminate\Database\Eloquent\Collection;

/**
 * Trait     AuthUserTrait
 *
 * @package  Arcanedev\LaravelAuth\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  \Illuminate\Database\Eloquent\Collection  permissions
 */
trait AuthUserTrait
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use AuthRoleTrait;

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get all user permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermissionsAttribute()
    {
        $permissions = new Collection;

        foreach ($this->roles as $role) {
            /** @var Role $role */
            $permissions = $permissions->merge($role->permissions);
        }

        return $permissions;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    /**
     * Attach a role to a user.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role|int  $role
     * @param  bool                                    $reload
     */
    public function attachRole($role, $reload = true)
    {
        if ( ! $this->hasRole($role)) {
            event(new AttachingRoleToUser($this, $role));
            $this->roles()->attach($role);
            $this->loadRoles($reload);
            event(new AttachedRoleToUser($this, $role));
        }
    }

    /**
     * Sync the roles by its slugs.
     *
     * @param  array  $slugs
     * @param  bool   $reload
     *
     * @return array
     */
    public function syncRoles(array $slugs, $reload = true)
    {
        /** @var \Illuminate\Database\Eloquent\Collection $roles */
        $roles  = app(RoleContract::class)->whereIn('slug', $slugs)->get();

        event(new SyncingUserWithRoles($this, $roles));
        $synced = $this->roles()->sync($roles->pluck('id'));
        event(new SyncedUserWithRoles($this, $roles, $synced));

        $this->loadRoles($reload);

        return $synced;
    }

    /**
     * Detach a role from a user.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role|int  $role
     * @param  bool                                        $reload
     *
     * @return int
     */
    public function detachRole($role, $reload = true)
    {
        event(new DetachingRole($this, $role));
        $results = $this->roles()->detach($role);
        event(new DetachedRole($this, $role, $results));

        $this->loadRoles($reload);

        return $results;
    }

    /**
     * Detach all roles from a user.
     *
     * @param  bool  $reload
     *
     * @return int
     */
    public function detachAllRoles($reload = true)
    {
        event(new DetachingRoles($this));
        $results = $this->roles()->detach();
        event(new DetachedRoles($this, $results));

        $this->loadRoles($reload);

        return $results;
    }

    /* -----------------------------------------------------------------
     |  Permission Check Methods
     | -----------------------------------------------------------------
     */
    /**
     * Check if the user has a permission.
     *
     * @param  string  $slug
     *
     * @return bool
     */
    public function may($slug)
    {
        $permissions = $this->permissions->filter(function(Permission $permission) use ($slug) {
            return $permission->slug === str_slug($slug, config('laravel-auth.slug-separator', '.'));
        });

        return ! $permissions->isEmpty();
    }

    /**
     * Check if the user has at least one permission.
     *
     * @param  array  $permissions
     * @param  array  $failedPermissions
     *
     * @return bool
     */
    public function mayOne(array $permissions, array &$failedPermissions = [])
    {
        foreach ($permissions as $permission) {
            if ( ! $this->may($permission))
                $failedPermissions[] = $permission;
        }

        return count($permissions) !== count($failedPermissions);
    }

    /**
     * Check if the user has all permissions.
     *
     * @param  array  $permissions
     * @param  array  $failedPermissions
     *
     * @return bool
     */
    public function mayAll(array $permissions, array &$failedPermissions = [])
    {
        $this->mayOne($permissions, $failedPermissions);

        return count($failedPermissions) === 0;
    }
}
