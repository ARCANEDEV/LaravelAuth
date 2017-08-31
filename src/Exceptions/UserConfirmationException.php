<?php namespace Arcanedev\LaravelAuth\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class     UserAlreadyConfirmedException
 *
 * @package  Arcanedev\LaravelAuth\Exceptions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UserConfirmationException extends ModelNotFoundException
{
    /* -----------------------------------------------------------------
     |  Setters & Getters
     | -----------------------------------------------------------------
     */

    /**
     * Set the affected Eloquent model.
     *
     * @param  string  $model
     * @param  array   $ids
     *
     * @return self
     */
    public function setModel($model, $ids = [])
    {
        parent::setModel($model);

        $this->message = 'Unconfirmed user was not found.';

        return $this;
    }
}
