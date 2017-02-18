<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Bases\Model;
use Arcanedev\LaravelAuth\Models\Traits\AuthRoleTrait;
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
class Permission extends Model implements PermissionContract
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */
    use AuthRoleTrait;

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
        $this->setTable(config('laravel-auth.permissions.table', 'permissions'));

        parent::__construct($attributes);
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
     * @param  bool                                    $reload
     */
    public function attachRole($role, $reload = true)
    {
        if ( ! $this->hasRole($role)) {
            $this->roles()->attach($role);
            $this->loadRoles($reload);
        }
    }

    /**
     * Sync the roles by its slugs.
     *
     * @param  array  $slugs
     * @param  bool   $reload
     *
     * @return array
     */
    public function syncRoles(array $slugs, $reload = true)
    {
        /** @var \Illuminate\Database\Eloquent\Collection $roles */
        $roles  = app(RoleContract::class)->whereIn('slug', $slugs)->get();

//        event(new SyncingPermissionWithRoles($this, $roles));
        $synced = $this->roles()->sync($roles->pluck('id'));
//        event(new SyncedPermissionWithRoles($this, $roles, $synced));

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
        $results = $this->roles()->detach($role);
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
        return $this->detachRole(null, $reload);
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
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
