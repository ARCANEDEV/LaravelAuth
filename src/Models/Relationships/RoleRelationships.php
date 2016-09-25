<?php namespace Arcanedev\LaravelAuth\Models\Relationships;

use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\User;

/**
 * Trait     RoleRelationships
 *
 * @package  Arcanedev\LaravelAuth\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @method  \Illuminate\Database\Eloquent\Relations\BelongsToMany  belongsToMany(string $related, string $table = null, string $foreignKey = null, string $otherKey = null, string $relation = null)
 */
trait RoleRelationships
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
    public function users()
    {
        $model = config('laravel-auth.users.model', User::class);

        return $this->belongsToMany($model, 'role_user', 'role_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Role belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        $model = config('laravel-auth.permissions.model', Permission::class);

        return $this->belongsToMany($model, 'permission_role', 'role_id', 'permission_id')
                    ->withTimestamps();
    }
}
