<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Events\PermissionsGroups\AttachedPermissionsToGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\AttachedPermissionToGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\AttachingPermissionsToGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\AttachingPermissionToGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\CreatedPermissionsGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\CreatingPermissionsGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\DeletedPermissionsGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\DeletingPermissionsGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\DetachedAllPermissions;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\DetachedPermissionFromGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\DetachedPermissionsFromGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\DetachingAllPermissions;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\DetachingPermissionFromGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\DetachingPermissionsFromGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\SavedPermissionsGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\SavingPermissionsGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\UpdatedPermissionsGroup;
use Arcanedev\LaravelAuth\Events\PermissionsGroups\UpdatingPermissionsGroup;
use Arcanedev\LaravelAuth\Listeners\PermissionGroups\DetachingPermissions;
use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\PermissionsGroup;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

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
        'creating' => CreatingPermissionsGroup::class,
        'created'  => CreatedPermissionsGroup::class,
        'saving'   => SavingPermissionsGroup::class,
        'saved'    => SavedPermissionsGroup::class,
        'updating' => UpdatingPermissionsGroup::class,
        'updated'  => UpdatedPermissionsGroup::class,
        'deleting' => DeletingPermissionsGroup::class,
        'deleted'  => DeletedPermissionsGroup::class,

        // Custom events
        'creating-permission'       => CreatingPermissionsGroup::class,
        'created-permission'        => CreatedPermissionsGroup::class,
        'attaching-permission'      => AttachingPermissionToGroup::class,
        'attached-permission'       => AttachedPermissionToGroup::class,
        'attaching-permissions'     => AttachingPermissionsToGroup::class,
        'attached-permissions'      => AttachedPermissionsToGroup::class,
        'detaching-permission'      => DetachingPermissionFromGroup::class,
        'detached-permission'       => DetachedPermissionFromGroup::class,
        'detaching-permissions'     => DetachingPermissionsFromGroup::class,
        'detached-permissions'      => DetachedPermissionsFromGroup::class,
        'detaching-all-permissions' => DetachingAllPermissions::class,
        'detached-all-permissions'  => DetachedAllPermissions::class,
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
        Event::fake();

        $group = $this->createGroup(
            $attributes = $this->getAuthGroupAttributes()
        );

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        $this->seeInPrefixedDatabase('permissions_groups', $attributes);
        $this->assertCount(0, $group->permissions);
    }

    /** @test */
    public function it_can_update()
    {
        Event::fake();

        $group = $this->createGroup($attributes = [
            'name'        => 'Custom group',
            'description' => 'Custom group description',
        ]);

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        $this->seeInPrefixedDatabase('permissions_groups', $attributes);

        $group->update($updatedAttributes = [
            'name'        => 'Super Custom Group',
            'slug'        => 'super-custom-group',
            'description' => 'Super Custom Group description',
        ]);

        $this->assertFiredEvents(['saving', 'saved', 'updating', 'updated',]);
        $this->seeInPrefixedDatabase('permissions_groups', $updatedAttributes);
        $this->dontSeeInPrefixedDatabase('permissions_groups', $attributes);
    }

    /** @test */
    public function it_can_create_permission()
    {
        Event::fake();

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        $this->assertCount(0, $group->permissions);

        $group->createPermission([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $this->assertFiredEvents(['creating-permission', 'created-permission']);
        $this->assertCount(1, $group->permissions);

        $group->createPermission([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Allow to update users',
        ]);

        $this->assertFiredEvents(['creating-permission', 'created-permission']);
        $this->assertCount(2, $group->permissions);
    }

    /**
     * @test
     *
     * @expectedException \Illuminate\Database\QueryException
     */
    public function it_must_throw_an_exception_on_duplicated_permissions()
    {
        Event::fake();

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
        Event::fake();

        $permission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $this->assertFalse($group->hasPermission($permission));
        $this->assertCount(0, $group->permissions);

        $group->attachPermission($permission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission',]);

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
        Event::fake();

        $permission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        $this->assertFalse($group->hasPermission($permission));
        $this->assertCount(0, $group->permissions);

        $permission = $group->attachPermissionById($permission->id);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertTrue($group->hasPermission($permission));
        $this->assertCount(1, $group->permissions);
        $this->assertEquals($group->id, $permission->group_id);
    }

    /** @test */
    public function it_can_attach_many_permissions()
    {
        Event::fake();

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

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

        $this->assertFiredEvents(['attaching-permissions', 'attached-permissions']);
        $this->assertCount(2, $group->permissions);
        foreach ($permissions as $permission) {
            $this->assertTrue($group->hasPermission($permission));
        }
    }

    /** @test */
    public function it_can_detach_permission()
    {
        Event::fake();

        $permission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        $this->assertFalse($group->hasPermission($permission));
        $this->assertCount(0, $group->permissions);

        $group->attachPermission($permission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertTrue($group->hasPermission($permission));
        $this->assertCount(1, $group->permissions);
        $this->assertEquals($group->id, $permission->group_id);

        $group->detachPermission($permission);

        $this->assertFiredEvents(['detaching-permission', 'detached-permission']);
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
        Event::fake();

        $permission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        $this->assertFalse($group->hasPermission($permission));
        $this->assertCount(0, $group->permissions);

        $group->attachPermission($permission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertTrue($group->hasPermission($permission));
        $this->assertCount(1, $group->permissions);
        $this->assertEquals($group->id, $permission->group_id);

        $permission = $group->detachPermissionById($permission->id);

        $this->assertFiredEvents(['detaching-permission', 'detached-permission']);
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
        Event::fake();

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

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

        $this->assertFiredEvents(['attaching-permissions', 'attached-permissions']);
        $this->assertCount(3, $group->permissions);

        $group->detachPermissions([$create->id, $delete->id]);

        $this->assertFiredEvents(['detaching-permissions', 'detached-permissions']);
        $this->assertCount(1, $group->permissions);

        $expected = $group->permissions->first();
        $this->assertEquals($expected->slug, $update->slug);
    }

    /** @test */
    public function it_can_detach_all_permissions()
    {
        Event::fake();

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

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

        $this->assertFiredEvents(['attaching-permissions', 'attached-permissions']);
        $this->assertCount(2, $group->permissions);
        foreach ($permissions as $permission) {
            $this->assertTrue($group->hasPermission($permission));
        }

        $group->detachAllPermissions();

        $this->assertFiredEvents(['detaching-all-permissions', 'detached-all-permissions']);
        $this->assertCount(0, $group->permissions);
    }

    /** @test */
    public function it_can_attach_and_detach_permission_from_group_to_group()
    {
        Event::fake();

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

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        $this->assertFalse($authGroup->hasPermission($permission));
        $this->assertCount(0, $authGroup->permissions);
        $this->assertFalse($blogGroup->hasPermission($permission));
        $this->assertCount(0, $blogGroup->permissions);

        $authGroup->attachPermission($permission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertTrue($authGroup->hasPermission($permission));
        $this->assertCount(1, $authGroup->permissions);
        $this->assertFalse($blogGroup->hasPermission($permission));
        $this->assertCount(0, $blogGroup->permissions);
        $this->assertEquals($authGroup->id, $permission->group_id);

        $blogGroup->attachPermission($permission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertFalse($authGroup->hasPermission($permission));
        $this->assertCount(0, $authGroup->permissions);
        $this->assertTrue($blogGroup->hasPermission($permission));
        $this->assertCount(1, $blogGroup->permissions);
        $this->assertEquals($blogGroup->id, $permission->group_id);

        $blogGroup->detachPermission($permission);

        $this->assertFiredEvents(['detaching-permission', 'detached-permission']);
        $this->assertFalse($authGroup->hasPermission($permission));
        $this->assertCount(0, $authGroup->permissions);
        $this->assertFalse($blogGroup->hasPermission($permission));
        $this->assertCount(0, $blogGroup->permissions);
        $this->assertEquals(0, $permission->group_id);
    }

    /** @test */
    public function it_can_delete_a_group()
    {
        Event::fake();

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $permission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);
        $group->attachPermission($permission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertTrue($group->hasPermission($permission));
        $this->assertCount(1, $group->permissions);
        $this->assertEquals($group->id, $permission->group_id);

        $group->delete();

        Event::assertDispatched(DeletingPermissionsGroup::class, function ($e) {
            (new DetachingPermissions)->handle($e);

            Event::assertDispatched(DetachingAllPermissions::class);
            Event::assertDispatched(DetachedAllPermissions::class);

            return true;
        });

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
