<?php namespace Arcanedev\LaravelAuth\Listeners\Users;

use Arcanedev\LaravelAuth\Events\Users\CreatingUser;
use Arcanedev\LaravelAuth\Services\UserConfirmator;

/**
 * Class     GenerateConfirmationCode
 *
 * @package  Arcanedev\LaravelAuth\Listeners\Users
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class GenerateConfirmationCode
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the event.
     *
     * @param  \Arcanedev\LaravelAuth\Events\Users\CreatingUser  $event
     */
    public function handle(CreatingUser $event)
    {
        if (UserConfirmator::isEnabled()) {
            $event->user->confirmation_code = UserConfirmator::generateCode();
        }

        return $event;
    }
}
