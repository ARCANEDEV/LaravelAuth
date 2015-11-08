<?php namespace Arcanedev\LaravelAuth\Contracts;

/**
 * Interface  Role
 *
 * @package   Arcanedev\LaravelAuth\Contracts
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
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
interface Role
{
    /* ------------------------------------------------------------------------------------------------
     |  Relationships
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();

    /**
     * Role belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions();
}
