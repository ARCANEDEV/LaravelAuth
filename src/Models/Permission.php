<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Bases\Model;
use Arcanedev\LaravelAuth\Models\Relationships\PermissionRelationships;
use Arcanedev\LaravelAuth\Models\Traits\AuthRoleTrait;
use Arcanesoft\Contracts\Auth\Models\Permission as PermissionContract;
use Illuminate\Support\Str;

/**
 * Class     Permission
 *
 * @package  Arcanedev\LaravelAuth\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                                            id
 * @property  int                                            group_id
 * @property  string                                         name
 * @property  string                                         slug
 * @property  string                                         description
 * @property  \Carbon\Carbon                                 created_at
 * @property  \Carbon\Carbon                                 updated_at
 * @property  \Illuminate\Database\Eloquent\Collection       roles
 * @property  \Arcanedev\LaravelAuth\Models\PermissionsGroup group
 */
class Permission extends Model implements PermissionContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use PermissionRelationships, AuthRoleTrait;

    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['group_id', 'name', 'slug', 'description'];

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
     |  Relationships
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Permission belongs to one group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(
            config('laravel-auth.permissions-groups.model', PermissionsGroup::class),
            'group_id'
        );
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

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if slug is the same as the given value.
     *
     * @param  string  $value
     *
     * @return bool
     */
    public function checkSlug($value)
    {
        return $this->slug === $this->slugify($value);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Slugify the value.
     *
     * @param  string  $value
     *
     * @return string
     */
    protected function slugify($value)
    {
        return Str::slug($value, config('laravel-auth.permissions.slug-separator', '.'));
    }
}
