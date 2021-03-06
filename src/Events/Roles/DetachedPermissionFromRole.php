<?php namespace Arcanedev\LaravelAuth\Events\Roles;

use Arcanesoft\Contracts\Auth\Models\Role;

/**
 * Class     DetachedPermissionFromRole
 *
 * @package  Arcanedev\LaravelAuth\Events\Roles
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachedPermissionFromRole extends AbstractRoleEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanesoft\Contracts\Auth\Models\Permission|int */
    public $permission;

    /** @var int */
    public $results;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * DetachedPermissionFromRole constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role            $role
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission|int  $permission
     * @param  int                                               $results
     */
    public function __construct(Role $role, $permission, $results)
    {
        parent::__construct($role);

        $this->permission = $permission;
        $this->results    = $results;
    }
}
