<?php namespace Arcanedev\LaravelAuth\Events\Roles;

use Arcanesoft\Contracts\Auth\Models\Role;

/**
 * Class     DetachingPermissionFromRole
 *
 * @package  Arcanedev\LaravelAuth\Events\Roles
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachingPermissionFromRole
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\Role */
    public $role;

    /** @var  \Arcanesoft\Contracts\Auth\Models\Permission|int */
    public $permission;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * DetachingPermissionFromRole constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role            $role
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission|int  $permission
     */
    public function __construct(Role $role, $permission)
    {
        $this->role       = $role;
        $this->permission = $permission;
    }
}
