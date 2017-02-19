<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\PermissionsGroup;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Arcanedev\LaravelAuth\Events\PermissionsGroups as PermissionsGroupEvents;

/**
 * Class     PermissionsGroupTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PermissionsGroupTest extends ModelsTest
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanedev\LaravelAuth\Models\PermissionsGroup */
    protected $groupModel;

    /** @var  array */
    protected $modelEvents = [
        // Laravel Events
        'creating' => PermissionsGroupEvents\CreatingPermissionsGroup::class,
        'created'  => PermissionsGroupEvents\CreatedPermissionsGroup::class,
        'saving'   => PermissionsGroupEvents\SavingPermissionsGroup::class,
        'saved'    => PermissionsGroupEvents\SavedPermissionsGroup::class,
        'updating' => PermissionsGroupEvents\UpdatingPermissionsGroup::class,
        'updated'  => PermissionsGroupEvents\UpdatedPermissionsGroup::class,
        'deleting' => PermissionsGroupEvents\DeletingPermissionsGroup::class,
        'deleted'  => PermissionsGroupEvents\DeletedPermissionsGroup::class,

        // Custom events
        'creating-permission'       => PermissionsGroupEvents\CreatingPermission::class,
        'created-permission'        => PermissionsGroupEvents\CreatedPermission::class,
        'attaching-permission'      => PermissionsGroupEvents\AttachingPermissionToGroup::class,
        'attached-permission'       => PermissionsGroupEvents\AttachedPermissionToGroup::class,
        'attaching-permissions'     => PermissionsGroupEvents\AttachingPermissionsToGroup::class,
        'attached-permissions'      => PermissionsGroupEvents\AttachedPermissionsToGroup::class,
        'detaching-permission'      => PermissionsGroupEvents\DetachingPermissionFromGroup::class,
        'detached-permission'       => PermissionsGroupEvents\DetachedPermissionFromGroup::class,
        'detaching-permissions'     => PermissionsGroupEvents\DetachingPermissionsFromGroup::class,
        'detached-permissions'      => PermissionsGroupEvents\DetachedPermissionsFromGroup::class,
        'detaching-all-permissions' => PermissionsGroupEvents\DetachingAllPermissionsFromGroup::class,
        'detached-all-permissions'  => PermissionsGroupEvents\DetachedAllPermissionsFromGroup::class,
    ];

    /* -----------------------------------------------------------------
     |  Setup Methods
     | -----------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $this->groupModel = new PermissionsGroup;
    }

    public function tearDown()
    {
        unset($this->groupModel);

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
            // \Arcanesoft\Contracts\Auth\Models\PermissionsGroup::class,
            \Arcanedev\LaravelAuth\Models\PermissionsGroup::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->groupModel);
        }
    }

    /** @test */
    public function it_has_relationships()
    {
        $permissionsRelationship = $this->groupModel->permissions();

        $this->assertInstanceOf(HasMany::class, $permissionsRelationship);

        /** @var  Permission  $permission */
        $permission = $permissionsRelationship->getRelated();

        $this->assertInstanceOf(Permission::class, $permission);
    }

    /** @test */
    public function it_can_create()
    {
        $this->checkFiredEvents(['creating', 'created', 'saving', 'saved']);

        $group = $this->createGroup(
            $attributes = $this->getAuthGroupAttributes()
        );

        $this->seeInPrefixedDatabase('permissions_groups', $attributes);
        $this->assertCount(0, $group->permissions);
    }

    /** @test */
    public function it_can_update()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved', 'updating', 'updated',
        ]);

        $attributes = [
            'name'        => 'Custom group',
            'description' => 'Custom group description',
        ];

        $group = $this->createGroup($attributes);

        $this->seeInPrefixedDatabase('permissions_groups', $attributes);

        $attributes = [
            'name'        => 'Super Custom Group',
            'slug'        => 'super-custom-group',
            'description' => 'Super Custom Group description',
        ];

        $group->update($attributes);

        $this->seeInPrefixedDatabase('permissions_groups', $attributes);
    }

    /** @test */
    public function it_can_create_permission()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'creating-permission', 'created-permission',
        ]);

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $this->assertCount(0, $group->permissions);

        $group->createPermission([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $this->assertCount(1, $group->permissions);

        $group->createPermission([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Allow to update users',
        ]);

        $this->assertCount(2, $group->permissions);
    }

    /**
     * @test
     *
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_throw_an_exception_on_duplicated_permissions()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'creating-permission', 'created-permission',
        ]);

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        for ($i = 0; $i < 2; $i ++) {
            $group->createPermission([
                'name'        => 'Create users',
                'slug'        => 'auth.users.create',
                'description' => 'Allow to create users',
            ]);
        }
    }

    /** @test */
    public function it_can_attach_permission()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-permission', 'attached-permission',
        ]);

        $permission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $this->assertFalse($group->hasPermission($permission));
        $this->assertCount(0, $group->permissions);

        $group->attachPermission($permission);

        $this->assertTrue($group->hasPermission($permission));
        $this->assertCount(1, $group->permissions);
        $this->assertEquals($group->id, $permission->group_id);

        // Make sure that attach only once :
        $group->attachPermission($permission);

        $this->assertTrue($group->hasPermission($permission));
        $this->assertCount(1, $group->permissions);
        $this->assertEquals($group->id, $permission->group_id);
    }

    /** @test */
    public function it_can_attach_permission_by_id()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-permission', 'attached-permission',
        ]);

        $permission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $group      = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $this->assertFalse($group->hasPermission($permission));
        $this->assertCount(0, $group->permissions);

        $permission = $group->attachPermissionById($permission->id);

        $this->assertTrue($group->hasPermission($permission));
        $this->assertCount(1, $group->permissions);
        $this->assertEquals($group->id, $permission->group_id);
    }

    /** @test */
    public function it_can_attach_many_permissions()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-permissions', 'attached-permissions',
        ]);

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $permissions = [
            Permission::create([
                'name'        => 'Create users',
                'slug'        => 'auth.users.create',
                'description' => 'Allow to create users',
            ]),
            Permission::create([
                'name'        => 'Update users',
                'slug'        => 'auth.users.update',
                'description' => 'Allow to update users',
            ])
        ];

        $this->assertCount(0, $group->permissions);

        $group->attachPermissions($permissions);

        $this->assertCount(2, $group->permissions);
        foreach ($permissions as $permission) {
            $this->assertTrue($group->hasPermission($permission));
        }
    }

    /** @test */
    public function it_can_detach_permission()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-permission', 'attached-permission', 'detaching-permission', 'detached-permission',
        ]);

        $permission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $group      = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $this->assertFalse($group->hasPermission($permission));
        $this->assertCount(0, $group->permissions);

        $group->attachPermission($permission);

        $this->assertTrue($group->hasPermission($permission));
        $this->assertCount(1, $group->permissions);
        $this->assertEquals($group->id, $permission->group_id);

        $group->detachPermission($permission);

        $this->assertFalse($group->hasPermission($permission));
        $this->assertCount(0, $group->permissions);
        $this->assertEquals(0, $permission->group_id);

        // Make sure it can not detach this
        $group->detachPermission($permission);

        $this->assertFalse($group->hasPermission($permission));
        $this->assertCount(0, $group->permissions);
        $this->assertEquals(0, $permission->group_id);
    }

    /** @test */
    public function it_can_detach_permission_by_id()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-permission', 'attached-permission', 'detaching-permission', 'detached-permission',
        ]);

        $permission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $group      = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $this->assertFalse($group->hasPermission($permission));
        $this->assertCount(0, $group->permissions);

        $group->attachPermission($permission);

        $this->assertTrue($group->hasPermission($permission));
        $this->assertCount(1, $group->permissions);
        $this->assertEquals($group->id, $permission->group_id);

        $permission = $group->detachPermissionById($permission->id);

        $this->assertFalse($group->hasPermission($permission));
        $this->assertCount(0, $group->permissions);
        $this->assertEquals(0, $permission->group_id);

        // Make sure it can not detach this
        $permission = $group->detachPermissionById($permission->id);

        $this->assertFalse($group->hasPermission($permission));
        $this->assertCount(0, $group->permissions);
        $this->assertEquals(0, $permission->group_id);
    }

    /** @test */
    public function it_can_detach_permissions_by_ids()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-permissions', 'attached-permissions', 'detaching-permissions', 'detached-permissions',
        ]);

        $group      = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $create = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $update = Permission::create([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Allow to update users',
        ]);

        $delete = Permission::create([
            'name'        => 'Delete users',
            'slug'        => 'auth.users.delete',
            'description' => 'Allow to delete users',
        ]);

        $group->attachPermissions(compact('create', 'update', 'delete'));

        $this->assertCount(3, $group->permissions);

        $group->detachPermissions([$create->id, $delete->id]);

        $this->assertCount(1, $group->permissions);

        $expected = $group->permissions->first();
        $this->assertEquals($expected->slug, $update->slug);
    }

    /** @test */
    public function it_can_detach_all_permissions()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-permissions', 'attached-permissions',
            'detaching-all-permissions', 'detached-all-permissions',
        ]);

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $permissions = [
            Permission::create([
                'name'        => 'Create users',
                'slug'        => 'auth.users.create',
                'description' => 'Allow to create users',
            ]),
            Permission::create([
                'name'        => 'Update users',
                'slug'        => 'auth.users.update',
                'description' => 'Allow to update users',
            ])
        ];

        $this->assertCount(0, $group->permissions);

        $group->attachPermissions($permissions);

        $this->assertCount(2, $group->permissions);
        foreach ($permissions as $permission) {
            $this->assertTrue($group->hasPermission($permission));
        }

        $group->detachAllPermissions();

        $this->assertCount(0, $group->permissions);
    }

    /** @test */
    public function it_can_attach_and_detach_permission_from_group_to_group()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-permission', 'attached-permission', 'detaching-permission', 'detached-permission',
        ]);

        $permission = Permission::create([
            'name'        => 'Random permission',
            'slug'        => 'random.permission',
            'description' => 'Random permission description',
        ]);

        $authGroup  = $this->createGroup(
            $this->getAuthGroupAttributes()
        );
        $blogGroup  = $this->createGroup(
            $this->getBlogGroupAttributes()
        );

        $this->assertFalse($authGroup->hasPermission($permission));
        $this->assertCount(0, $authGroup->permissions);
        $this->assertFalse($blogGroup->hasPermission($permission));
        $this->assertCount(0, $blogGroup->permissions);

        $authGroup->attachPermission($permission);

        $this->assertTrue($authGroup->hasPermission($permission));
        $this->assertCount(1, $authGroup->permissions);
        $this->assertFalse($blogGroup->hasPermission($permission));
        $this->assertCount(0, $blogGroup->permissions);
        $this->assertEquals($authGroup->id, $permission->group_id);

        $blogGroup->attachPermission($permission);

        $this->assertFalse($authGroup->hasPermission($permission));
        $this->assertCount(0, $authGroup->permissions);
        $this->assertTrue($blogGroup->hasPermission($permission));
        $this->assertCount(1, $blogGroup->permissions);
        $this->assertEquals($blogGroup->id, $permission->group_id);

        $blogGroup->detachPermission($permission);

        $this->assertFalse($authGroup->hasPermission($permission));
        $this->assertCount(0, $authGroup->permissions);
        $this->assertFalse($blogGroup->hasPermission($permission));
        $this->assertCount(0, $blogGroup->permissions);
        $this->assertEquals(0, $permission->group_id);
    }

    /** @test */
    public function it_can_delete_a_group()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved', 'deleting', 'deleted',
            'attaching-permission', 'attached-permission',
            'detaching-all-permissions', 'detached-all-permissions',
        ]);

        $permission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $group->attachPermission($permission);

        $this->assertTrue($group->hasPermission($permission));
        $this->assertCount(1, $group->permissions);
        $this->assertEquals($group->id, $permission->group_id);

        $group->delete();

        /** @var  Permission  $permission */
        $permission = Permission::where('id', $permission->id)->first();

        $this->assertEquals(0, $permission->group_id);
    }

    /* -----------------------------------------------------------------
     |  Helpers
     | -----------------------------------------------------------------
     */

    /**
     * Get auth group attributes.
     *
     * @return array
     */
    private function getAuthGroupAttributes()
    {
        return [
            'name'        => 'Auth Group',
            'slug'        => Str::slug('Auth Group', '-'),
            'description' => 'Auth Permissions Group description.',
        ];
    }

    /**
     * Get auth group attributes.
     *
     * @return array
     */
    private function getBlogGroupAttributes()
    {
        return [
            'name'        => 'Blog Group',
            'slug'        => Str::slug('Blog Group', '-'),
            'description' => 'Blog Permissions Group description.',
        ];
    }

    /**
     * Create a permissions group.
     *
     * @param  array  $attributes
     *
     * @return PermissionsGroup
     */
    private function createGroup(array $attributes)
    {
        $group = $this->groupModel->create($attributes);

        return $this->groupModel->find($group->id);
    }
}
