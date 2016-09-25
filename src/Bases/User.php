<?php namespace Arcanedev\LaravelAuth\Bases;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * Class     User
 *
 * @package  Arcanedev\LaravelAuth\Bases
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class User
    extends Model
    implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use Authenticatable, Authorizable, CanResetPassword;
}
