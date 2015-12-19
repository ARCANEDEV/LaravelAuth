<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Bases\Model;

/**
 * Class     PermissionsGroup
 *
 * @package  Arcanedev\LaravelAuth\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                                       id
 * @property  string                                    name
 * @property  string                                    slug
 * @property  string                                    description
 * @property  \Carbon\Carbon                            created_at
 * @property  \Carbon\Carbon                            updated_at
 * @property  \Illuminate\Database\Eloquent\Collection  permissions
 */
class PermissionsGroup extends Model
{
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
        $this->setTable(config('laravel-auth.permissions-group.table', 'permissions_group'));

        parent::__construct($attributes);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Relationships
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Permissions Groups has many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'group_id');
    }

    /* ------------------------------------------------------------------------------------------------
     |  CRUD Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Create and attach a permission.
     *
     * @param  array  $attributes
     * @param  bool   $reload
     */
    public function createPermission(array $attributes, $reload = true)
    {
        $this->permissions()->create($attributes);

        if ($reload) {
            $this->load('permissions');
        }
    }

    /**
     * Attach the permission to a group.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|int  $permission
     * @param  bool                                          $reload
     */
    public function attachPermission($permission, $reload = true)
    {
        if ($this->hasPermission($permission)) {
            return;
        }

        $this->permissions()->save($permission);

        if ($reload) {
            $this->load('permissions');
        }
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
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
