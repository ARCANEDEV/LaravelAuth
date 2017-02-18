<?php namespace Arcanedev\LaravelAuth\Events\RoleUser;

use Arcanedev\LaravelAuth\Models\Pivots\RoleUser;

/**
 * Class     AbstractRoleUserEvent
 *
 * @package  Arcanedev\LaravelAuth\Events\RoleUser
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class AbstractRoleUserEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var \Arcanedev\LaravelAuth\Models\Pivots\RoleUser */
    public $roleUser;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * AbstractRoleUserEvent constructor.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Pivots\RoleUser  $roleUser
     */
    public function __construct(RoleUser $roleUser)
    {
        $this->roleUser = $roleUser;
    }
}
