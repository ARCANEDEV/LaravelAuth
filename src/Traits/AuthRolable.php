<?php namespace Arcanedev\LaravelAuth\Traits;

use Arcanedev\LaravelAuth\Models\Role;

/**
 * Trait     AuthRolable
 *
 * @package  Arcanedev\LaravelAuth\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  \Illuminate\Database\Eloquent\Collection  roles
 *
 * @method    \Illuminate\Database\Eloquent\Relations\BelongsToMany  roles()
 * @method    \Arcanedev\LaravelAuth\Traits\AuthRolable              load(mixed $relations)
 */
trait AuthRolable
{
    /* ------------------------------------------------------------------------------------------------
     |  Role CRUD Functions
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
        if ($role instanceof Role) {
            $role = (array) $role->getKey();
        }

        $results = $this->roles()->detach($role);
        $this->load('roles');

        return $results;
    }

    /**
     * Detach all roles from a user.
     *
     * @return int
     */
    public function detachAllRoles()
    {
        $results = $this->roles()->detach();
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
        if ($id instanceof Role) {
            $id = $id->getKey();
        }

        return $this->roles->contains($id);
    }
}
