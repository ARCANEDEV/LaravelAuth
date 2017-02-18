<?php namespace Arcanedev\LaravelAuth\Models\Traits;

use Arcanesoft\Contracts\Auth\Models\Role as RoleContract;

/**
 * Trait     AuthRoleTrait
 *
 * @package  Arcanedev\LaravelAuth\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  \Illuminate\Database\Eloquent\Collection  roles
 *
 * @method    \Illuminate\Database\Eloquent\Relations\BelongsToMany  roles()
 * @method    \Arcanedev\LaravelAuth\Models\Traits\AuthRoleTrait     load(mixed $relations)
 */
trait AuthRoleTrait
{
    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */
    /**
     * Check if user has the given role (Role Model or Id).
     *
     * @param  mixed  $id
     *
     * @return bool
     */
    public function hasRole($id)
    {
        return $this->roles->contains($id);
    }

    /**
     * Check if has all roles.
     *
     * @param  array  $roles
     * @param  array  &$failedRoles
     *
     * @return bool
     */
    public function isAll(array $roles, array &$failedRoles = [])
    {
        $this->isOne($roles, $failedRoles);

        return count($failedRoles) === 0;
    }

    /**
     * Check if has at least one role.
     *
     * @param  array  $roles
     * @param  array  &$failedRoles
     *
     * @return bool
     */
    public function isOne(array $roles, array &$failedRoles = [])
    {
        foreach ($roles as $role) {
            if ( ! $this->hasRoleSlug($role))
                $failedRoles[] = $role;
        }

        return count($roles) !== count($failedRoles);
    }

    /**
     * Check if has a role by its slug.
     *
     * @param  string  $slug
     *
     * @return bool
     */
    public function hasRoleSlug($slug)
    {
        $roles = $this->roles->filter(function(RoleContract $role) use ($slug) {
            return $role->checkSlug($slug);
        });

        return ! $roles->isEmpty();
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */
    /**
     * Load all roles.
     *
     * @param  bool  $load
     *
     * @return self
     */
    protected function loadRoles($load = true)
    {
        return $load ? $this->load('roles') : $this;
    }
}
