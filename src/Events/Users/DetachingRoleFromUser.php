<?php namespace Arcanedev\LaravelAuth\Events\Users;

use Arcanesoft\Contracts\Auth\Models\User;

/**
 * Class     DetachingRole
 *
 * @package  Arcanedev\LaravelAuth\Events\Users
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachingRoleFromUser extends AbstractUserEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanesoft\Contracts\Auth\Models\Role|int */
    public $role;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * DetachingRole constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User      $user
     * @param  \Arcanesoft\Contracts\Auth\Models\Role|int  $role
     */
    public function __construct(User $user, $role)
    {
        parent::__construct($user);

        $this->role = $role;
    }
}
