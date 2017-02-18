<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\Pivots\RoleUser;
use Arcanedev\LaravelAuth\Models\Role;
use Arcanedev\LaravelAuth\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class     RoleTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RoleTest extends ModelsTest
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanedev\LaravelAuth\Models\Role */
    protected $roleModel;

    /** @var array */
    protected $modelEvents = [
        // Laravel Events
        'creating'        => \Arcanedev\LaravelAuth\Events\Roles\CreatingRole::class,
        'created'         => \Arcanedev\LaravelAuth\Events\Roles\CreatedRole::class,
        'saving'          => \Arcanedev\LaravelAuth\Events\Roles\SavingRole::class,
        'saved'           => \Arcanedev\LaravelAuth\Events\Roles\SavedRole::class,
        'updating'        => \Arcanedev\LaravelAuth\Events\Roles\UpdatingRole::class,
        'updated'         => \Arcanedev\LaravelAuth\Events\Roles\UpdatedRole::class,
        'deleting'        => \Arcanedev\LaravelAuth\Events\Roles\DeletingRole::class,
        'deleted'         => \Arcanedev\LaravelAuth\Events\Roles\DeletedRole::class,
        'restoring'       => \Arcanedev\LaravelAuth\Events\Roles\RestoringRole::class,
        'restored'        => \Arcanedev\LaravelAuth\Events\Roles\RestoredRole::class,

        // Custom events
        'attaching-user'  => \Arcanedev\LaravelAuth\Events\Roles\AttachingUserToRole::class,
        'attached-user'   => \Arcanedev\LaravelAuth\Events\Roles\AttachedUserToRole::class,
        'detaching-user'  => \Arcanedev\LaravelAuth\Events\Roles\DetachingUserFromRole::class,
        'detached-user'   => \Arcanedev\LaravelAuth\Events\Roles\DetachedUserFromRole::class,
        'detaching-users' => \Arcanedev\LaravelAuth\Events\Roles\DetachingAllUsersFromRole::class,
        'detached-users'  => \Arcanedev\LaravelAuth\Events\Roles\DetachedAllUsersFromRole::class,

        'attaching-permission'  => \Arcanedev\LaravelAuth\Events\Roles\AttachingPermissionToRole::class,
        'attached-permission'   => \Arcanedev\LaravelAuth\Events\Roles\AttachedPermissionToRole::class,
        'detaching-permission'  => \Arcanedev\LaravelAuth\Events\Roles\DetachingPermissionFromRole::class,
        'detached-permission'   => \Arcanedev\LaravelAuth\Events\Roles\DetachedPermissionFromRole::class,
        'detaching-permissions' => \Arcanedev\LaravelAuth\Events\Roles\DetachingAllPermissionsFromRole::class,
        'detached-permissions'  => \Arcanedev\LaravelAuth\Events\Roles\DetachedAllPermissionsFromRole::class,
    ];

    /* -----------------------------------------------------------------
     |  Setup Methods
     | -----------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $this->roleModel = new Role;
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($this->roleModel);
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
            \Arcanesoft\Contracts\Auth\Models\Role::class,
            \Arcanedev\LaravelAuth\Models\Role::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->roleModel);
        }
    }

    /** @test */
    public function it_has_relationships()
    {
        $usersRelationship       = $this->roleModel->users();
        $permissionsRelationship = $this->roleModel->permissions();

        $this->assertInstanceOf(BelongsToMany::class, $usersRelationship);
        $this->assertInstanceOf(BelongsToMany::class, $permissionsRelationship);

        /**
         * @var  \Arcanedev\LaravelAuth\Models\User        $user
         * @var  \Arcanedev\LaravelAuth\Models\Permission  $permission
         */
        $user       = $usersRelationship->getRelated();
        $permission = $permissionsRelationship->getRelated();

        $this->assertInstanceOf(User::class,       $user);
        $this->assertInstanceOf(Permission::class, $permission);
    }

    /** @test */
    public function it_can_create()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
        ]);

        $attributes = [
            'name'        => 'Custom role',
            'slug'        => 'Custom role',
            'description' => 'Custom role description.',
        ];

        $role = $this->createRole($attributes);

        $this->assertSame($attributes['name'],                 $role->name);
        $this->assertSame(Str::slug($attributes['slug'], '-'), $role->slug);
        $this->assertSame($attributes['description'],          $role->description);
        $this->assertTrue($role->is_active);
        $this->assertTrue($role->isActive());
        $this->assertFalse($role->is_locked);
        $this->assertFalse($role->isLocked());
    }

    /** @test */
    public function it_can_update()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved', 'updating', 'updated',
        ]);

        $attributes = $this->getAdminRoleAttributes();

        $role = $this->createRole($attributes);

        $this->seeInPrefixedDatabase('roles', $attributes);
        $this->seeInPrefixedDatabase('roles', Arr::except($role->toArray(), ['created_at', 'updated_at']));

        $this->assertTrue($role->is_active);
        $this->assertTrue($role->isActive());
        $this->assertFalse($role->is_locked);
        $this->assertFalse($role->isLocked());

        $updatedAttributes = [
            'name'        => 'Custom role',
            'description' => 'Custom role description.',
        ];

        $role->update($updatedAttributes);

        $this->dontSeeInPrefixedDatabase('roles', $attributes);
        $this->seeInPrefixedDatabase('roles', $updatedAttributes);
        $this->seeInPrefixedDatabase('roles', Arr::except($role->toArray(), ['created_at', 'updated_at']));

        $this->assertTrue($role->is_active);
        $this->assertTrue($role->isActive());
        $this->assertFalse($role->is_locked);
        $this->assertFalse($role->isLocked());
    }

    /** @test */
    public function it_activate_and_disable()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved', 'updating', 'updated',
        ]);

        $attributes = $this->getAdminRoleAttributes();

        $role = $this->createRole($attributes);

        $this->assertTrue($role->isActive());

        $saved = $role->deactivate();

        $this->assertTrue($saved);
        $this->assertFalse($role->isActive());

        $saved = $role->activate();

        $this->assertTrue($saved);
        $this->assertTrue($role->isActive());

        $saved = $role->deactivate(false);

        $this->assertFalse($saved);
        $this->assertFalse($role->isActive());

        $saved = $role->activate(false);

        $this->assertFalse($saved);
        $this->assertTrue($role->isActive());
    }

    /** @test */
    public function it_can_delete()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved', 'deleting', 'deleted',
        ]);

        $role = $this->createRole();

        $this->seeInPrefixedDatabase('roles', $role->toArray());

        $role->delete();

        $this->dontSeeInPrefixedDatabase('roles', $role->toArray());
    }

    /** @test */
    public function it_can_attach_and_detach_user()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-user', 'attached-user', 'detaching-user', 'detached-user',
        ]);

        $role = $this->createRole([
            'name'         => 'Custom role',
            'description'  => 'Custom role description.',
        ]);

        $admin = User::create([
            'username'   => 'super-admin',
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'email'      => 'sys.admin@gmail.com',
            'password'   => 'SuPeR-PaSsWoRd',
        ]);
        $member = User::create([
            'username'   => 'john-doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ]);

        $this->assertCount(0, $role->users);

        $role->attachUser($admin);

        $this->assertCount(1, $role->users);
        $this->assertTrue($role->hasUser($admin));

        $role->attachUser($member);

        $this->assertCount(2, $role->users);
        $this->assertTrue($role->hasUser($admin));
        $this->assertTrue($role->hasUser($member));

        // Assert the pivot table
        foreach ($role->users as $user) {
            $this->assertInstanceOf(RoleUser::class, $user->pivot);
            $this->assertSame($user->pivot->role_id, $role->id);
            $this->assertSame($user->pivot->user_id, $user->id);
        }

        $role->detachUser($admin);

        $this->assertCount(1, $role->users);
        $this->assertFalse($role->hasUser($admin));
        $this->assertTrue($role->hasUser($member));

        $role->detachUser($member);

        $this->assertCount(0, $role->users);
        $this->assertFalse($role->hasUser($admin));
        $this->assertFalse($role->hasUser($member));
    }

    /** @test */
    public function it_can_prevent_attaching_a_duplicated_user()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved', 'attaching-user', 'attached-user',
        ]);

        $role = $this->createRole();
        $user = User::create([
            'username'   => 'john-doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ]);

        $this->assertCount(0, $role->users);

        for ($i = 0; $i < 5; $i++) {
            $role->attachUser($user);
            $this->assertCount(1, $role->users);
            $this->assertTrue($role->hasUser($user));
        }
    }

    /** @test */
    public function it_can_detach_all_users()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-user', 'attached-user', 'detaching-users', 'detached-users',
        ]);

        $role = $this->createRole([
            'name'         => 'Custom role',
            'description'  => 'Custom role description.',
        ]);

        $admin  = User::create([
            'username'   => 'super-admin',
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'email'      => 'sys.admin@gmail.com',
            'password'   => 'SuPeR-PaSsWoRd',
        ]);
        $member = User::create([
            'username'   => 'john-doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ]);

        $this->assertCount(0, $role->users);

        $role->attachUser($admin);

        $this->assertCount(1, $role->users);
        $this->assertTrue($role->hasUser($admin));

        $role->attachUser($member);

        $this->assertCount(2, $role->users);
        $this->assertTrue($role->hasUser($admin));
        $this->assertTrue($role->hasUser($member));

        $role->detachAllUsers();

        $this->assertCount(0, $role->users);
        $this->assertFalse($role->hasUser($admin));
        $this->assertFalse($role->hasUser($member));
    }

    /** @test */
    public function it_can_attach_and_detach_permission()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-permission', 'attached-permission',
            'detaching-permission', 'detached-permission',
        ]);

        $role                 = $this->createRole();
        $createUserPermission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);
        $updateUserPermission = Permission::create([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Update users permission description.',
        ]);

        $this->assertCount(0, $role->permissions);

        $role->attachPermission($createUserPermission);
        $this->assertCount(1, $role->permissions);
        $this->assertTrue($role->hasPermission($createUserPermission));

        $role->attachPermission($updateUserPermission);
        $this->assertCount(2, $role->permissions);
        $this->assertTrue($role->hasPermission($createUserPermission));
        $this->assertTrue($role->hasPermission($updateUserPermission));

        $role->detachPermission($createUserPermission);
        $this->assertCount(1, $role->permissions);
        $this->assertFalse($role->hasPermission($createUserPermission));
        $this->assertTrue($role->hasPermission($updateUserPermission));

        $role->detachPermission($updateUserPermission);
        $this->assertCount(0, $role->permissions);
        $this->assertFalse($role->hasPermission($createUserPermission));
        $this->assertFalse($role->hasPermission($updateUserPermission));

        $role->attachPermission($createUserPermission);
        $this->assertCount(1, $role->permissions);
        $this->assertTrue($role->hasPermission($createUserPermission));
        $this->assertFalse($role->hasPermission($updateUserPermission));
    }

    /** @test */
    public function it_can_prevent_attaching_a_duplicated_permission()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-permission', 'attached-permission',
        ]);

        $role                 = $this->createRole();
        $createUserPermission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);

        $this->assertCount(0, $role->permissions);

        for ($i = 0; $i < 5; $i++) {
            $role->attachPermission($createUserPermission);
            $this->assertCount(1, $role->permissions);
            $this->assertTrue($role->hasPermission($createUserPermission));
        }
    }

    /** @test */
    public function it_can_detach_all_permissions()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-permission', 'attached-permission',
            'detaching-permissions', 'detached-permissions',
        ]);

        $role = $this->createRole();

        $createUserPermission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);
        $updateUserPermission = Permission::create([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Update users permission description.',
        ]);

        $this->assertCount(0, $role->permissions);

        $role->attachPermission($createUserPermission);
        $role->attachPermission($updateUserPermission);

        $this->assertCount(2, $role->permissions);
        $this->assertTrue($role->hasPermission($createUserPermission));
        $this->assertTrue($role->hasPermission($updateUserPermission));

        $role->detachAllPermissions();

        $this->assertCount(0, $role->permissions);
    }

    /** @test */
    public function it_can_check_has_same_permission()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-permission', 'attached-permission',
        ]);

        $role = $this->createRole();

        $this->assertFalse($role->can('auth.users.create'));

        $createUserPermission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);

        $role->attachPermission($createUserPermission);

        $this->assertTrue($role->can('auth.users.create'));
    }

    /** @test */
    public function it_can_check_if_has_any_permissions()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-permission', 'attached-permission',
        ]);

        $role = $this->createRole();

        $failedPermissions  = [];
        $permissionsToCheck = [
            'auth.users.create',
            'auth.users.update',
            'auth.users.delete',
        ];

        $this->assertFalse($role->canAny($permissionsToCheck, $failedPermissions));
        $this->assertCount(3, $failedPermissions);
        $this->assertSame($permissionsToCheck, $failedPermissions);

        $createUserPermission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);

        $role->attachPermission($createUserPermission);

        $failedPermissions  = [];

        $this->assertTrue($role->canAny($permissionsToCheck, $failedPermissions));
        $this->assertCount(2, $failedPermissions);
        $this->assertSame([
            'auth.users.update',
            'auth.users.delete',
        ], $failedPermissions);

        $updateUserPermission = Permission::create([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Update users permission description.',
        ]);

        $role->attachPermission($updateUserPermission);

        $failedPermissions  = [];

        $this->assertTrue($role->canAny($permissionsToCheck, $failedPermissions));
        $this->assertCount(1, $failedPermissions);
        $this->assertSame(['auth.users.delete'], $failedPermissions);

        $deleteUserPermission = Permission::create([
            'name'        => 'Delete users',
            'slug'        => 'auth.users.delete',
            'description' => 'Delete users permission description.',
        ]);

        $role->attachPermission($deleteUserPermission);

        $failedPermissions  = [];

        $this->assertTrue($role->canAny($permissionsToCheck, $failedPermissions));
        $this->assertEmpty($failedPermissions);
    }

    /** @test */
    public function it_can_check_if_has_all_permissions()
    {
        $this->checkFiredEvents([
            'creating', 'created', 'saving', 'saved',
            'attaching-permission', 'attached-permission',
        ]);

        $role = $this->createRole();

        $failedPermissions  = [];
        $permissionsToCheck = [
            'auth.users.create',
            'auth.users.update',
            'auth.users.delete',
        ];

        $this->assertFalse($role->canAll($permissionsToCheck, $failedPermissions));
        $this->assertCount(3, $failedPermissions);
        $this->assertSame($permissionsToCheck, $failedPermissions);

        $createUserPermission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);

        $role->attachPermission($createUserPermission);

        $failedPermissions  = [];

        $this->assertFalse($role->canAll($permissionsToCheck, $failedPermissions));
        $this->assertCount(2, $failedPermissions);
        $this->assertSame([
            'auth.users.update',
            'auth.users.delete',
        ], $failedPermissions);

        $updateUserPermission = Permission::create([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Update users permission description.',
        ]);

        $role->attachPermission($updateUserPermission);

        $failedPermissions  = [];

        $this->assertFalse($role->canAll($permissionsToCheck, $failedPermissions));
        $this->assertCount(1, $failedPermissions);
        $this->assertSame(['auth.users.delete'], $failedPermissions);

        $deleteUserPermission = Permission::create([
            'name'        => 'Delete users',
            'slug'        => 'auth.users.delete',
            'description' => 'Delete users permission description.',
        ]);

        $role->attachPermission($deleteUserPermission);

        $failedPermissions  = [];

        $this->assertTrue($role->canAll($permissionsToCheck, $failedPermissions));
        $this->assertEmpty($failedPermissions);
    }

    /* -----------------------------------------------------------------
     |  Helpers
     | -----------------------------------------------------------------
     */
    /**
     * Create role model.
     *
     * @param  array  $attributes
     *
     * @return Role
     */
    private function createRole(array $attributes = [])
    {
        if (empty($attributes)) {
            $attributes = $this->getAdminRoleAttributes();
        }

        /** @var Role $role */
        $role = $this->roleModel->create($attributes);

        return $this->roleModel->find($role->id);
    }

    /**
     * Get a dummy user attributes.
     *
     * @return array
     */
    private function getAdminRoleAttributes()
    {
        return [
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ];
    }

    /**
     * Check the fired & unfired events.
     *
     * @param  array  $keys
     */
    protected function checkFiredEvents(array $keys)
    {
        $events = collect($this->modelEvents);

        $this->expectsEvents(
            $events->only($keys)->values()->toArray()
        );

        $this->doesntExpectEvents(
            $events->except($keys)->values()->toArray()
        );
    }
}
