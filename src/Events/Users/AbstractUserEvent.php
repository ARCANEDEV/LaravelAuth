<?php namespace Arcanedev\LaravelAuth\Events\Users;

use Arcanesoft\Contracts\Auth\Models\User;

/**
 * Class     AbstractUserEvent
 *
 * @package  Arcanedev\LaravelAuth\Events\Users
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class AbstractUserEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\User */
    public $user;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * AbstractUserEvent constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User  $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
