<?php namespace Arcanedev\LaravelAuth\Events\Roles;

use Arcanesoft\Contracts\Auth\Models\Role;

/**
 * Class     DetachedUserFromRole
 *
 * @package  Arcanedev\LaravelAuth\Events\Roles
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachedUserFromRole extends AbstractRoleEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanesoft\Contracts\Auth\Models\User|int */
    public $user;

    /** @var  int */
    public $results;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * DetachedUserFromRole constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role      $role
     * @param  \Arcanesoft\Contracts\Auth\Models\User|int  $user
     * @param  int                                         $results
     */
    public function __construct(Role $role, $user, $results)
    {
        parent::__construct($role);

        $this->user    = $user;
        $this->results = $results;
    }
}
