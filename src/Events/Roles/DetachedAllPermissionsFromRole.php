<?php namespace Arcanedev\LaravelAuth\Events\Roles;

use Arcanesoft\Contracts\Auth\Models\Role;

/**
 * Class     DetachedAllPermissionsFromRole
 *
 * @package  Arcanedev\LaravelAuth\Events\Roles
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachedAllPermissionsFromRole extends AbstractRoleEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
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
        parent::__construct($role);

        $this->results = $results;
    }
}
