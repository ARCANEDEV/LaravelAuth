<?php namespace Arcanedev\LaravelAuth\Events\PermissionsGroups;

use Arcanesoft\Contracts\Auth\Models\PermissionsGroup;

/**
 * Class     CreatingPermission
 *
 * @package  Arcanedev\LaravelAuth\Events\PermissionsGroup
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CreatingPermission extends AbstractPermissionsGroupEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  array */
    public $permissionAttributes;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * CreatingPermission constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\PermissionsGroup  $group
     * @param  array                                               $permissionAttributes
     */
    public function __construct(PermissionsGroup $group, array $permissionAttributes)
    {
        parent::__construct($group);

        $this->permissionAttributes = $permissionAttributes;
    }
}
