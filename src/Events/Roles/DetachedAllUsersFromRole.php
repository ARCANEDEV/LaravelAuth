<?php namespace Arcanedev\LaravelAuth\Events\Roles;

use Arcanesoft\Contracts\Auth\Models\Role;

class DetachedAllUsersFromRole
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\Role */
    public $role;

    /** @var  int */
    public $results;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * DetachingAllUsersFromRole constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role  $role
     * @param  int                                     $results
     */
    public function __construct(Role $role, $results)
    {
        $this->role    = $role;
        $this->results = $results;
    }
}
