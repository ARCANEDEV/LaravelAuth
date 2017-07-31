<?php namespace Arcanedev\LaravelAuth\Events\Users;

use Arcanesoft\Contracts\Auth\Models\User;

/**
 * Class     DetachedRoles
 *
 * @package  Arcanedev\LaravelAuth\Events\Users
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DetachedRolesFromUser extends AbstractUserEvent
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var int */
    public $results;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * DetachedRole constructor.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User  $user
     * @param  int                                     $results
     */
    public function __construct(User $user, $results)
    {
        parent::__construct($user);

        $this->results = $results;
    }
}
