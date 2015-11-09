<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Bases\Model;
use Arcanedev\LaravelAuth\Contracts\Role as RoleContract;
use Arcanedev\LaravelAuth\Traits\AuthRoleRelationships;

/**
 * Class     Role
 *
 * @package  Arcanedev\LaravelAuth\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                                       id
 * @property  string                                    slug
 * @property  string                                    description
 * @property  bool                                      is_active
 * @property  bool                                      is_locked
 * @property  \Carbon\Carbon                            created_at
 * @property  \Carbon\Carbon                            updated_at
 * @property  \Illuminate\Database\Eloquent\Collection  users
 * @property  \Illuminate\Database\Eloquent\Collection  permissions
 */
class Role extends Model implements RoleContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use AuthRoleRelationships;

    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description'];

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
        $this->setTable(config('laravel-auth.roles.table', 'roles'));

        parent::__construct($attributes);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Set the name attribute.
     *
     * @param  string  $name
     */
    public function setNameAttribute($name)
    {
        $this->attributes['name'] = $name;
        $this->setSlugAttribute($name);
    }

    /**
     * Set the slug attribute.
     *
     * @param  string  $slug
     */
    public function setSlugAttribute($slug)
    {
        $this->attributes['slug'] = str_slug($slug);
    }

    /* ------------------------------------------------------------------------------------------------
     |  CRUD Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Attach a permission to a role.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|int  $permission
     *
     * @return int|bool
     */
    public function attachPermission($permission)
    {
        if ($this->hasPermission($permission)) {
            return;
        }

        $this->permissions()->attach($permission);
        $this->load('permissions');
    }

    /**
     * Detach a permission from a role.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|int  $permission
     *
     * @return int
     */
    public function detachPermission($permission)
    {
        if ($permission instanceof Permission) {
            $permission = (array) $permission->getKey();
        }

        $result = $this->permissions()->detach($permission);
        $this->load('permissions');

        return $result;
    }

    /**
     * Detach all permissions.
     *
     * @return int
     */
    public function detachAllPermissions()
    {
        $result = $this->permissions()->detach();
        $this->load('permissions');

        return $result;
    }

    /**
     * Check if role has the given permission (Permission Model or Id).
     *
     * @param  mixed  $id
     *
     * @return bool
     */
    public function hasPermission($id)
    {
        if ($id instanceof Permission) {
            $id = $id->getKey();
        }

        return $this->permissions->contains($id);
    }
}
