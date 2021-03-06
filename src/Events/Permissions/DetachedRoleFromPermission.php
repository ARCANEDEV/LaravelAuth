<?php namespace Arcanedev\LaravelAuth\Events\Permissions;

use Arcanesoft\Contracts\Auth\Models\Permission;

/**
 * Class     DetachedRoleFromPermission
 *
 * @package  Arcanedev\LaravelAuth\Events\Permissions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachedRoleFromPermission extends AbstractPermissionEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanesoft\Contracts\Auth\Models\Role|int */
    public $role;

    /** @var  int */
    public $results;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * DetachedRoleFromPermission constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission  $permission
     * @param  \Arcanesoft\Contracts\Auth\Models\Role|int    $role
     * @param  int                                           $results
     */
    public function __construct(Permission $permission, $role, $results)
    {
        parent::__construct($permission);

        $this->role    = $role;
        $this->results = $results;
    }
}
