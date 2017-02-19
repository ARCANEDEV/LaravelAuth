<?php namespace Arcanedev\LaravelAuth\Events\PermissionsGroups;

use Arcanesoft\Contracts\Auth\Models\PermissionsGroup;

/**
 * Class     CreatedPermission
 *
 * @package  Arcanedev\LaravelAuth\Events\PermissionsGroups
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CreatedPermission extends AbstractPermissionsGroupEvent
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
     * CreatedPermission constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\PermissionsGroup  $group
     * @param  mixed                                               $permission
     */
    public function __construct(PermissionsGroup $group, $permission)
    {
        parent::__construct($group);

        $this->permission = $permission;
    }
}
