<?php namespace Arcanedev\LaravelAuth\Models;

use Carbon\Carbon;

/**
 * Class     PasswordReset
 *
 * @package  Arcanedev\LaravelAuth\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  string          email
 * @property  string          token
 * @property  \Carbon\Carbon  created_at
 *
 * @property  \Arcanedev\LaravelAuth\Models\User  $user
 */
class PasswordReset extends AbstractModel
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden  = ['token'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates   = [self::CREATED_AT];

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setConnection(null)->setPrefix(null);
        $this->setTable(config('auth.passwords.users.table', 'password_resets'));
    }

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * The user relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(
            config('laravel-auth.users.model', User::class),
            'email',
            'email'
        );
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the token repository.
     *
     * @return \Illuminate\Auth\Passwords\TokenRepositoryInterface
     */
    public static function getTokenRepository()
    {
        return app('auth.password')->getRepository();
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the password reset was expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->created_at->lt(
            Carbon::now()->subMinutes(config('auth.passwords.users.expire', 60))
        );
    }
}
