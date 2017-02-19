<?php namespace Arcanedev\LaravelAuth\Events\Users;

use Arcanesoft\Contracts\Auth\Models\User;

/**
 * Class     DetachingRole
 *
 * @package  Arcanedev\LaravelAuth\Events\Users
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachingRole
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\User */
    public $user;

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
        $this->user = $user;
        $this->role = $role;
    }
}
