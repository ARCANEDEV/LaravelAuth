<?php namespace Arcanedev\LaravelAuth\Events\PermissionsGroups;

use Arcanesoft\Contracts\Auth\Models\PermissionsGroup;

/**
 * Class     DetachingPermissionsFromGroup
 *
 * @package  Arcanedev\LaravelAuth\Events\PermissionsGroups
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachingPermissionsFromGroup extends AbstractPermissionsGroupEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  array */
    public $permissions;

    /** @var  int */
    public $detached;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * DetachingPermissionsFromGroup constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\PermissionsGroup  $group
     * @param  array                                               $permissions
     */
    public function __construct(PermissionsGroup $group, array $permissions)
    {
        parent::__construct($group);

        $this->permissions = $permissions;
    }
}
