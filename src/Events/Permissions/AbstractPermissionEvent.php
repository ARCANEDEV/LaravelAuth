<?php namespace Arcanedev\LaravelAuth\Events\Permissions;

use Arcanesoft\Contracts\Auth\Models\Permission as PermissionContract;

/**
 * Class     AbstractPermissionEvent
 *
 * @package  Arcanedev\LaravelAuth\Events\Permissions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class AbstractPermissionEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /**
     * @var \Arcanesoft\Contracts\Auth\Models\Permission|\Arcanedev\LaravelAuth\Models\Permission
     */
    public $permission;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * UserEvent constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission|\Arcanedev\LaravelAuth\Models\Permission  $permission
     */
    public function __construct(PermissionContract $permission)
    {
        $this->permission = $permission;
    }
}
