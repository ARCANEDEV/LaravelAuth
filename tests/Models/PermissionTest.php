<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Arcanedev\LaravelAuth\Events\Permissions as PermissionEvents;

/**
 * Class     PermissionTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PermissionTest extends ModelsTest
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\Permission */
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
     |  Setup Methods
     | -----------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $this->permission = new Permission;
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($this->permission);
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
            $this->assertInstanceOf($expected, $this->permission);
        }
    }

    /** @test */
    public function it_has_relationships()
    {
        $groupRelationship = $this->permission->group();
        $rolesRelationship = $this->permission->roles();

        $this->assertInstanceOf(BelongsTo::class,     $groupRelationship);
        $this->assertInstanceOf(BelongsToMany::class, $rolesRelationship);

        $this->assertInstanceOf(
            \Arcanedev\LaravelAuth\Models\PermissionsGroup::class,
            $groupRelationship->getRelated()
        );
        $this->assertInstanceOf(
            \Arcanedev\LaravelAuth\Models\Role::class,
            $rolesRelationship->getRelated()
        );
    }

    /** @test */
    public function it_can_create()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
        ]);

        $attributes = [
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ];

        $this->permission->create($attributes);

        $this->seeInPrefixedDatabase('permissions', $attributes);
    }

    /** @test */
    public function it_can_update()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved', 'updating', 'updated'
        ]);

        $attributes = [
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ];

        $permission        = $this->permission->create($attributes);
        $updatedAttributes = [
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Allow to update users',
        ];

        $this->seeInPrefixedDatabase('permissions', $attributes);

        $permission->update($updatedAttributes);

        $this->seeInPrefixedDatabase('permissions',     $updatedAttributes);
        $this->dontSeeInPrefixedDatabase('permissions', $attributes);
    }

    /** @test */
    public function it_can_delete()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved', 'deleting', 'deleted'
        ]);

        $attributes = [
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ];

        $permission = $this->permission->create($attributes);

        $this->seeInPrefixedDatabase('permissions', $attributes);

        $permission->delete();

        $this->dontSeeInPrefixedDatabase('permissions', $attributes);
    }

    /** @test */
    public function it_can_attach_and_detach_roles()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-role', 'attached-role', 'detaching-role', 'detached-role',
        ]);

        $this->permission = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $adminRole */
        $adminRole = Role::create([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $moderatorRole */
        $moderatorRole = Role::create([
            'name'        => 'Moderator',
            'description' => 'Moderator role descriptions.',
        ]);

        $this->assertCount(0, $this->permission->roles);

        $this->permission->attachRole($adminRole);

        $this->assertCount(1, $this->permission->roles);
        $this->assertTrue($this->permission->hasRole($adminRole));

        $this->permission->attachRole($moderatorRole);

        $this->assertCount(2, $this->permission->roles);
        $this->assertTrue($this->permission->hasRole($adminRole));
        $this->assertTrue($this->permission->hasRole($moderatorRole));

        $this->permission->detachRole($adminRole);

        $this->assertCount(1, $this->permission->roles);
        $this->assertFalse($this->permission->hasRole($adminRole));
        $this->assertTrue($this->permission->hasRole($moderatorRole));

        $this->permission->detachRole($moderatorRole);

        $this->assertCount(0, $this->permission->roles);
        $this->assertFalse($this->permission->hasRole($adminRole));
        $this->assertFalse($this->permission->hasRole($moderatorRole));
    }

    /** @test */
    public function it_can_prevent_attaching_a_duplicated_role()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved', 'attaching-role', 'attached-role',
        ]);

        $this->permission = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $adminRole */
        $adminRole = Role::create([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);

        $this->assertCount(0, $this->permission->roles);

        for ($i = 0; $i < 5; $i++) {
            $this->permission->attachRole($adminRole);
            $this->assertCount(1, $this->permission->roles);
            $this->assertTrue($this->permission->hasRole($adminRole));
        }
    }

    /** @test */
    public function it_can_sync_roles_by_its_slugs()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved', 'syncing-roles', 'synced-roles',
        ]);

        $this->permission = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        $roles = collect([
            Role::create([
                'name'        => 'Admin',
                'description' => 'Admin role descriptions.',
            ]),
            Role::create([
                'name'        => 'Moderator',
                'description' => 'Moderator role descriptions.',
            ])
        ]);

        $this->assertCount(0, $this->permission->roles);

        $synced = $this->permission->syncRoles(
            $roles->pluck('slug')
        );

        $this->assertCount($roles->count(),               $synced['attached']);
        $this->assertSame($roles->pluck('id')->toArray(), $synced['attached']);
        $this->assertEmpty($synced['detached']);
        $this->assertEmpty($synced['updated']);
    }

    /** @test */
    public function it_can_detach_all_roles()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-role', 'attached-role', 'detaching-roles', 'detached-roles'
        ]);

        $this->permission = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $adminRole */
        $adminRole = Role::create([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);
        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $moderatorRole */
        $moderatorRole = Role::create([
            'name'        => 'Moderator',
            'description' => 'Moderator role descriptions.',
        ]);

        $this->assertCount(0, $this->permission->roles);

        $this->permission->attachRole($adminRole);

        $this->assertCount(1, $this->permission->roles);
        $this->assertTrue($this->permission->hasRole($adminRole));

        $this->permission->attachRole($moderatorRole);

        $this->assertCount(2, $this->permission->roles);
        $this->assertTrue($this->permission->hasRole($adminRole));
        $this->assertTrue($this->permission->hasRole($moderatorRole));

        $this->permission->detachAllRoles();

        $this->assertCount(0, $this->permission->roles);
        $this->assertFalse($this->permission->hasRole($adminRole));
        $this->assertFalse($this->permission->hasRole($moderatorRole));
    }

    /** @test */
    public function it_can_check_has_same_role()
    {
        $this->permission = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        $this->assertFalse($this->permission->hasRoleSlug('admin'));

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $adminRole */
        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $this->permission->attachRole($adminRole);

        $this->assertTrue($this->permission->hasRoleSlug('Admin'));
        $this->assertTrue($this->permission->hasRoleSlug('admin'));
    }

    /** @test */
    public function it_can_check_has_any_role()
    {
        $this->permission = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        /** @var  \Illuminate\Support\Collection  $failedRoles */
        $this->assertFalse($this->permission->isOne(['admin', 'member'], $failedRoles));
        $this->assertCount(2, $failedRoles);
        $this->assertSame(['admin', 'member'], $failedRoles->all());

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $adminRole */
        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $this->permission->attachRole($adminRole);

        $this->assertTrue($this->permission->isOne(['admin', 'member'], $failedRoles));
        $this->assertCount(1, $failedRoles);
        $this->assertSame(['member'], $failedRoles->all());
    }

    /** @test */
    public function it_can_check_has_all_roles()
    {
        $this->permission = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        /** @var  \Illuminate\Support\Collection  $failedRoles */
        $this->assertFalse($this->permission->isAll(['admin', 'member'], $failedRoles));
        $this->assertCount(2, $failedRoles);
        $this->assertSame(['admin', 'member'], $failedRoles->all());

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $adminRole */
        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $this->permission->attachRole($adminRole);

        $this->assertFalse($this->permission->isAll(['admin', 'member'], $failedRoles));
        $this->assertCount(1, $failedRoles);
        $this->assertSame(['member'], $failedRoles->all());

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $memberRole */
        $memberRole = Role::create([
            'name'        => 'Member',
            'slug'        => 'member',
            'description' => 'Member role descriptions.',
        ]);

        $this->permission->attachRole($memberRole);

        $this->assertTrue($this->permission->isAll(['admin', 'member'], $failedRoles));
        $this->assertEmpty($failedRoles->all());
    }
}
