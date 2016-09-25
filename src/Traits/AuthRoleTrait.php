<?php namespace Arcanedev\LaravelAuth\Traits;

use Arcanesoft\Contracts\Auth\Models\Role as RoleContract;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Trait     AuthRoleTrait
 *
 * @package  Arcanedev\LaravelAuth\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  \Illuminate\Database\Eloquent\Collection  roles
 *
 * @method    \Illuminate\Database\Eloquent\Relations\BelongsToMany  roles()
 * @method    \Arcanedev\LaravelAuth\Traits\AuthRoleTrait            load(mixed $relations)
 */
trait AuthRoleTrait
{
    /* ------------------------------------------------------------------------------------------------
     |  Role CRUD Functions
     | ------------------------------------------------------------------------------------------------
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
            $this->roles()->attach($role);
            $this->loadRoles($reload);
        }
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
        if ($role instanceof Eloquent) {
            $role = (array) $role->getKey();
        }

        $results = $this->roles()->detach($role);
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
        $results = $this->roles()->detach();
        $this->loadRoles($reload);

        return $results;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
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
        if ($id instanceof Eloquent)
            $id = $id->getKey();

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

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
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
