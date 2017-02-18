<?php namespace Arcanedev\LaravelAuth\Events\Users;

use Arcanesoft\Contracts\Auth\Models\Role;
use Arcanesoft\Contracts\Auth\Models\User;

/**
 * Class     DetachedRole
 *
 * @package  Arcanedev\LaravelAuth\Events\Users
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachedRole
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /**
     * @var \Arcanesoft\Contracts\Auth\Models\User
     */
    public $user;

    /**
     * @var \Arcanesoft\Contracts\Auth\Models\Role
     */
    public $role;

    /**
     * @var int
     */
    public $results;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * DetachedRole constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User  $user
     * @param  \Arcanesoft\Contracts\Auth\Models\Role  $role
     * @param  int                                     $results
     */
    public function __construct(User $user, Role $role, $results)
    {
        $this->user    = $user;
        $this->role    = $role;
        $this->results = $results;
    }
}
