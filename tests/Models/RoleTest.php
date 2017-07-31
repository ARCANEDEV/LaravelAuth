<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Events\Roles as RoleEvents;
use Arcanedev\LaravelAuth\Listeners\Roles\DetachingPermissions;
use Arcanedev\LaravelAuth\Listeners\Roles\DetachingUsers;
use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\Pivots\RoleUser;
use Arcanedev\LaravelAuth\Models\Role;
use Arcanedev\LaravelAuth\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

/**
 * Class     RoleTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @todo: Cleaning/Refactoring the event/listeners assertions.
 */
class RoleTest extends ModelsTest
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelAuth\Models\Role */
    protected $role;

    /** @var array */
    protected $modelEvents = [
        // Laravel Events
        'creating'        => RoleEvents\CreatingRole::class,
        'created'         => RoleEvents\CreatedRole::class,
        'saving'          => RoleEvents\SavingRole::class,
        'saved'           => RoleEvents\SavedRole::class,
        'updating'        => RoleEvents\UpdatingRole::class,
        'updated'         => RoleEvents\UpdatedRole::class,
        'deleting'        => RoleEvents\DeletingRole::class,
        'deleted'         => RoleEvents\DeletedRole::class,
        'restoring'       => RoleEvents\RestoringRole::class,
        'restored'        => RoleEvents\RestoredRole::class,

        // Custom events
        'attaching-user'  => RoleEvents\AttachingUserToRole::class,
        'attached-user'   => RoleEvents\AttachedUserToRole::class,
        'detaching-user'  => RoleEvents\DetachingUserFromRole::class,
        'detached-user'   => RoleEvents\DetachedUserFromRole::class,
        'detaching-users' => RoleEvents\DetachingAllUsersFromRole::class,
        'detached-users'  => RoleEvents\DetachedAllUsersFromRole::class,

        'attaching-permission'  => RoleEvents\AttachingPermissionToRole::class,
        'attached-permission'   => RoleEvents\AttachedPermissionToRole::class,
        'detaching-permission'  => RoleEvents\DetachingPermissionFromRole::class,
        'detached-permission'   => RoleEvents\DetachedPermissionFromRole::class,
        'detaching-permissions' => RoleEvents\DetachingAllPermissionsFromRole::class,
        'detached-permissions'  => RoleEvents\DetachedAllPermissionsFromRole::class,
    ];

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp()
    {
        parent::setUp();

        $this->role = new Role;
    }

    public function tearDown()
    {
        unset($this->role);

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
            \Arcanesoft\Contracts\Auth\Models\Role::class,
            \Arcanedev\LaravelAuth\Models\Role::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->role);
        }
    }

    /** @test */
    public function it_has_relationships()
    {
        $usersRelationship       = $this->role->users();
        $permissionsRelationship = $this->role->permissions();

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
        Event::fake();

        $attributes = [
            'name'        => 'Custom role',
            'slug'        => 'Custom role',
            'description' => 'Custom role description.',
        ];

        $role = $this->createRole($attributes);

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

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
        Event::fake();

        $attributes = $this->getAdminRoleAttributes();

        $role = $this->createRole($attributes);

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

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

        $this->assertFiredEvents(['updating', 'updated', 'saving', 'saved']);

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
        Event::fake();

        $attributes = $this->getAdminRoleAttributes();

        $role = $this->createRole($attributes);

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $this->assertTrue($role->isActive());

        $saved = $role->deactivate();

        $this->assertFiredEvents(['saving', 'saved', 'updating', 'updated']);

        $this->assertTrue($saved);
        $this->assertFalse($role->isActive());

        $saved = $role->activate();

        $this->assertFiredEvents(['saving', 'saved', 'updating', 'updated']);

        $this->assertTrue($saved);
        $this->assertTrue($role->isActive());

        $saved = $role->deactivate(false);

        $this->assertFiredEvents(['saving', 'saved', 'updating', 'updated']);

        $this->assertFalse($saved);
        $this->assertFalse($role->isActive());

        $saved = $role->activate(false);

        $this->assertFiredEvents(['saving', 'saved', 'updating', 'updated']);

        $this->assertFalse($saved);
        $this->assertTrue($role->isActive());
    }

    /** @test */
    public function it_can_delete()
    {
        Event::fake();

        $role = $this->createRole();

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $this->seeInPrefixedDatabase('roles', $role->toArray());

        $role->delete();

        Event::assertDispatched(RoleEvents\DeletingRole::class, function (RoleEvents\DeletingRole $e) use ($role) {
            (new DetachingUsers)->handle($e);
            (new DetachingPermissions)->handle($e);

            return $e->role->id === $role->id && $role->users->isEmpty() && $role->permissions->isEmpty();
        });
        $this->assertFiredEvents(['deleted']);

        $this->dontSeeInPrefixedDatabase('roles', $role->toArray());
    }

    /** @test */
    public function it_can_attach_and_detach_user()
    {
        Event::fake();

        $role = $this->createRole([
            'name'         => 'Custom role',
            'description'  => 'Custom role description.',
        ]);

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        /** @var  \Arcanesoft\Contracts\Auth\Models\User  $admin */
        $admin  = User::create([
            'username'   => 'super-admin',
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'email'      => 'sys.admin@gmail.com',
            'password'   => 'SuPeR-PaSsWoRd',
        ]);
        /** @var  \Arcanesoft\Contracts\Auth\Models\User  $member */
        $member = User::create([
            'username'   => 'john-doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ]);

        $this->assertCount(0, $role->users);

        $role->attachUser($admin);

        $this->assertFiredEvents(['attaching-user', 'attached-user']);

        $this->assertCount(1, $role->users);
        $this->assertTrue($role->hasUser($admin));

        $role->attachUser($member);

        $this->assertFiredEvents(['attaching-user', 'attached-user']);

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

        $this->assertFiredEvents(['detaching-user', 'detached-user']);

        $this->assertCount(1, $role->users);
        $this->assertFalse($role->hasUser($admin));
        $this->assertTrue($role->hasUser($member));

        $role->detachUser($member);

        $this->assertFiredEvents(['detaching-user', 'detached-user']);

        $this->assertCount(0, $role->users);
        $this->assertFalse($role->hasUser($admin));
        $this->assertFalse($role->hasUser($member));
    }

    /** @test */
    public function it_can_prevent_attaching_a_duplicated_user()
    {
        Event::fake();

        $role = $this->createRole();

        /** @var \Arcanesoft\Contracts\Auth\Models\User $user */
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
        Event::fake();

        $role = $this->createRole([
            'name'         => 'Custom role',
            'description'  => 'Custom role description.',
        ]);

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        /** @var  \Arcanesoft\Contracts\Auth\Models\User  $admin */
        $admin  = User::create([
            'username'   => 'super-admin',
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'email'      => 'sys.admin@gmail.com',
            'password'   => 'SuPeR-PaSsWoRd',
        ]);

        /** @var  \Arcanesoft\Contracts\Auth\Models\User  $member */
        $member = User::create([
            'username'   => 'john-doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ]);

        $this->assertCount(0, $role->users);

        $role->attachUser($admin);

        $this->assertFiredEvents(['attaching-user', 'attached-user']);

        $this->assertCount(1, $role->users);
        $this->assertTrue($role->hasUser($admin));

        $role->attachUser($member);

        $this->assertFiredEvents(['attaching-user', 'attached-user']);

        $this->assertCount(2, $role->users);
        $this->assertTrue($role->hasUser($admin));
        $this->assertTrue($role->hasUser($member));

        $role->detachAllUsers();

        $this->assertFiredEvents(['detaching-users', 'detached-users']);

        $this->assertCount(0, $role->users);
        $this->assertFalse($role->hasUser($admin));
        $this->assertFalse($role->hasUser($member));
    }

    /** @test */
    public function it_can_attach_and_detach_permission()
    {
        Event::fake();

        $role = $this->createRole();

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        /** @var  \Arcanesoft\Contracts\Auth\Models\Permission  $createUserPermission */
        $createUserPermission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);
        /** @var  \Arcanesoft\Contracts\Auth\Models\Permission  $updateUserPermission */
        $updateUserPermission = Permission::create([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Update users permission description.',
        ]);

        $this->assertCount(0, $role->permissions);

        $role->attachPermission($createUserPermission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertCount(1, $role->permissions);
        $this->assertTrue($role->hasPermission($createUserPermission));

        $role->attachPermission($updateUserPermission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertCount(2, $role->permissions);
        $this->assertTrue($role->hasPermission($createUserPermission));
        $this->assertTrue($role->hasPermission($updateUserPermission));

        $role->detachPermission($createUserPermission);

        $this->assertFiredEvents(['detaching-permission', 'detached-permission']);
        $this->assertCount(1, $role->permissions);
        $this->assertFalse($role->hasPermission($createUserPermission));
        $this->assertTrue($role->hasPermission($updateUserPermission));

        $role->detachPermission($updateUserPermission);

        $this->assertFiredEvents(['detaching-permission', 'detached-permission']);
        $this->assertCount(0, $role->permissions);
        $this->assertFalse($role->hasPermission($createUserPermission));
        $this->assertFalse($role->hasPermission($updateUserPermission));

        $role->attachPermission($createUserPermission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertCount(1, $role->permissions);
        $this->assertTrue($role->hasPermission($createUserPermission));
        $this->assertFalse($role->hasPermission($updateUserPermission));
    }

    /** @test */
    public function it_can_prevent_attaching_a_duplicated_permission()
    {
        Event::fake();

        $role = $this->createRole();

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        /** @var  \Arcanesoft\Contracts\Auth\Models\Permission  $createUserPermission */
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

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
    }

    /** @test */
    public function it_can_detach_all_permissions()
    {
        Event::fake();

        $role = $this->createRole();

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        /** @var  \Arcanesoft\Contracts\Auth\Models\Permission  $createUserPermission */
        $createUserPermission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);
        /** @var  \Arcanesoft\Contracts\Auth\Models\Permission  $updateUserPermission */
        $updateUserPermission = Permission::create([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Update users permission description.',
        ]);

        $this->assertCount(0, $role->permissions);

        $role->attachPermission($createUserPermission);
        $role->attachPermission($updateUserPermission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertCount(2, $role->permissions);
        $this->assertTrue($role->hasPermission($createUserPermission));
        $this->assertTrue($role->hasPermission($updateUserPermission));

        $role->detachAllPermissions();

        $this->assertFiredEvents(['detaching-permissions', 'detached-permissions']);
        $this->assertCount(0, $role->permissions);
    }

    /** @test */
    public function it_can_check_has_same_permission()
    {
        Event::fake();

        $role = $this->createRole();

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        $this->assertFalse($role->can('auth.users.create'));

        /** @var  \Arcanesoft\Contracts\Auth\Models\Permission  $createUserPermission */
        $createUserPermission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);

        $role->attachPermission($createUserPermission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertTrue($role->can('auth.users.create'));
    }

    /** @test */
    public function it_can_check_if_has_any_permissions()
    {
        Event::fake();

        $role = $this->createRole();

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $permissionsToCheck = [
            'auth.users.create',
            'auth.users.update',
            'auth.users.delete',
        ];

        /** @var  \Illuminate\Support\Collection  $failedPermissions */
        $this->assertFalse($role->canAny($permissionsToCheck, $failedPermissions));
        $this->assertCount(3, $failedPermissions);
        $this->assertSame($permissionsToCheck, $failedPermissions->all());

        /** @var  \Arcanesoft\Contracts\Auth\Models\Permission  $createUserPermission */
        $createUserPermission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);

        $role->attachPermission($createUserPermission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertTrue($role->canAny($permissionsToCheck, $failedPermissions));
        $this->assertCount(2, $failedPermissions);
        $this->assertSame([
            'auth.users.update',
            'auth.users.delete',
        ], $failedPermissions->all());

        /** @var  \Arcanesoft\Contracts\Auth\Models\Permission  $updateUserPermission */
        $updateUserPermission = Permission::create([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Update users permission description.',
        ]);

        $role->attachPermission($updateUserPermission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertTrue($role->canAny($permissionsToCheck, $failedPermissions));
        $this->assertCount(1, $failedPermissions);
        $this->assertSame(['auth.users.delete'], $failedPermissions->all());

        /** @var  \Arcanesoft\Contracts\Auth\Models\Permission  $deleteUserPermission */
        $deleteUserPermission = Permission::create([
            'name'        => 'Delete users',
            'slug'        => 'auth.users.delete',
            'description' => 'Delete users permission description.',
        ]);

        $role->attachPermission($deleteUserPermission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertTrue($role->canAny($permissionsToCheck, $failedPermissions));
        $this->assertEmpty($failedPermissions->all());
    }

    /** @test */
    public function it_can_check_if_has_all_permissions()
    {
        Event::fake();

        $role = $this->createRole();

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $permissionsToCheck = [
            'auth.users.create',
            'auth.users.update',
            'auth.users.delete',
        ];

        /** @var  \Illuminate\Support\Collection  $failed */
        $this->assertFalse($role->canAll($permissionsToCheck, $failed));
        $this->assertCount(3, $failed);
        $this->assertSame($permissionsToCheck, $failed->all());

        /** @var  \Arcanesoft\Contracts\Auth\Models\Permission  $createUserPermission */
        $createUserPermission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);

        $role->attachPermission($createUserPermission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertFalse($role->canAll($permissionsToCheck, $failed));
        $this->assertCount(2, $failed);
        $this->assertSame([
            'auth.users.update',
            'auth.users.delete',
        ], $failed->all());

        /** @var  \Arcanesoft\Contracts\Auth\Models\Permission  $updateUserPermission */
        $updateUserPermission = Permission::create([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Update users permission description.',
        ]);

        $role->attachPermission($updateUserPermission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertFalse($role->canAll($permissionsToCheck, $failed));
        $this->assertCount(1, $failed);
        $this->assertSame(['auth.users.delete'], $failed->all());

        /** @var  \Arcanesoft\Contracts\Auth\Models\Permission  $deleteUserPermission */
        $deleteUserPermission = Permission::create([
            'name'        => 'Delete users',
            'slug'        => 'auth.users.delete',
            'description' => 'Delete users permission description.',
        ]);

        $role->attachPermission($deleteUserPermission);

        $this->assertFiredEvents(['attaching-permission', 'attached-permission']);
        $this->assertTrue($role->canAll($permissionsToCheck, $failed));
        $this->assertEmpty($failed->all());
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
     * @return \Arcanedev\LaravelAuth\Models\Role
     */
    private function createRole(array $attributes = [])
    {
        if (empty($attributes)) {
            $attributes = $this->getAdminRoleAttributes();
        }

        $role = $this->role->create($attributes);

        return $this->role->find($role->id);
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
}
