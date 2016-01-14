<?php namespace Arcanedev\LaravelAuth\Traits;

use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\Role;
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
    use AuthRoleTrait, AuthUserRelationships;

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

    /* ------------------------------------------------------------------------------------------------
     |  Permission Check Functions
     | ------------------------------------------------------------------------------------------------
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

        return $permissions->count() === 1;
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
            if ( ! $this->may($permission)) {
                $failedPermissions[] = $permission;
            }
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
