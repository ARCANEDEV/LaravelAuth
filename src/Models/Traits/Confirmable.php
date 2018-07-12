<?php namespace Arcanedev\LaravelAuth\Models\Traits;

use Arcanedev\LaravelAuth\Exceptions\UserConfirmationException;
use Arcanedev\LaravelAuth\Services\UserConfirmator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait     Confirmable
 *
 * @package  Arcanedev\LaravelAuth\Models\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  string|null          confirmation_code  (Optional)
 * @property  \Carbon\Carbon|null  confirmed_at       (Optional)
 * @property  bool                 is_confirmed       (Optional)
 *
 * @method  static  \Illuminate\Database\Eloquent\Builder  confirmed()
 * @method  static  \Illuminate\Database\Eloquent\Builder  unconfirmed(string $code = null)
 */
trait Confirmable
{
    /* -----------------------------------------------------------------
     |  Scopes
     | -----------------------------------------------------------------
     */

    /**
     * Scope confirmed users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConfirmed(Builder $query)
    {
        return $query->whereNull('confirmation_code')
                     ->whereNotNull('confirmed_at');
    }

    /**
     * Scope unconfirmed users by code.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string                                 $code
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnconfirmed(Builder $query, $code = null)
    {
        return $query->whereNotNull('confirmation_code')
                     ->unless(is_null($code), function (Builder $q) use ($code) {
                         return $q->where('confirmation_code', $code);
                     })
                     ->whereNull('confirmed_at');
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the `is_confirmed` attribute.
     *
     * @return bool
     */
    public function getIsConfirmedAttribute()
    {
        return $this->isConfirmed();
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Confirm the unconfirmed user account by confirmation code.
     *
     * @param  string  $code
     *
     * @return \Arcanesoft\Contracts\Auth\Models\User
     *
     * @throws \Arcanedev\LaravelAuth\Exceptions\UserConfirmationException
     */
    public static function findUnconfirmed($code)
    {
        /** @var  \Arcanesoft\Contracts\Auth\Models\User|null  $unconfirmed */
        $unconfirmed = static::unconfirmed($code)->first();

        if ( ! $unconfirmed instanceof self)
            throw (new UserConfirmationException)->setModel(static::class);

        return $unconfirmed;
    }

    /**
     * Confirm the new user account.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\User|string  $code
     *
     * @return \Arcanesoft\Contracts\Auth\Models\User
     */
    public static function confirm($code)
    {
        if ($code instanceof self)
            $code = $code->confirmation_code;

        return (new UserConfirmator)->confirm(
            static::findUnconfirmed($code)
        );
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if user has a confirmed account.
     *
     * @return bool
     */
    public function isConfirmed()
    {
        return ! is_null($this->confirmed_at);
    }
}
