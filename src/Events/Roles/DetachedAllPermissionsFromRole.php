<?php namespace Arcanedev\LaravelAuth\Events\Roles;

use Arcanesoft\Contracts\Auth\Models\Role;

/**
 * Class     DetachedAllPermissionsFromRole
 *
 * @package  Arcanedev\LaravelAuth\Events\Roles
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachedAllPermissionsFromRole
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
     * DetachedAllPermissionsFromRole constructor.
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
