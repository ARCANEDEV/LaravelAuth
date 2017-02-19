<?php namespace Arcanedev\LaravelAuth\Events\PermissionsGroups;

use Arcanesoft\Contracts\Auth\Models\Permission;
use Arcanesoft\Contracts\Auth\Models\PermissionsGroup;

/**
 * Class     AttachedPermissionToGroup
 *
 * @package  Arcanedev\LaravelAuth\Events\PermissionsGroups
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class AttachedPermissionToGroup extends AbstractPermissionsGroupEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\Permission */
    public $permission;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * AttachedPermissionToGroup constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\PermissionsGroup  $group
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission        $permission
     */
    public function __construct(PermissionsGroup $group, Permission $permission)
    {
        parent::__construct($group);

        $this->permission = $permission;
    }
}
