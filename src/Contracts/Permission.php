<?php namespace Arcanedev\LaravelAuth\Contracts;

/**
 * Interface  Permission
 *
 * @package   Arcanedev\LaravelAuth\Contracts
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                                       id
 * @property  string                                    name
 * @property  string                                    slug
 * @property  string                                    description
 * @property  string                                    model
 * @property  \Carbon\Carbon                            created_at
 * @property  \Carbon\Carbon                            updated_at
 * @property  \Illuminate\Database\Eloquent\Collection  roles
 */
interface Permission
{
    /* ------------------------------------------------------------------------------------------------
     |  Relationships
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Permission belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles();
}
