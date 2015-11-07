<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Bases\Model;
use Arcanedev\LaravelAuth\Contracts\Permission as PermissionContract;
use Arcanedev\LaravelAuth\Traits\AuthPermissionRelationships;
use Carbon\Carbon;

/**
 * Class     Permission
 *
 * @package  Arcanedev\LaravelAuth\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int     id
 * @property  string  name
 * @property  string  slug
 * @property  string  description
 * @property  string  model
 * @property  Carbon  created_at
 * @property  Carbon  updated_at
 */
class Permission extends Model implements PermissionContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use AuthPermissionRelationships;

    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description', 'model'];

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
        $this->setTable(config('laravel-auth.permissions.table', 'permissions'));

        parent::__construct($attributes);
    }
}
