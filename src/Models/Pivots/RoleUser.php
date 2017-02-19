<?php namespace Arcanedev\LaravelAuth\Models\Pivots;

/**
 * Class     RoleUser
 *
 * @package  Arcanedev\LaravelAuth\Models\Pivots
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int             user_id
 * @property  int             role_id
 * @property  \Carbon\Carbon  created_at
 * @property  \Carbon\Carbon  updated_at
 */
class RoleUser extends AbstractPivot
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'role_id' => 'integer',
    ];
}
