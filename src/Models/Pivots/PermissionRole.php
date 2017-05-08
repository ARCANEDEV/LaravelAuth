<?php namespace Arcanedev\LaravelAuth\Models\Pivots;

/**
 * Class     PermissionRole
 *
 * @package  Arcanedev\LaravelAuth\Models\Pivots
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int             permission_id
 * @property  int             role_id
 * @property  \Carbon\Carbon  created_at
 * @property  \Carbon\Carbon  updated_at
 */
class PermissionRole extends AbstractPivot
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
        'permission_id' => 'integer',
        'role_id'       => 'integer',
    ];
}
