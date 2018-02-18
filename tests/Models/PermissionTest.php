<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Events\Permissions as PermissionEvents;
use Arcanedev\LaravelAuth\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

/**
 * Class     PermissionTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @todo: Cleaning/Refactoring the event/listeners assertions.
 */
class PermissionTest extends ModelsTest
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelAuth\Models\Permission|\Arcanesoft\Contracts\Auth\Models\Permission */
    protected $permission;

    /** @var array */
    protected $modelEvents = [
        // Laravel Events
        'creating'        => PermissionEvents\CreatingPermission::class,
        'created'         => PermissionEvents\CreatedPermission::class,
        'saving'          => PermissionEvents\SavingPermission::class,
        'saved'           => PermissionEvents\SavedPermission::class,
        'updating'        => PermissionEvents\UpdatingPermission::class,
        'updated'         => PermissionEvents\UpdatedPermission::class,
        'deleting'        => PermissionEvents\DeletingPermission::class,
        'deleted'         => PermissionEvents\DeletedPermission::class,

        // Custom events
        'attaching-role'  => PermissionEvents\AttachingRoleToPermission::class,
        'attached-role'   => PermissionEvents\AttachedRoleToPermission::class,
        'syncing-roles'   => PermissionEvents\SyncingRolesWithPermission::class,
        'synced-roles'    => PermissionEvents\SyncedRolesWithPermission::class,
        'detaching-role'  => PermissionEvents\DetachingRoleFromPermission::class,
        'detached-role'   => PermissionEvents\DetachedRoleFromPermission::class,
        'detaching-roles' => PermissionEvents\DetachingAllRolesFromPermission::class,
        'detached-roles'  => PermissionEvents\DetachedAllRolesFromPermission::class,
    ];

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp()
    {
        parent::setUp();

        $this->permission = new Permission;
    }

    public function tearDown()
    {
        unset($this->permission);

        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated()
    {
        $expectations = [
            \Illuminate\Database\Eloquent\Model::class,
            \Arcanesoft\Contracts\Auth\Models\Permission::class,
            \Arcanedev\LaravelAuth\Models\Permission::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->permission);
        }
    }

    /** @test */
    public function it_has_relationships()
    {
        $groupRelationship = $this->permission->group();
        $rolesRelationship = $this->permission->roles();

        static::assertInstanceOf(BelongsTo::class,     $groupRelationship);
        static::assertInstanceOf(BelongsToMany::class, $rolesRelationship);

        static::assertInstanceOf(
            \Arcanedev\LaravelAuth\Models\PermissionsGroup::class,
            $groupRelationship->getRelated()
        );
        static::assertInstanceOf(
            \Arcanedev\LaravelAuth\Models\Role::class,
            $rolesRelationship->getRelated()
        );
    }

    /** @test */
    public function it_can_create()
    {
        Event::fake();

        $this->permission->newQuery()->create($attributes = [
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        $this->seeInPrefixedDatabase('permissions', $attributes);
    }

    /** @test */
    public function it_can_update()
    {
        Event::fake();

        $permission = $this->permission->newQuery()->create($attributes = [
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        $this->seeInPrefixedDatabase('permissions', $attributes);

        $permission->update($updatedAttributes = [
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Allow to update users',
        ]);

        static::assertFiredEvents(['saving', 'saved', 'updating', 'updated']);
        $this->seeInPrefixedDatabase('permissions',     $updatedAttributes);
        $this->dontSeeInPrefixedDatabase('permissions', $attributes);
    }

    /** @test */
    public function it_can_delete()
    {
        Event::fake();

        $attributes = [
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ];

        /** @var  \Arcanedev\LaravelAuth\Models\Permission  $permission */
        $permission = $this->permission->newQuery()->create($attributes);

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        $this->seeInPrefixedDatabase('permissions', $attributes);

        $permission->attachRole(
            static::createNewRole([
                'name'        => 'Admin',
                'description' => 'Admin role descriptions.',
            ])
        );

        $permission->delete();

        Event::assertDispatched(PermissionEvents\DeletingPermission::class, function (PermissionEvents\DeletingPermission $e) use ($permission) {
            static::assertSame(1, $e->permission->roles()->count());
            (new \Arcanedev\LaravelAuth\Listeners\Permissions\DetachingRoles)->handle($e);
            static::assertSame(0, $e->permission->roles()->count());

            return $e->permission->id === $permission->id;
        });
        static::assertFiredEvents(['deleted']);
        $this->dontSeeInPrefixedDatabase('permissions', $attributes);
    }

    /** @test */
    public function it_can_attach_and_detach_roles()
    {
        Event::fake();

        $this->permission = static::createNewPermission([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        $adminRole = static::createNewRole([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);

        $moderatorRole = static::createNewRole([
            'name'        => 'Moderator',
            'description' => 'Moderator role descriptions.',
        ]);

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        static::assertCount(0, $this->permission->roles);

        $this->permission->attachRole($adminRole);

        static::assertFiredEvents(['attaching-role', 'attached-role']);
        static::assertCount(1, $this->permission->roles);
        static::assertTrue($this->permission->hasRole($adminRole));

        $this->permission->attachRole($moderatorRole);

        static::assertFiredEvents(['attaching-role', 'attached-role']);
        static::assertCount(2, $this->permission->roles);
        static::assertTrue($this->permission->hasRole($adminRole));
        static::assertTrue($this->permission->hasRole($moderatorRole));

        $this->permission->detachRole($adminRole);

        static::assertFiredEvents(['detaching-role', 'detached-role']);
        static::assertCount(1, $this->permission->roles);
        static::assertFalse($this->permission->hasRole($adminRole));
        static::assertTrue($this->permission->hasRole($moderatorRole));

        $this->permission->detachRole($moderatorRole);

        static::assertFiredEvents(['detaching-role', 'detached-role']);
        static::assertCount(0, $this->permission->roles);
        static::assertFalse($this->permission->hasRole($adminRole));
        static::assertFalse($this->permission->hasRole($moderatorRole));
    }

    /** @test */
    public function it_can_prevent_attaching_a_duplicated_role()
    {
        Event::fake();

        $this->permission = static::createNewPermission([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $adminRole = static::createNewRole([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);

        static::assertCount(0, $this->permission->roles);

        for ($i = 0; $i < 5; $i++) {
            $this->permission->attachRole($adminRole);
            static::assertCount(1, $this->permission->roles);
            static::assertTrue($this->permission->hasRole($adminRole));
        }

        static::assertFiredEvents(['attaching-role', 'attached-role']);
    }

    /** @test */
    public function it_can_sync_roles_by_its_slugs()
    {
        Event::fake();

        $this->permission = static::createNewPermission([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $roles = new Collection([
            static::createNewRole([
                'name'        => 'Admin',
                'description' => 'Admin role descriptions.',
            ]),
            static::createNewRole([
                'name'        => 'Moderator',
                'description' => 'Moderator role descriptions.',
            ])
        ]);

        static::assertCount(0, $this->permission->roles);

        $synced = $this->permission->syncRoles(
            $roles->pluck('slug')
        );

        static::assertFiredEvents(['syncing-roles', 'synced-roles']);
        static::assertCount($roles->count(), $synced['attached']);
        static::assertSame($roles->pluck('id')->toArray(), $synced['attached']);
        static::assertEmpty($synced['detached']);
        static::assertEmpty($synced['updated']);
    }

    /** @test */
    public function it_can_detach_all_roles()
    {
        Event::fake();

        $this->permission = static::createNewPermission([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $adminRole = static::createNewRole([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);
        $moderatorRole = static::createNewRole([
            'name'        => 'Moderator',
            'description' => 'Moderator role descriptions.',
        ]);

        static::assertCount(0, $this->permission->roles);

        $this->permission->attachRole($adminRole);

        static::assertFiredEvents(['attaching-role', 'attached-role']);
        static::assertCount(1, $this->permission->roles);
        static::assertTrue($this->permission->hasRole($adminRole));

        $this->permission->attachRole($moderatorRole);

        static::assertFiredEvents(['attaching-role', 'attached-role']);
        static::assertCount(2, $this->permission->roles);
        static::assertTrue($this->permission->hasRole($adminRole));
        static::assertTrue($this->permission->hasRole($moderatorRole));

        $this->permission->detachAllRoles();

        static::assertFiredEvents(['detaching-roles', 'detached-roles']);
        static::assertCount(0, $this->permission->roles);
        static::assertFalse($this->permission->hasRole($adminRole));
        static::assertFalse($this->permission->hasRole($moderatorRole));
    }

    /** @test */
    public function it_can_check_has_same_role()
    {
        $this->permission = static::createNewPermission([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        static::assertFalse($this->permission->hasRoleSlug('admin'));

        $adminRole = static::createNewRole([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $this->permission->attachRole($adminRole);

        static::assertTrue($this->permission->hasRoleSlug('Admin'));
        static::assertTrue($this->permission->hasRoleSlug('admin'));
    }

    /** @test */
    public function it_can_check_has_any_role()
    {
        $this->permission = static::createNewPermission([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        /** @var  \Illuminate\Support\Collection  $failedRoles */
        static::assertFalse($this->permission->isOne(['admin', 'member'], $failedRoles));
        static::assertCount(2, $failedRoles);
        static::assertSame(['admin', 'member'], $failedRoles->all());

        $this->permission->attachRole(
            static::createNewRole([
                'name'        => 'Admin',
                'slug'        => 'admin',
                'description' => 'Admin role descriptions.',
            ])
        );

        static::assertTrue($this->permission->isOne(['admin', 'member'], $failedRoles));
        static::assertCount(1, $failedRoles);
        static::assertSame(['member'], $failedRoles->all());
    }

    /** @test */
    public function it_can_check_has_all_roles()
    {
        $this->permission = static::createNewPermission([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        /** @var  \Illuminate\Support\Collection  $failedRoles */
        static::assertFalse($this->permission->isAll(['admin', 'member'], $failedRoles));
        static::assertCount(2, $failedRoles);
        static::assertSame(['admin', 'member'], $failedRoles->all());

        $this->permission->attachRole(
            static::createNewRole([
                'name'        => 'Admin',
                'slug'        => 'admin',
                'description' => 'Admin role descriptions.',
            ])
        );

        static::assertFalse($this->permission->isAll(['admin', 'member'], $failedRoles));
        static::assertCount(1, $failedRoles);
        static::assertSame(['member'], $failedRoles->all());

        $this->permission->attachRole(
            static::createNewRole([
                'name'        => 'Member',
                'slug'        => 'member',
                'description' => 'Member role descriptions.',
            ])
        );

        static::assertTrue($this->permission->isAll(['admin', 'member'], $failedRoles));
        static::assertEmpty($failedRoles->all());
    }
}
