<?php namespace Arcanedev\LaravelAuth\Events\PermissionsGroups;

use Arcanesoft\Contracts\Auth\Models\PermissionsGroup;

/**
 * Class     AttachedPermissionsToGroup
 *
 * @package  Arcanedev\LaravelAuth\Events\PermissionsGroups
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class AttachedPermissionsToGroup extends AbstractPermissionsGroupEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Illuminate\Support\Collection|array */
    public $permissions;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * AttachedPermissionsToGroup constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\PermissionsGroup  $group
     * @param  \Illuminate\Support\Collection|array                $permissions
     */
    public function __construct(PermissionsGroup $group, $permissions)
    {
        parent::__construct($group);

        $this->permissions = $permissions;
    }
}
