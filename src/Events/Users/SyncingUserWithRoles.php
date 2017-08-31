<?php namespace Arcanedev\LaravelAuth\Events\Users;

use Arcanesoft\Contracts\Auth\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class     SyncingUserWithRoles
 *
 * @package  Arcanedev\LaravelAuth\Events\Users
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SyncingUserWithRoles extends AbstractUserEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Illuminate\Database\Eloquent\Collection */
    public $roles;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * SyncingUserWithRoles constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User    $user
     * @param  \Illuminate\Database\Eloquent\Collection  $roles
     */
    public function __construct(User $user, Collection $roles)
    {
        parent::__construct($user);

        $this->roles = $roles;
    }
}
