<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Bases\Model;
use Arcanedev\LaravelAuth\Contracts\User as UserContract;
use Arcanedev\LaravelAuth\Traits\AuthUserRelationships;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

/**
 * Class     User
 *
 * @package  Arcanedev\LaravelAuth\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int     id
 * @property  string  username
 * @property  string  first_name
 * @property  string  last_name
 * @property  string  email
 * @property  string  password
 * @property  string  remember_token
 * @property  bool    active
 * @property  bool    confirmed
 * @property  string  confirmation_code
 * @property  Carbon  confirmed_at
 * @property  Carbon  created_at
 * @property  Carbon  updated_at
 * @property  Carbon  deleted_at
 */
class User
    extends Model
    implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, UserContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use Authenticatable, Authorizable, AuthUserRelationships, CanResetPassword, SoftDeletes;

    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'first_name', 'last_name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden   = [
        'password', 'remember_token', 'confirmation_code',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'confirmed_at', 'deleted_at'
    ];

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('laravel-auth.users.table', 'users'));

        parent::__construct($attributes);
    }
}
