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
            static::assertInstanceOf($expected, $this->role);
        }
    }

    /** @test */
    public function it_has_relationships()
    {
        $usersRelationship       = $this->role->users();
        $permissionsRelationship = $this->role->permissions();

        static::assertInstanceOf(BelongsToMany::class, $usersRelationship);
        static::assertInstanceOf(BelongsToMany::class, $permissionsRelationship);

        /**
         * @var  \Arcanedev\LaravelAuth\Models\User        $user
         * @var  \Arcanedev\LaravelAuth\Models\Permission  $permission
         */
        $user       = $usersRelationship->getRelated();
        $permission = $permissionsRelationship->getRelated();

        static::assertInstanceOf(User::class,       $user);
        static::assertInstanceOf(Permission::class, $permission);
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

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        static::assertSame($attributes['name'],                 $role->name);
        static::assertSame(Str::slug($attributes['slug'], '-'), $role->slug);
        static::assertSame($attributes['description'],          $role->description);
        static::assertTrue($role->is_active);
        static::assertTrue($role->isActive());
        static::assertFalse($role->is_locked);
        static::assertFalse($role->isLocked());
    }

    /** @test */
    public function it_can_update()
    {
        Event::fake();

        $attributes = $this->getAdminRoleAttributes();

        $role = $this->createRole($attributes);

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $this->seeInPrefixedDatabase('roles', $attributes);
        $this->seeInPrefixedDatabase('roles', Arr::except($role->toArray(), ['created_at', 'updated_at']));

        static::assertTrue($role->is_active);
        static::assertTrue($role->isActive());
        static::assertFalse($role->is_locked);
        static::assertFalse($role->isLocked());

        $updatedAttributes = [
            'name'        => 'Custom role',
            'description' => 'Custom role description.',
        ];

        $role->update($updatedAttributes);

        static::assertFiredEvents(['updating', 'updated', 'saving', 'saved']);

        $this->dontSeeInPrefixedDatabase('roles', $attributes);
        $this->seeInPrefixedDatabase('roles', $updatedAttributes);
        $this->seeInPrefixedDatabase('roles', Arr::except($role->toArray(), ['created_at', 'updated_at']));

        static::assertTrue($role->is_active);
        static::assertTrue($role->isActive());
        static::assertFalse($role->is_locked);
        static::assertFalse($role->isLocked());
    }

    /** @test */
    public function it_activate_and_disable()
    {
        Event::fake();

        $attributes = $this->getAdminRoleAttributes();

        $role = $this->createRole($attributes);

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        static::assertTrue($role->isActive());

        $saved = $role->deactivate();

        static::assertFiredEvents(['saving', 'saved', 'updating', 'updated']);

        static::assertTrue($saved);
        static::assertFalse($role->isActive());

        $saved = $role->activate();

        static::assertFiredEvents(['saving', 'saved', 'updating', 'updated']);

        static::assertTrue($saved);
        static::assertTrue($role->isActive());

        $saved = $role->deactivate(false);

        static::assertFiredEvents(['saving', 'saved', 'updating', 'updated']);

        static::assertFalse($saved);
        static::assertFalse($role->isActive());

        $saved = $role->activate(false);

        static::assertFiredEvents(['saving', 'saved', 'updating', 'updated']);

        static::assertFalse($saved);
        static::assertTrue($role->isActive());
    }

    /** @test */
    public function it_can_delete()
    {
        Event::fake();

        $role = $this->createRole();

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $this->seeInPrefixedDatabase('roles', $role->toArray());

        $role->delete();

        Event::assertDispatched(RoleEvents\DeletingRole::class, function (RoleEvents\DeletingRole $e) use ($role) {
            (new DetachingUsers)->handle($e);
            (new DetachingPermissions)->handle($e);

            return $e->role->id === $role->id && $role->users->isEmpty() && $role->permissions->isEmpty();
        });
        static::assertFiredEvents(['deleted']);

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

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $admin = static::createNewUser([
            'username'   => 'super-admin',
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'email'      => 'sys.admin@gmail.com',
            'password'   => 'SuPeR-PaSsWoRd',
        ]);

        $member = static::createNewUser([
            'username'   => 'john-doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ]);

        static::assertCount(0, $role->users);

        $role->attachUser($admin);

        static::assertFiredEvents(['attaching-user', 'attached-user']);

        static::assertCount(1, $role->users);
        static::assertTrue($role->hasUser($admin));

        $role->attachUser($member);

        static::assertFiredEvents(['attaching-user', 'attached-user']);

        static::assertCount(2, $role->users);
        static::assertTrue($role->hasUser($admin));
        static::assertTrue($role->hasUser($member));

        // Assert the pivot table
        foreach ($role->users as $user) {
            static::assertInstanceOf(RoleUser::class, $user->pivot);
            static::assertSame($user->pivot->role_id, $role->id);
            static::assertSame($user->pivot->user_id, $user->id);
        }

        $role->detachUser($admin);

        static::assertFiredEvents(['detaching-user', 'detached-user']);

        static::assertCount(1, $role->users);
        static::assertFalse($role->hasUser($admin));
        static::assertTrue($role->hasUser($member));

        $role->detachUser($member);

        static::assertFiredEvents(['detaching-user', 'detached-user']);

        static::assertCount(0, $role->users);
        static::assertFalse($role->hasUser($admin));
        static::assertFalse($role->hasUser($member));
    }

    /** @test */
    public function it_can_prevent_attaching_a_duplicated_user()
    {
        Event::fake();

        $role = $this->createRole();

        /** @var \Arcanesoft\Contracts\Auth\Models\User $user */
        $user = static::createNewUser([
            'username'   => 'john-doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ]);

        static::assertCount(0, $role->users);

        for ($i = 0; $i < 5; $i++) {
            $role->attachUser($user);
            static::assertCount(1, $role->users);
            static::assertTrue($role->hasUser($user));
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

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $admin  = static::createNewUser([
            'username'   => 'super-admin',
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'email'      => 'sys.admin@gmail.com',
            'password'   => 'SuPeR-PaSsWoRd',
        ]);

        $member = static::createNewUser([
            'username'   => 'john-doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ]);

        static::assertCount(0, $role->users);

        $role->attachUser($admin);

        static::assertFiredEvents(['attaching-user', 'attached-user']);

        static::assertCount(1, $role->users);
        static::assertTrue($role->hasUser($admin));

        $role->attachUser($member);

        static::assertFiredEvents(['attaching-user', 'attached-user']);

        static::assertCount(2, $role->users);
        static::assertTrue($role->hasUser($admin));
        static::assertTrue($role->hasUser($member));

        $role->detachAllUsers();

        static::assertFiredEvents(['detaching-users', 'detached-users']);

        static::assertCount(0, $role->users);
        static::assertFalse($role->hasUser($admin));
        static::assertFalse($role->hasUser($member));
    }

    /** @test */
    public function it_can_attach_and_detach_permission()
    {
        Event::fake();

        $role = $this->createRole();

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $createUserPermission = static::createNewPermission([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);
        $updateUserPermission = static::createNewPermission([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Update users permission description.',
        ]);

        static::assertCount(0, $role->permissions);

        $role->attachPermission($createUserPermission);

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertCount(1, $role->permissions);
        static::assertTrue($role->hasPermission($createUserPermission));

        $role->attachPermission($updateUserPermission);

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertCount(2, $role->permissions);
        static::assertTrue($role->hasPermission($createUserPermission));
        static::assertTrue($role->hasPermission($updateUserPermission));

        $role->detachPermission($createUserPermission);

        static::assertFiredEvents(['detaching-permission', 'detached-permission']);
        static::assertCount(1, $role->permissions);
        static::assertFalse($role->hasPermission($createUserPermission));
        static::assertTrue($role->hasPermission($updateUserPermission));

        $role->detachPermission($updateUserPermission);

        static::assertFiredEvents(['detaching-permission', 'detached-permission']);
        static::assertCount(0, $role->permissions);
        static::assertFalse($role->hasPermission($createUserPermission));
        static::assertFalse($role->hasPermission($updateUserPermission));

        $role->attachPermission($createUserPermission);

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertCount(1, $role->permissions);
        static::assertTrue($role->hasPermission($createUserPermission));
        static::assertFalse($role->hasPermission($updateUserPermission));
    }

    /** @test */
    public function it_can_prevent_attaching_a_duplicated_permission()
    {
        Event::fake();

        $role = $this->createRole();

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $createUserPermission = static::createNewPermission([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);

        static::assertCount(0, $role->permissions);

        for ($i = 0; $i < 5; $i++) {
            $role->attachPermission($createUserPermission);
            static::assertCount(1, $role->permissions);
            static::assertTrue($role->hasPermission($createUserPermission));
        }

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
    }

    /** @test */
    public function it_can_detach_all_permissions()
    {
        Event::fake();

        $role = $this->createRole();

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $createUserPermission = static::createNewPermission([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);
        $updateUserPermission = static::createNewPermission([
            'name'        => 'Update users',
            'slug'        => 'auth.users.update',
            'description' => 'Update users permission description.',
        ]);

        static::assertCount(0, $role->permissions);

        $role->attachPermission($createUserPermission);
        $role->attachPermission($updateUserPermission);

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertCount(2, $role->permissions);
        static::assertTrue($role->hasPermission($createUserPermission));
        static::assertTrue($role->hasPermission($updateUserPermission));

        $role->detachAllPermissions();

        static::assertFiredEvents(['detaching-permissions', 'detached-permissions']);
        static::assertCount(0, $role->permissions);
    }

    /** @test */
    public function it_can_check_has_same_permission()
    {
        Event::fake();

        $role = $this->createRole();

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);
        static::assertFalse($role->can('auth.users.create'));

        $role->attachPermission(
            static::createNewPermission([
                'name'        => 'Create users',
                'slug'        => 'auth.users.create',
                'description' => 'Create users permission description.',
            ])
        );

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertTrue($role->can('auth.users.create'));
    }

    /** @test */
    public function it_can_check_if_has_any_permissions()
    {
        Event::fake();

        $role = $this->createRole();

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $permissionsToCheck = [
            'auth.users.create',
            'auth.users.update',
            'auth.users.delete',
        ];

        /** @var  \Illuminate\Support\Collection  $failedPermissions */
        static::assertFalse($role->canAny($permissionsToCheck, $failedPermissions));
        static::assertCount(3, $failedPermissions);
        static::assertSame($permissionsToCheck, $failedPermissions->all());

        $role->attachPermission(
            static::createNewPermission([
                'name'        => 'Create users',
                'slug'        => 'auth.users.create',
                'description' => 'Create users permission description.',
            ])
        );

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertTrue($role->canAny($permissionsToCheck, $failedPermissions));
        static::assertCount(2, $failedPermissions);
        static::assertSame([
            'auth.users.update',
            'auth.users.delete',
        ], $failedPermissions->all());

        $role->attachPermission(
            static::createNewPermission([
                'name'        => 'Update users',
                'slug'        => 'auth.users.update',
                'description' => 'Update users permission description.',
            ])
        );

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertTrue($role->canAny($permissionsToCheck, $failedPermissions));
        static::assertCount(1, $failedPermissions);
        static::assertSame(['auth.users.delete'], $failedPermissions->all());

        $role->attachPermission(
            static::createNewPermission([
                'name'        => 'Delete users',
                'slug'        => 'auth.users.delete',
                'description' => 'Delete users permission description.',
            ])
        );

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertTrue($role->canAny($permissionsToCheck, $failedPermissions));
        static::assertEmpty($failedPermissions->all());
    }

    /** @test */
    public function it_can_check_if_has_all_permissions()
    {
        Event::fake();

        $role = $this->createRole();

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $permissionsToCheck = [
            'auth.users.create',
            'auth.users.update',
            'auth.users.delete',
        ];

        /** @var  \Illuminate\Support\Collection  $failed */
        static::assertFalse($role->canAll($permissionsToCheck, $failed));
        static::assertCount(3, $failed);
        static::assertSame($permissionsToCheck, $failed->all());

        $role->attachPermission(
            static::createNewPermission([
                'name'        => 'Create users',
                'slug'        => 'auth.users.create',
                'description' => 'Create users permission description.',
            ])
        );

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertFalse($role->canAll($permissionsToCheck, $failed));
        static::assertCount(2, $failed);
        static::assertSame([
            'auth.users.update',
            'auth.users.delete',
        ], $failed->all());

        $role->attachPermission(
            static::createNewPermission([
                'name'        => 'Update users',
                'slug'        => 'auth.users.update',
                'description' => 'Update users permission description.',
            ])
        );

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertFalse($role->canAll($permissionsToCheck, $failed));
        static::assertCount(1, $failed);
        static::assertSame(['auth.users.delete'], $failed->all());

        $role->attachPermission(
            static::createNewPermission([
                'name'        => 'Delete users',
                'slug'        => 'auth.users.delete',
                'description' => 'Delete users permission description.',
            ])
        );

        static::assertFiredEvents(['attaching-permission', 'attached-permission']);
        static::assertTrue($role->canAll($permissionsToCheck, $failed));
        static::assertEmpty($failed->all());
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
     * @return \Arcanedev\LaravelAuth\Models\Role|mixed
     */
    private function createRole(array $attributes = [])
    {
        if (empty($attributes)) {
            $attributes = $this->getAdminRoleAttributes();
        }

        $role = $this->role->newQuery()->create($attributes);

        return $role->refresh();
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
