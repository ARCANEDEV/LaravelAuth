<?php namespace Arcanedev\LaravelAuth\Models\Traits;

use Arcanesoft\Contracts\Auth\Models\Role;

/**
 * Trait     Roleable
 *
 * @package  Arcanedev\LaravelAuth\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  \Illuminate\Database\Eloquent\Collection  roles
 *
 * @method    \Illuminate\Database\Eloquent\Relations\BelongsToMany  roles()
 * @method    \Arcanedev\LaravelAuth\Models\Traits\Roleable          load(mixed $relations)
 */
trait Roleable
{
    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */
    /**
     * Check if user has the given role (Role Model or Id).
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role|int  $id
     *
     * @return bool
     */
    public function hasRole($id)
    {
        if ($id instanceof Role) $id = $id->getKey();

        return $this->roles->contains('id', $id);
    }

    /**
     * Check if has all roles.
     *
     * @param  \Illuminate\Support\Collection|array  $roles
     * @param  \Illuminate\Support\Collection        &$failed
     *
     * @return bool
     */
    public function isAll($roles, &$failed = null)
    {
        $this->isOne($roles, $failed);

        return $failed->isEmpty();
    }

    /**
     * Check if has at least one role.
     *
     * @param  \Illuminate\Support\Collection|array  $roles
     * @param  \Illuminate\Support\Collection        &$failed
     *
     * @return bool
     */
    public function isOne($roles, &$failed = null)
    {
        $roles = is_array($roles) ? collect($roles) : $roles;

        $failed = $roles->reject(function ($role) {
            return $this->hasRoleSlug($role);
        })->values();

        return $roles->count() !== $failed->count();
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
        return ! $this->roles->filter->hasSlug($slug)->isEmpty();
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
