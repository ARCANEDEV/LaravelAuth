<?php namespace Arcanedev\LaravelAuth\Events\Users;

use Arcanesoft\Contracts\Auth\Models\User as UserContract;

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
    /**
     * @var \Arcanesoft\Contracts\Auth\Models\User|\Arcanedev\LaravelAuth\Models\User
     */
    public $user;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * UserEvent constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User|\Arcanedev\LaravelAuth\Models\User  $user
     */
    public function __construct(UserContract $user)
    {
        $this->user = $user;
    }
}
