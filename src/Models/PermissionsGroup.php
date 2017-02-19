<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Events\PermissionsGroups as PermissionsGroupEvents;
use Arcanesoft\Contracts\Auth\Models\Permission as PermissionContract;
use Arcanesoft\Contracts\Auth\Models\PermissionsGroup as PermissionsGroupContract;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

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
class PermissionsGroup extends AbstractModel implements PermissionsGroupContract
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
        parent::__construct($attributes);

        $this->setTable(
            config('laravel-auth.permissions-groups.table', 'permissions_groups')
        );
    }

    /* ------------------------------------------------------------------------------------------------
     |  Relationships
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Permissions Groups has many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->hasMany(
            config('laravel-auth.permissions.model', Permission::class),
            'group_id'
        );
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
        $this->attributes['slug'] = $this->slugify($slug);
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
        event(new PermissionsGroupEvents\CreatingPermission($this, $attributes));
        $permission = $this->permissions()->create($attributes);
        event(new PermissionsGroupEvents\CreatedPermission($this, $permission));

        $this->loadPermissions($reload);
    }

    /**
     * Attach the permission to a group.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission  $permission
     * @param  bool                                          $reload
     */
    public function attachPermission(&$permission, $reload = true)
    {
        if ($this->hasPermission($permission)) return;

        event(new PermissionsGroupEvents\AttachingPermissionToGroup($this, $permission));
        $permission = $this->permissions()->save($permission);
        event(new PermissionsGroupEvents\AttachedPermissionToGroup($this, $permission));

        $this->loadPermissions($reload);
    }

    /**
     * Attach the permission by id to a group.
     *
     * @param  int   $id
     * @param  bool  $reload
     *
     * @return \Arcanesoft\Contracts\Auth\Models\Permission|null
     */
    public function attachPermissionById($id, $reload = true)
    {
        $permission = $this->getPermissionById($id);

        if ($permission !== null) $this->attachPermission($permission, $reload);

        return $permission;
    }

    /**
     * Attach a collection of permissions to the group.
     *
     * @param  \Illuminate\Database\Eloquent\Collection|array  $permissions
     * @param  bool                                            $reload
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function attachPermissions($permissions, $reload = true)
    {
        event(new PermissionsGroupEvents\AttachingPermissionsToGroup($this, $permissions));
        $permissions = $this->permissions()->saveMany($permissions);
        event(new PermissionsGroupEvents\AttachedPermissionsToGroup($this, $permissions));

        $this->loadPermissions($reload);

        return $permissions;
    }

    /**
     * Attach the permission from a group.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission  $permission
     * @param  bool                                          $reload
     */
    public function detachPermission(&$permission, $reload = true)
    {
        if ( ! $this->hasPermission($permission)) return;

        /** @var  \Arcanesoft\Contracts\Auth\Models\Permission  $permission */
        $permission = $this->getPermissionFromGroup($permission);

        event(new PermissionsGroupEvents\DetachingPermissionFromGroup($this, $permission));
        $permission->update(['group_id' => 0]);
        event(new PermissionsGroupEvents\DetachedPermissionFromGroup($this, $permission));

        $this->loadPermissions($reload);
    }

    /**
     * Attach the permission by id to a group.
     *
     * @param  int   $id
     * @param  bool  $reload
     *
     * @return \Arcanesoft\Contracts\Auth\Models\Permission
     */
    public function detachPermissionById($id, $reload = true)
    {
        if ( ! is_null($permission = $this->getPermissionById($id)))
            $this->detachPermission($permission, $reload);

        return $permission;
    }

    /**
     * Detach multiple permissions by ids.
     *
     * @param  array  $ids
     * @param  bool   $reload
     *
     * @return int
     */
    public function detachPermissions(array $ids, $reload = true)
    {
        event(new PermissionsGroupEvents\DetachingPermissionsFromGroup($this, $ids));
        $detached = $this->permissions()->whereIn('id', $ids)->update(['group_id' => 0]);
        event(new PermissionsGroupEvents\DetachedPermissionsFromGroup($this, $ids, $detached));

        $this->loadPermissions($reload);

        return $detached;
    }

    /**
     * Detach all permissions from the group.
     *
     * @param  bool  $reload
     *
     * @return int
     */
    public function detachAllPermissions($reload = true)
    {
        event(new PermissionsGroupEvents\DetachingAllPermissionsFromGroup($this));
        $detached = $this->permissions()->update(['group_id' => 0]);
        event(new PermissionsGroupEvents\DetachedAllPermissionsFromGroup($this, $detached));

        $this->loadPermissions($reload);

        return $detached;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if role has the given permission (Permission Model or Id).
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission|int  $id
     *
     * @return bool
     */
    public function hasPermission($id)
    {
        if ($id instanceof Eloquent) $id = $id->getKey();

        return $this->getPermissionFromGroup($id) !== null;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get a permission from the group.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Permission|int  $id
     *
     * @return \Arcanesoft\Contracts\Auth\Models\Permission|null
     */
    private function getPermissionFromGroup($id)
    {
        if ($id instanceof Eloquent) $id = $id->getKey();

        $this->loadPermissions();

        return $this->permissions
            ->filter(function (PermissionContract $permission) use ($id) {
                return $permission->getKey() == $id;
            })
            ->first();
    }

    /**
     * Get a permission by id.
     *
     * @param  int  $id
     *
     * @return \Arcanesoft\Contracts\Auth\Models\Permission|null
     */
    private function getPermissionById($id)
    {
        return $this->permissions()->getRelated()->where('id', $id)->first();
    }

    /**
     * Load the permissions.
     *
     * @param  bool  $load
     *
     * @return self
     */
    protected function loadPermissions($load = true)
    {
        return $load ? $this->load('permissions') : $this;
    }

    /**
     * Slugify the value.
     *
     * @param  string  $value
     *
     * @return string
     */
    protected function slugify($value)
    {
        return Str::slug($value, config('laravel-auth.permissions-groups.slug-separator', '-'));
    }
}
