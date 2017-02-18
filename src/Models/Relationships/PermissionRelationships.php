<?php namespace Arcanedev\LaravelAuth\Models\Relationships;

use Arcanedev\LaravelAuth\Models\Role;

/**
 * Class     PermissionRelationships
 *
 * @package  Arcanedev\LaravelAuth\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @method  \Illuminate\Database\Eloquent\Relations\BelongsToMany  belongsToMany(string $related, string $table = null, string $foreignKey = null, string $otherKey = null, string $relation = null)
 */
trait PermissionRelationships
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
    public function roles()
    {
        return $this
            ->belongsToMany(
                config('laravel-auth.roles.model', Role::class),
                config('laravel-auth.database.prefix').'permission_role',
                'permission_id',
                'role_id'
            )
            ->withTimestamps();
    }
}
