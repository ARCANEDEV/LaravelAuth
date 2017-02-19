<?php namespace Arcanedev\LaravelAuth\Events\Permissions;

use Arcanesoft\Contracts\Auth\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class     SyncingRolesWithPermission
 *
 * @package  Arcanedev\LaravelAuth\Events\Permissions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SyncingRolesWithPermission
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\Permission */
    public $permission;

    /** @var  \Illuminate\Database\Eloquent\Collection */
    public $roles;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * SyncingRolesWithPermission constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission  $permission
     * @param  \Illuminate\Database\Eloquent\Collection      $roles
     */
    public function __construct(Permission $permission, Collection $roles)
    {
        $this->permission = $permission;
        $this->roles      = $roles;
    }
}
