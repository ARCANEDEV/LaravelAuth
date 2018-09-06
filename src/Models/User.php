<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Events\Users\{
    ActivatedUser, ActivatingUser, AttachedRoleToUser, AttachingRoleToUser, CreatedUser, CreatingUser,
    DeactivatedUser, DeactivatingUser, DeletedUser, DeletingUser, DetachedRoleFromUser, DetachedRolesFromUser,
    DetachingRoleFromUser, DetachingRolesFromUser, RestoredUser, RestoringUser, SavedUser, SavingUser,
    SyncedUserWithRoles, SyncingUserWithRoles, UpdatedUser, UpdatingUser
};
use Arcanedev\LaravelAuth\Services\SocialAuthenticator;
use Arcanesoft\Contracts\Auth\Models\{
    Permission as PermissionContract,
    Role as RoleContract,
    User as UserContract
};
use Illuminate\Auth\{
    Authenticatable, MustVerifyEmail, Passwords\CanResetPassword
};
use Illuminate\Contracts\Auth\{
    Access\Authorizable as AuthorizableContract,
    Authenticatable as AuthenticatableContract,
    CanResetPassword as CanResetPasswordContract
};
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class     User
 *
 * @package  Arcanedev\LaravelAuth\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                                       id
 * @property  string                                    username
 * @property  string                                    first_name
 * @property  string                                    last_name
 * @property  string                                    full_name
 * @property  string                                    email
 * @property  \Carbon\Carbon|null                       email_verified_at
 * @property  string                                    password
 * @property  string                                    remember_token
 * @property  bool                                      is_admin
 * @property  \Carbon\Carbon                            last_activity
 * @property  \Carbon\Carbon                            created_at
 * @property  \Carbon\Carbon                            updated_at
 * @property  \Carbon\Carbon|null                       activated_at
 * @property  \Carbon\Carbon                            deleted_at
 *
 * @property  \Illuminate\Database\Eloquent\Collection       roles
 * @property  \Illuminate\Support\Collection                 permissions
 * @property  \Arcanedev\LaravelAuth\Models\Pivots\RoleUser  pivot
 *
 * @method  static  \Illuminate\Database\Eloquent\Builder|static  lastActive(int $minutes = null)
 * @method  static  \Illuminate\Database\Eloquent\Builder|static  unverifiedEmail
 * @method  static  \Illuminate\Database\Eloquent\Builder|static  verifiedEmail
 */
class User
    extends AbstractModel
    implements UserContract, AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use Authenticatable,
        Authorizable,
        CanResetPassword,
        MustVerifyEmail,
        Traits\Roleable,
        Traits\Activatable,
        SoftDeletes;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------

     */
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden   = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'           => 'integer',
        'is_admin'     => 'boolean',
        'is_active'    => 'boolean',
        'is_confirmed' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'email_verified_at',
        'last_activity',
        'activated_at',
        'deleted_at',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'creating'  => CreatingUser::class,
        'created'   => CreatedUser::class,
        'updating'  => UpdatingUser::class,
        'updated'   => UpdatedUser::class,
        'saving'    => SavingUser::class,
        'saved'     => SavedUser::class,
        'deleting'  => DeletingUser::class,
        'deleted'   => DeletedUser::class,
        'restoring' => RestoringUser::class,
        'restored'  => RestoredUser::class,
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
        $this->setTable(config('laravel-auth.users.table', 'users'));

        if (SocialAuthenticator::isEnabled()) {
            $this->hidden   = array_merge($this->hidden, ['social_provider_id']);
            $this->fillable = array_merge($this->fillable, ['social_provider', 'social_provider_id']);
        }

        parent::__construct($attributes);
    }

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * User belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
                config('laravel-auth.roles.model', Role::class),
                $this->getPrefix().config('laravel-auth.role-user.table', 'role_user')
            )
            ->using(Pivots\RoleUser::class)
            ->withTimestamps();
    }

    /**
     * Get all user permissions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPermissionsAttribute()
    {
        return $this->active_roles
            ->pluck('permissions')
            ->flatten()
            ->unique(function (PermissionContract $permission) {
                return $permission->getKey();
            });
    }

    /* -----------------------------------------------------------------
     |  Scopes
     | -----------------------------------------------------------------
     */

    /**
     * Scope last active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int|null                               $minutes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastActive($query, $minutes = null)
    {
        $date = $this->freshTimestamp()->subMinutes(
            $minutes ?: config('laravel_auth.track-activity.minutes', 5)
        )->toDateTimeString();

        return $query->where('last_activity', '>=', $date);
    }

    /**
     * Scope unverified emails' users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnverifiedEmail($query)
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * Scope unverified emails' users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerifiedEmail($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Set the `email` attribute.
     *
     * @param  string  $email
     */
    public function setEmailAttribute($email)
    {
        $this->attributes['email'] = Str::lower($email);
    }

    /**
     * Set the `username` attribute.
     *
     * @param  string  $username
     */
    public function setUsernameAttribute($username)
    {
        $this->attributes['username'] = Str::slug($username, config('laravel-auth.users.slug-separator', '.'));
    }

    /**
     * Set the `first_name` attribute.
     *
     * @param  string  $firstName
     */
    public function setFirstNameAttribute($firstName)
    {
        $this->attributes['first_name'] = Str::title(Str::lower($firstName));
    }

    /**
     * Set the `last_name` attribute.
     *
     * @param  string  $lastName
     */
    public function setLastNameAttribute($lastName)
    {
        $this->attributes['last_name'] = Str::upper($lastName);
    }

    /**
     * Get the `full_name` attribute.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Set the `password` attribute.
     *
     * @param  string  $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Activate the model.
     *
     * @param  bool  $save
     *
     * @return bool
     */
    public function activate($save = true)
    {
        event(new ActivatingUser($this));
        $result = $this->switchActive(true, $save);
        event(new ActivatedUser($this));

        return $result;
    }

    /**
     * Deactivate the model.
     *
     * @param  bool  $save
     *
     * @return bool
     */
    public function deactivate($save = true)
    {
        event(new DeactivatingUser($this));
        $result = $this->switchActive(false, $save);
        event(new DeactivatedUser($this));

        return $result;
    }

    /**
     * Attach a role to a user.
     *
     * @param  \Arcanesoft\Contracts\Auth\Models\Role|int  $role
     * @param  bool                                        $reload
     */
    public function attachRole($role, $reload = true)
    {
        if ($this->hasRole($role)) return;

        event(new AttachingRoleToUser($this, $role));
        $this->roles()->attach($role);
        event(new AttachedRoleToUser($this, $role));

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
        /** @var  \Illuminate\Database\Eloquent\Collection  $roles */
        $roles = app(RoleContract::class)->whereIn('slug', $slugs)->get();

        event(new SyncingUserWithRoles($this, $roles));
        $synced = $this->roles()->sync($roles->pluck('id'));
        event(new SyncedUserWithRoles($this, $roles, $synced));

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
        event(new DetachingRoleFromUser($this, $role));
        $results = $this->roles()->detach($role);
        event(new DetachedRoleFromUser($this, $role, $results));

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
        event(new DetachingRolesFromUser($this));
        $results = $this->roles()->detach();
        event(new DetachedRolesFromUser($this, $results));

        $this->loadRoles($reload);

        return $results;
    }

    /**
     * Update the user's last activity.
     *
     * @param  bool  $save
     */
    public function updateLastActivity($save = true)
    {
        $this->forceFill(['last_activity' => $this->freshTimestamp()]);

        if ($save) $this->save();
    }

    /* -----------------------------------------------------------------
     |  Permission Check Methods
     | -----------------------------------------------------------------
     */
    /**
     * Check if the user has a permission.
     *
     * @param  string  $slug
     *
     * @return bool
     */
    public function may($slug)
    {
        return ! $this->permissions->filter(function (PermissionContract $permission) use ($slug) {
            return $permission->hasSlug($slug);
        })->isEmpty();
    }

    /**
     * Check if the user has at least one permission.
     *
     * @param  \Illuminate\Support\Collection|array  $permissions
     * @param  \Illuminate\Support\Collection        &$failed
     *
     * @return bool
     */
    public function mayOne($permissions, &$failed = null)
    {
        $permissions = is_array($permissions) ? collect($permissions) : $permissions;

        $failed = $permissions->reject(function ($permission) {
            return $this->may($permission);
        })->values();

        return $permissions->count() !== $failed->count();
    }

    /**
     * Check if the user has all permissions.
     *
     * @param  \Illuminate\Support\Collection|array  $permissions
     * @param  \Illuminate\Support\Collection        &$failed
     *
     * @return bool
     */
    public function mayAll($permissions, &$failed = null)
    {
        $this->mayOne($permissions, $failed);

        return $failed instanceof Collection ? $failed->isEmpty() : false;
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if user is an administrator.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Check if user is a moderator.
     *
     * @return bool
     */
    public function isModerator()
    {
        // Override this method to give more privileges than members.
        return false;
    }

    /**
     * Check if user is a member.
     *
     * @return bool
     */
    public function isMember()
    {
        return ! $this->isAdmin();
    }

    /**
     * Check if user can be impersonated.
     *
     * @return bool
     */
    public function canBeImpersonated()
    {
        return $this->isMember();
    }
}
