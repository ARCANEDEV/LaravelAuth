<?php namespace Arcanedev\LaravelAuth\Events\Permissions;

use Arcanesoft\Contracts\Auth\Models\Permission;

/**
 * Class     DetachedAllRolesFromPermission
 *
 * @package  Arcanedev\LaravelAuth\Events\Permissions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachedAllRolesFromPermission extends AbstractPermissionEvent
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
     * DetachedAllRolesFromPermission constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission  $permission
     * @param  int                                           $results
     */
    public function __construct(Permission $permission, $results)
    {
        parent::__construct($permission);

        $this->results = $results;
    }
}
