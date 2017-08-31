<?php namespace Arcanedev\LaravelAuth\Events\Users;

use Arcanesoft\Contracts\Auth\Models\User;

/**
 * Class     DetachedRole
 *
 * @package  Arcanedev\LaravelAuth\Events\Users
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachedRoleFromUser extends AbstractUserEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanesoft\Contracts\Auth\Models\Role|int */
    public $role;

    /** @var int */
    public $results;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * DetachedRole constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User      $user
     * @param  \Arcanesoft\Contracts\Auth\Models\Role|int  $role
     * @param  int                                         $results
     */
    public function __construct(User $user, $role, $results)
    {
        parent::__construct($user);

        $this->role    = $role;
        $this->results = $results;
    }
}
