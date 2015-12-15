<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Bases\Model;
use Arcanedev\LaravelAuth\Traits\AuthPermissionRelationships;
use Arcanedev\LaravelAuth\Traits\AuthRoleTrait;
use Arcanedev\LaravelAuth\Traits\Slugable;
use Arcanesoft\Contracts\Auth\Models\Permission as PermissionContract;

/**
 * Class     Permission
 *
 * @package  Arcanedev\LaravelAuth\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                                       id
 * @property  string                                    name
 * @property  string                                    slug
 * @property  string                                    description
 * @property  string                                    model
 * @property  \Carbon\Carbon                            created_at
 * @property  \Carbon\Carbon                            updated_at
 * @property  \Illuminate\Database\Eloquent\Collection  roles
 */
class Permission extends Model implements PermissionContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use AuthPermissionRelationships, AuthRoleTrait, Slugable;

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

    /* ------------------------------------------------------------------------------------------------
     |  Setters & Getters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Set the slug attribute.
     *
     * @param  string  $slug
     */
    public function setSlugAttribute($slug)
    {
        $this->attributes['slug'] = $this->slugify($slug);
    }
}
