<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Events\Permissions\AttachedRoleToPermission;
use Arcanedev\LaravelAuth\Events\Permissions\AttachingRoleToPermission;
use Arcanedev\LaravelAuth\Events\Permissions\CreatedPermission;
use Arcanedev\LaravelAuth\Events\Permissions\CreatingPermission;
use Arcanedev\LaravelAuth\Events\Permissions\DeletedPermission;
use Arcanedev\LaravelAuth\Events\Permissions\DeletingPermission;
use Arcanedev\LaravelAuth\Events\Permissions\DetachedAllRolesFromPermission;
use Arcanedev\LaravelAuth\Events\Permissions\DetachedRoleFromPermission;
use Arcanedev\LaravelAuth\Events\Permissions\DetachingAllRolesFromPermission;
use Arcanedev\LaravelAuth\Events\Permissions\DetachingRoleFromPermission;
use Arcanedev\LaravelAuth\Events\Permissions\SavedPermission;
use Arcanedev\LaravelAuth\Events\Permissions\SavingPermission;
use Arcanedev\LaravelAuth\Events\Permissions\SyncedRolesWithPermission;
use Arcanedev\LaravelAuth\Events\Permissions\SyncingRolesWithPermission;
use Arcanedev\LaravelAuth\Events\Permissions\UpdatedPermission;
use Arcanedev\LaravelAuth\Events\Permissions\UpdatingPermission;
use Arcanedev\LaravelAuth\Models\Traits\Roleable;
use Arcanesoft\Contracts\Auth\Models\Permission as PermissionContract;
use Arcanesoft\Contracts\Auth\Models\Role as RoleContract;
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
 *
 * @property  \Illuminate\Database\Eloquent\Collection             roles
 * @property  \Arcanedev\LaravelAuth\Models\PermissionsGroup       group
 * @property  \Arcanedev\LaravelAuth\Models\Pivots\PermissionRole  pivot
 */
class Permission extends AbstractModel implements PermissionContract
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use Roleable;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['group_id', 'name', 'slug', 'description'];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $events = [
        'creating' => CreatingPermission::class,
        'created'  => CreatedPermission::class,
        'updating' => UpdatingPermission::class,
        'updated'  => UpdatedPermission::class,
        'saving'   => SavingPermission::class,
        'saved'    => SavedPermission::class,
        'deleting' => DeletingPermission::class,
        'deleted'  => DeletedPermission::class,
    ];

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

        $this->setTable(config('laravel-auth.permissions.table', 'permissions'));
    }

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
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

    /**
     * Permission belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this
            ->belongsToMany(
                config('laravel-auth.roles.model', Role::class),
                $this->getPrefix().'permission_role'
            )
            ->withTimestamps();
    }

    /* -----------------------------------------------------------------
     |  Setters & Getters
     | -----------------------------------------------------------------
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

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Attach a role to a user.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role|int  $role
     * @param  bool                                        $reload
     */
    public function attachRole($role, $reload = true)
    {
        if ($this->hasRole($role)) return;

        event(new AttachingRoleToPermission($this, $role));
        $this->roles()->attach($role);
        event(new AttachedRoleToPermission($this, $role));

        $this->loadRoles($reload);
    }

    /**
     * Sync the roles by its slugs.
     *
     * @param  array|\Illuminate\Support\Collection  $slugs
     * @param  bool                                  $reload
     *
     * @return array
     */
    public function syncRoles($slugs, $reload = true)
    {
        /** @var \Illuminate\Database\Eloquent\Collection $roles */
        $roles = app(RoleContract::class)->whereIn('slug', $slugs)->get();

        event(new SyncingRolesWithPermission($this, $roles));
        $synced = $this->roles()->sync($roles->pluck('id'));
        event(new SyncedRolesWithPermission($this, $roles, $synced));

        $this->loadRoles($reload);

        return $synced;
    }

    /**
     * Detach a role from a user.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role|int  $role
     * @param  bool                                        $reload
     *
     * @return int
     */
    public function detachRole($role, $reload = true)
    {
        event(new DetachingRoleFromPermission($this, $role));
        $results = $this->roles()->detach($role);
        event(new DetachedRoleFromPermission($this, $role, $results));

        $this->loadRoles($reload);

        return $results;
    }

    /**
     * Detach all roles from a user.
     *
     * @param  bool  $reload
     *
     * @return int
     */
    public function detachAllRoles($reload = true)
    {
        event(new DetachingAllRolesFromPermission($this));
        $results = $this->roles()->detach();
        event(new DetachedAllRolesFromPermission($this, $results));

        $this->loadRoles($reload);

        return $results;
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the permission has the same slug.
     *
     * @param  string  $slug
     *
     * @return bool
     */
    public function hasSlug($slug)
    {
        return $this->slug === $this->slugify($slug);
    }

    /* -----------------------------------------------------------------
     |  Other Functions
     | -----------------------------------------------------------------
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
