<?php namespace Arcanedev\LaravelAuth\Contracts;

/**
 * Interface  User
 *
 * @package   Arcanedev\LaravelAuth\Contracts
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int             id
 * @property  string          username
 * @property  string          first_name
 * @property  string          last_name
 * @property  string          email
 * @property  string          password
 * @property  string          remember_token
 * @property  bool            is_active
 * @property  bool            is_confirmed       (Optional)
 * @property  string          confirmation_code  (Optional)
 * @property  \Carbon\Carbon  confirmed_at       (Optional)
 * @property  \Carbon\Carbon  created_at
 * @property  \Carbon\Carbon  updated_at
 * @property  \Carbon\Carbon  deleted_at
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
     |  CRUD Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Detach a role from a user.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Role|int  $role
     *
     * @return int
     */
    public function detachRole($role);

    /**
     * Detach all roles from a user.
     *
     * @return int
     */
    public function detachAllRoles();

    /**
     * Update the user model in the database.
     *
     * @param  array  $attributes
     *
     * @return bool|int
     */
    public function update(array $attributes = []);

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
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