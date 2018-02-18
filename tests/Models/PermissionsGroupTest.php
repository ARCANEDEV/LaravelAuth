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
     |  Main Methods
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
            static::assertInstanceOf($expected, $this->groupModel);
        }
    }

    /** @test */
    public function it_has_relationships()
    {
        $permissionsRelationship = $this->groupModel->permissions();

        static::assertInstanceOf(HasMany::class, $permissionsRelationship);

        /** @var  Permission  $permission */
        $permission = $permissionsRelationship->getRelated();

        static::assertInstanceOf(Permission::class, $permission);
    }

    /** @test */
    public function it_can_create()
    {
        Event::fake();

        $group = $this->createGroup(
            $attributes = $this->getAuthGroupAttributes()
        );

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        $this->seeInPrefixedDatabase('permissions_groups', $attributes);
        static::assertCount(0, $group->permissions);
    }

    /** @test */
    public function it_can_update()
    {
        Event::fake();

        $group = $this->createGroup($attributes = [
            'name'        => 'Custom group',
            'description' => 'Custom group description',
        ]);

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        $this->seeInPrefixedDatabase('permissions_groups', $attributes);

        $group->update($updatedAttributes = [
            'name'        => 'Super Custom Group',
            'slug'        => 'super-custom-group',
            'description' => 'Super Custom Group description',
        ]);

        static::assertFiredEvents(['saving', 'saved', 'updating', 'updated',]);
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

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        static::assertCount(0, $group->permissions);

        $group->createPermission([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        static::assertFiredEvents(['creating-permission', 'created-permission']);
        static::assertCount(1, $group->permissions);

        $group->createPermission([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Allow to update users',
        ]);

        static::assertFiredEvents(['creating-permission', 'created-permission']);
        static::assertCount(2, $group->permissions);
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

        $permission = static::createNewPermission([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        static::assertFalse($group->hasPermission($permission));
        static::assertCount(0, $group->permissions);

        $group->attachPermission($permission);

        static::assertFiredEvents(['attaching-permission', 'attached-permission',]);

        static::assertTrue($group->hasPermission($permission));
        static::assertCount(1, $group->permissions);
        static::assertEquals($group->id, $permission->group_id);

        // Make sure that attach only once :
        $group->attachPermission($permission);

        static::assertTrue($group->hasPermission($permission));
        static::assertCount(1, $group->permissions);
        static::assertEquals($group->id, $permission->group_id);
    }

    /** @test */
    public function it_can_attach_permission_by_id()
    {
        Event::fake();

        $permission = static::createNewPermission([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        static::assertFalse($group->hasPermission($permission));
        static::assertCount(0, $group->permissions);

        $permission = $group->attachPermissionById($permission->id);

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertTrue($group->hasPermission($permission));
        static::assertCount(1, $group->permissions);
        static::assertEquals($group->id, $permission->group_id);
    }

    /** @test */
    public function it_can_attach_many_permissions()
    {
        Event::fake();

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $permissions = [
            static::createNewPermission([
                'name'        => 'Create users',
                'slug'        => 'auth.users.create',
                'description' => 'Allow to create users',
            ]),
            static::createNewPermission([
                'name'        => 'Update users',
                'slug'        => 'auth.users.update',
                'description' => 'Allow to update users',
            ])
        ];

        static::assertCount(0, $group->permissions);

        $group->attachPermissions($permissions);

        static::assertFiredEvents(['attaching-permissions', 'attached-permissions']);
        static::assertCount(2, $group->permissions);
        foreach ($permissions as $permission) {
            static::assertTrue($group->hasPermission($permission));
        }
    }

    /** @test */
    public function it_can_detach_permission()
    {
        Event::fake();

        $permission = static::createNewPermission([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        static::assertFalse($group->hasPermission($permission));
        static::assertCount(0, $group->permissions);

        $group->attachPermission($permission);

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertTrue($group->hasPermission($permission));
        static::assertCount(1, $group->permissions);
        static::assertEquals($group->id, $permission->group_id);

        $group->detachPermission($permission);

        static::assertFiredEvents(['detaching-permission', 'detached-permission']);
        static::assertFalse($group->hasPermission($permission));
        static::assertCount(0, $group->permissions);
        static::assertEquals(0, $permission->group_id);

        // Make sure it can not detach this
        $group->detachPermission($permission);

        static::assertFalse($group->hasPermission($permission));
        static::assertCount(0, $group->permissions);
        static::assertEquals(0, $permission->group_id);
    }

    /** @test */
    public function it_can_detach_permission_by_id()
    {
        Event::fake();

        $permission = static::createNewPermission([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        static::assertFalse($group->hasPermission($permission));
        static::assertCount(0, $group->permissions);

        $group->attachPermission($permission);

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertTrue($group->hasPermission($permission));
        static::assertCount(1, $group->permissions);
        static::assertEquals($group->id, $permission->group_id);

        $permission = $group->detachPermissionById($permission->id);

        static::assertFiredEvents(['detaching-permission', 'detached-permission']);
        static::assertFalse($group->hasPermission($permission));
        static::assertCount(0, $group->permissions);
        static::assertEquals(0, $permission->group_id);

        // Make sure it can not detach this
        $permission = $group->detachPermissionById($permission->id);

        static::assertFalse($group->hasPermission($permission));
        static::assertCount(0, $group->permissions);
        static::assertEquals(0, $permission->group_id);
    }

    /** @test */
    public function it_can_detach_permissions_by_ids()
    {
        Event::fake();

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $create = static::createNewPermission([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);

        $update = static::createNewPermission([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Allow to update users',
        ]);

        $delete = static::createNewPermission([
            'name'        => 'Delete users',
            'slug'        => 'auth.users.delete',
            'description' => 'Allow to delete users',
        ]);

        $group->attachPermissions(compact('create', 'update', 'delete'));

        static::assertFiredEvents(['attaching-permissions', 'attached-permissions']);
        static::assertCount(3, $group->permissions);

        $group->detachPermissions([$create->id, $delete->id]);

        static::assertFiredEvents(['detaching-permissions', 'detached-permissions']);
        static::assertCount(1, $group->permissions);

        $expected = $group->permissions->first();
        static::assertEquals($expected->slug, $update->slug);
    }

    /** @test */
    public function it_can_detach_all_permissions()
    {
        Event::fake();

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $permissions = [
            static::createNewPermission([
                'name'        => 'Create users',
                'slug'        => 'auth.users.create',
                'description' => 'Allow to create users',
            ]),
            static::createNewPermission([
                'name'        => 'Update users',
                'slug'        => 'auth.users.update',
                'description' => 'Allow to update users',
            ])
        ];

        static::assertCount(0, $group->permissions);

        $group->attachPermissions($permissions);

        static::assertFiredEvents(['attaching-permissions', 'attached-permissions']);
        static::assertCount(2, $group->permissions);
        foreach ($permissions as $permission) {
            static::assertTrue($group->hasPermission($permission));
        }

        $group->detachAllPermissions();

        static::assertFiredEvents(['detaching-all-permissions', 'detached-all-permissions']);
        static::assertCount(0, $group->permissions);
    }

    /** @test */
    public function it_can_attach_and_detach_permission_from_group_to_group()
    {
        Event::fake();

        $permission = static::createNewPermission([
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

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        static::assertFalse($authGroup->hasPermission($permission));
        static::assertCount(0, $authGroup->permissions);
        static::assertFalse($blogGroup->hasPermission($permission));
        static::assertCount(0, $blogGroup->permissions);

        $authGroup->attachPermission($permission);

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertTrue($authGroup->hasPermission($permission));
        static::assertCount(1, $authGroup->permissions);
        static::assertFalse($blogGroup->hasPermission($permission));
        static::assertCount(0, $blogGroup->permissions);
        static::assertEquals($authGroup->id, $permission->group_id);

        $blogGroup->attachPermission($permission);

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertFalse($authGroup->hasPermission($permission));
        static::assertCount(0, $authGroup->permissions);
        static::assertTrue($blogGroup->hasPermission($permission));
        static::assertCount(1, $blogGroup->permissions);
        static::assertEquals($blogGroup->id, $permission->group_id);

        $blogGroup->detachPermission($permission);

        static::assertFiredEvents(['detaching-permission', 'detached-permission']);
        static::assertFalse($authGroup->hasPermission($permission));
        static::assertCount(0, $authGroup->permissions);
        static::assertFalse($blogGroup->hasPermission($permission));
        static::assertCount(0, $blogGroup->permissions);
        static::assertEquals(0, $permission->group_id);
    }

    /** @test */
    public function it_can_delete_a_group()
    {
        Event::fake();

        $group = $this->createGroup(
            $this->getAuthGroupAttributes()
        );

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $permission = static::createNewPermission([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Allow to create users',
        ]);
        $group->attachPermission($permission);

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertTrue($group->hasPermission($permission));
        static::assertCount(1, $group->permissions);
        static::assertEquals($group->id, $permission->group_id);

        $group->delete();

        Event::assertDispatched(DeletingPermissionsGroup::class, function ($e) {
            (new DetachingPermissions)->handle($e);

            Event::assertDispatched(DetachingAllPermissions::class);
            Event::assertDispatched(DetachedAllPermissions::class);

            return true;
        });

        /** @var  \Arcanedev\LaravelAuth\Models\Permission  $permission */
        $permission = Permission::query()->findOrFail($permission->id);

        static::assertEquals(0, $permission->group_id);
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
     * @return \Arcanedev\LaravelAuth\Models\PermissionsGroup|mixed
     */
    private function createGroup(array $attributes)
    {
        return $this->groupModel
            ->newQuery()
            ->create($attributes)
            ->refresh();
    }
}
