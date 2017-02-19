<?php namespace Arcanedev\LaravelAuth\Events\Permissions;

use Arcanesoft\Contracts\Auth\Models\Permission;

/**
 * Class     AttachingRoleToPermission
 *
 * @package  Arcanedev\LaravelAuth\Events\Permissions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class AttachingRoleToPermission extends AbstractPermissionEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\Role|int */
    public $role;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * AttachingRoleToPermission constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission  $permission
     * @param  \Arcanesoft\Contracts\Auth\Models\Role|int    $role
     */
    public function __construct(Permission $permission, $role)
    {
        parent::__construct($permission);

        $this->role = $role;
    }
}
