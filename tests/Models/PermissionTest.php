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
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanesoft\Contracts\Auth\Models\Permission */
    protected $permission;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
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
        $this->permission = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $adminRole */
        $adminRole     = Role::create([
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
        $this->permission = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $adminRole */
        $adminRole     = Role::create([
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

        $failedRoles  = [];
        $this->assertFalse($this->permission->isOne(['admin', 'member'], $failedRoles));
        $this->assertCount(2, $failedRoles);
        $this->assertSame(['admin', 'member'], $failedRoles);

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $adminRole */
        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $this->permission->attachRole($adminRole);

        $failedRoles = [];
        $this->assertTrue($this->permission->isOne(['admin', 'member'], $failedRoles));
        $this->assertCount(1, $failedRoles);
        $this->assertSame(['member'], $failedRoles);
    }

    /** @test */
    public function it_can_check_has_all_roles()
    {
        $this->permission = Permission::create([
            'name'        => 'Custom permission',
            'slug'        => 'permissions.custom',
            'description' => 'Custom permission description.',
        ]);

        $failedRoles  = [];
        $this->assertFalse($this->permission->isAll(['admin', 'member'], $failedRoles));
        $this->assertCount(2, $failedRoles);
        $this->assertSame(['admin', 'member'], $failedRoles);

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $adminRole */
        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $this->permission->attachRole($adminRole);

        $failedRoles = [];
        $this->assertFalse($this->permission->isAll(['admin', 'member'], $failedRoles));
        $this->assertCount(1, $failedRoles);
        $this->assertSame(['member'], $failedRoles);

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $memberRole */
        $memberRole = Role::create([
            'name'        => 'Member',
            'slug'        => 'member',
            'description' => 'Member role descriptions.',
        ]);

        $this->permission->attachRole($memberRole);

        $failedRoles = [];
        $this->assertTrue($this->permission->isAll(['admin', 'member'], $failedRoles));
        $this->assertEmpty($failedRoles);
    }
}
