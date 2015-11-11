<?php namespace Arcanedev\LaravelAuth\Traits;

use Arcanedev\LaravelAuth\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Trait     AuthUserRelationships
 *
 * @package  Arcanedev\LaravelAuth\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @method    BelongsToMany  belongsToMany(string $related, string $table = null, string $foreignKey = null, string $otherKey = null, string $relation = null)
 */
trait AuthUserRelationships
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
    public function roles()
    {
        $model = config('laravel-auth.roles.model', Role::class);

        return $this->belongsToMany($model, 'role_user', 'user_id', 'role_id')->withTimestamps();
    }
}
