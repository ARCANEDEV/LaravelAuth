<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class     PermissionTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PermissionTest extends ModelsTest
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var Permission */
    protected $permissionModel;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $this->migrate();

        $this->permissionModel = new Permission;
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($this->permissionModel);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
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
            $this->assertInstanceOf($expected, $this->permissionModel);
        }
    }

    /** @test */
    public function it_has_relationships()
    {
        $groupRelationship = $this->permissionModel->group();
        $rolesRelationship = $this->permissionModel->roles();

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
        $attributes = [
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ];

        $this->permissionModel->create($attributes);

        $this->seeInPrefixedDatabase('permissions', $attributes);
    }

    /** @test */
    public function it_can_update()
    {
        $attributes = [
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ];

        $permission        = $this->permissionModel->create($attributes);
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
        $attributes = [
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ];

        $permission = $this->permissionModel->create($attributes);

        $this->seeInPrefixedDatabase('permissions', $attributes);

        $permission->delete();

        $this->dontSeeInPrefixedDatabase('permissions', $attributes);
    }

    /** @test */
    public function it_can_attach_and_detach_roles()
    {
        $permission    = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        $adminRole     = Role::create([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);
        $moderatorRole = Role::create([
            'name'        => 'Moderator',
            'description' => 'Moderator role descriptions.',
        ]);

        $this->assertCount(0, $permission->roles);

        $permission->attachRole($adminRole);

        $this->assertCount(1, $permission->roles);
        $this->assertTrue($permission->hasRole($adminRole));

        $permission->attachRole($moderatorRole);

        $this->assertCount(2, $permission->roles);
        $this->assertTrue($permission->hasRole($adminRole));
        $this->assertTrue($permission->hasRole($moderatorRole));

        $permission->detachRole($adminRole);

        $this->assertCount(1, $permission->roles);
        $this->assertFalse($permission->hasRole($adminRole));
        $this->assertTrue($permission->hasRole($moderatorRole));

        $permission->detachRole($moderatorRole);

        $this->assertCount(0, $permission->roles);
        $this->assertFalse($permission->hasRole($adminRole));
        $this->assertFalse($permission->hasRole($moderatorRole));
    }

    /** @test */
    public function it_can_prevent_attaching_a_duplicated_role()
    {
        $permission    = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);
        $adminRole     = Role::create([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);

        $this->assertCount(0, $permission->roles);

        for ($i = 0; $i < 5; $i++) {
            $permission->attachRole($adminRole);
            $this->assertCount(1, $permission->roles);
            $this->assertTrue($permission->hasRole($adminRole));
        }
    }

    /** @test */
    public function it_can_sync_roles_by_its_slugs()
    {
        $permission = Permission::create([
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

        $this->assertCount(0, $permission->roles);

        $synced = $permission->syncRoles(
            $roles->pluck('slug')->toArray()
        );

        $this->assertCount($roles->count(),               $synced['attached']);
        $this->assertSame($roles->pluck('id')->toArray(), $synced['attached']);
        $this->assertEmpty($synced['detached']);
        $this->assertEmpty($synced['updated']);
    }

    /** @test */
    public function it_can_detach_all_roles()
    {
        $permission    = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        $adminRole     = Role::create([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);
        $moderatorRole = Role::create([
            'name'        => 'Moderator',
            'description' => 'Moderator role descriptions.',
        ]);

        $this->assertCount(0, $permission->roles);

        $permission->attachRole($adminRole);

        $this->assertCount(1, $permission->roles);
        $this->assertTrue($permission->hasRole($adminRole));

        $permission->attachRole($moderatorRole);

        $this->assertCount(2, $permission->roles);
        $this->assertTrue($permission->hasRole($adminRole));
        $this->assertTrue($permission->hasRole($moderatorRole));

        $permission->detachAllRoles();

        $this->assertCount(0, $permission->roles);
        $this->assertFalse($permission->hasRole($adminRole));
        $this->assertFalse($permission->hasRole($moderatorRole));
    }

    /** @test */
    public function it_can_check_has_same_role()
    {
        $permission    = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        $this->assertFalse($permission->hasRoleSlug('admin'));

        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $permission->attachRole($adminRole);

        $this->assertTrue($permission->hasRoleSlug('Admin'));
        $this->assertTrue($permission->hasRoleSlug('admin'));
    }

    /** @test */
    public function it_can_check_has_any_role()
    {
        $permission    = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        $failedRoles  = [];
        $this->assertFalse($permission->isOne(['admin', 'member'], $failedRoles));
        $this->assertCount(2, $failedRoles);
        $this->assertEquals(['admin', 'member'], $failedRoles);

        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $permission->attachRole($adminRole);

        $failedRoles = [];
        $this->assertTrue($permission->isOne(['admin', 'member'], $failedRoles));
        $this->assertCount(1, $failedRoles);
        $this->assertEquals(['member'], $failedRoles);
    }

    /** @test */
    public function it_can_check_has_all_roles()
    {
        $permission    = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        $failedRoles  = [];
        $this->assertFalse($permission->isAll(['admin', 'member'], $failedRoles));
        $this->assertCount(2, $failedRoles);
        $this->assertEquals(['admin', 'member'], $failedRoles);

        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $permission->attachRole($adminRole);

        $failedRoles = [];
        $this->assertFalse($permission->isAll(['admin', 'member'], $failedRoles));
        $this->assertCount(1, $failedRoles);
        $this->assertEquals(['member'], $failedRoles);

        $memberRole = Role::create([
            'name'        => 'Member',
            'slug'        => 'member',
            'description' => 'Member role descriptions.',
        ]);

        $permission->attachRole($memberRole);

        $failedRoles = [];
        $this->assertTrue($permission->isAll(['admin', 'member'], $failedRoles));
        $this->assertEmpty($failedRoles);
    }
}
