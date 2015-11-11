<?php namespace Arcanedev\LaravelAuth\Contracts;

/**
 * Interface  User
 *
 * @package   Arcanedev\LaravelAuth\Contracts
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                                       id
 * @property  string                                    username
 * @property  string                                    first_name
 * @property  string                                    last_name
 * @property  string                                    email
 * @property  string                                    password
 * @property  string                                    remember_token
 * @property  bool                                      is_admin
 * @property  bool                                      is_active
 * @property  bool                                      is_confirmed       (Optional)
 * @property  string                                    confirmation_code  (Optional)
 * @property  \Carbon\Carbon                            confirmed_at       (Optional)
 * @property  \Carbon\Carbon                            created_at
 * @property  \Carbon\Carbon                            updated_at
 * @property  \Carbon\Carbon                            deleted_at
 * @property  \Illuminate\Database\Eloquent\Collection  roles
 */
interface User
{
    /* ------------------------------------------------------------------------------------------------
     |  Relationships
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * User belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles();

    /* ------------------------------------------------------------------------------------------------
     |  Getters and Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Set a given attribute on the user model.
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return \Arcanedev\LaravelAuth\Contracts\User
     */
    public function setAttribute($key, $value);

    /* ------------------------------------------------------------------------------------------------
     |  User CRUD Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Save the user model to the database.
     *
     * @param  array  $options
     *
     * @return bool
     */
    public function save(array $options = []);

    /* ------------------------------------------------------------------------------------------------
     |  Role CRUD Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Attach a role to a user.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|int  $role
     * @param  bool                                    $reload
     */
    public function attachRole($role, $reload = true);

    /**
     * Detach a role from a user.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|int  $role
     * @param  bool                                    $reload
     *
     * @return int
     */
    public function detachRole($role, $reload = true);

    /**
     * Detach all roles from a user.
     *
     * @param  bool  $reload
     *
     * @return int
     */
    public function detachAllRoles($reload = true);

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if user has the given role (Role Model or Id).
     *
     * @param  mixed  $id
     *
     * @return bool
     */
    public function hasRole($id);

    /**
     * Check if user has a role by its slug.
     *
     * @param  string  $slug
     *
     * @return bool
     */
    public function is($slug);

    /**
     * Check if user has any of given roles.
     *
     * @param  array  $roles
     * @param  array  &$failedRoles
     *
     * @return bool
     */
    public function isAny(array $roles, array &$failedRoles = []);

    /**
     * Check if user match all the given roles.
     *
     * @param  array  $roles
     * @param  array  &$failedRoles
     *
     * @return bool
     */
    public function isAll(array $roles, array &$failedRoles = []);

    /**
     * Check if user is an administrator.
     *
     * @return bool
     */
    public function isAdmin();

    /**
     * Check if user has an activated account.
     *
     * @return bool
     */
    public function isActive();

    /**
     * Check if user has a confirmed account.
     *
     * @return bool
     */
    public function isConfirmed();

    /**
     * Activate the user.
     *
     * @return bool
     */
    public function activate();

    /**
     * Deactivate the user.
     *
     * @return bool
     */
    public function deactivate();
}
