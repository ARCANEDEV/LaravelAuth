<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\Pivots\RoleUser;
use Arcanedev\LaravelAuth\Models\Role;
use Arcanedev\LaravelAuth\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class     UserTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UserTest extends ModelsTest
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /** @var  \Arcanedev\LaravelAuth\Models\User */
    protected $userModel;

    /** @var array */
    protected $modelEvents = [
        // Laravel Events
        'creating'        => \Arcanedev\LaravelAuth\Events\Users\CreatingUser::class,
        'created'         => \Arcanedev\LaravelAuth\Events\Users\CreatedUser::class,
        'saving'          => \Arcanedev\LaravelAuth\Events\Users\SavingUser::class,
        'saved'           => \Arcanedev\LaravelAuth\Events\Users\SavedUser::class,
        'updating'        => \Arcanedev\LaravelAuth\Events\Users\UpdatingUser::class,
        'updated'         => \Arcanedev\LaravelAuth\Events\Users\UpdatedUser::class,
        'deleting'        => \Arcanedev\LaravelAuth\Events\Users\DeletingUser::class,
        'deleted'         => \Arcanedev\LaravelAuth\Events\Users\DeletedUser::class,
        'restoring'       => \Arcanedev\LaravelAuth\Events\Users\RestoringUser::class,
        'restored'        => \Arcanedev\LaravelAuth\Events\Users\RestoredUser::class,

        // Custom events
        'confirming'      => \Arcanedev\LaravelAuth\Events\Users\ConfirmingUser::class,
        'confirmed'       => \Arcanedev\LaravelAuth\Events\Users\ConfirmedUser::class,
        'syncing-roles'   => \Arcanedev\LaravelAuth\Events\Users\SyncingUserWithRoles::class,
        'synced-roles'    => \Arcanedev\LaravelAuth\Events\Users\SyncedUserWithRoles::class,
        'attaching-role'  => \Arcanedev\LaravelAuth\Events\Users\AttachingRoleToUser::class,
        'attached-role'   => \Arcanedev\LaravelAuth\Events\Users\AttachedRoleToUser::class,
        'detaching-role'  => \Arcanedev\LaravelAuth\Events\Users\DetachingRole::class,
        'detached-role'   => \Arcanedev\LaravelAuth\Events\Users\DetachedRole::class,
        'detaching-roles' => \Arcanedev\LaravelAuth\Events\Users\DetachingRoles::class,
        'detached-roles'  => \Arcanedev\LaravelAuth\Events\Users\DetachedRoles::class,
    ];

    /* -----------------------------------------------------------------
     |  Setup Methods
     | -----------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $this->userModel = new User;
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($this->userModel);
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated()
    {
        $expectations = [
            // Laravel
            \Illuminate\Database\Eloquent\Model::class,
            \Illuminate\Contracts\Auth\Authenticatable::class,
            \Illuminate\Contracts\Auth\Access\Authorizable::class,
            \Illuminate\Contracts\Auth\CanResetPassword::class,

            // Arcanedev
            \Arcanesoft\Contracts\Auth\Models\User::class,
            \Arcanedev\LaravelAuth\Models\User::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->userModel);
        }
    }

    /** @test */
    public function it_has_relationships()
    {
        $rolesRelationship = $this->userModel->roles();

        $this->assertInstanceOf(BelongsToMany::class, $rolesRelationship);

        /** @var  Role  $roleModel */
        $roleModel = $rolesRelationship->getRelated();

        $this->assertInstanceOf(Role::class, $roleModel);
    }

    /** @test */
    public function it_can_create()
    {
        $this->checkFiredEvents([
            'created', 'creating', 'saved', 'saving',
        ]);

        $attributes = $this->getUserAttributes();
        $user       = $this->createUser();

        $this->assertSame($attributes['username'],    $user->username);
        $this->assertSame($attributes['first_name'],  $user->first_name);
        $this->assertSame($attributes['last_name'],   $user->last_name);
        $this->assertSame(
            $attributes['first_name'].' '.$attributes['last_name'], $user->full_name
        );
        $this->assertSame($attributes['email'],       $user->email);
        $this->assertNotSame($attributes['password'], $user->password);

        $this->assertFalse($user->is_admin);
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isModerator());
        $this->assertTrue($user->isMember());
        $this->assertFalse($user->is_active);
        $this->assertFalse($user->isActive());
        $this->assertFalse($user->is_confirmed);
        $this->assertFalse($user->isConfirmed());

        $this->assertCount(0, $user->roles);
    }

    /** @test */
    public function it_can_activate_and_deactivate()
    {
        $this->checkFiredEvents([
            'created', 'creating', 'saved', 'saving', 'updated', 'updating',
        ]);

        $attributes = [
            'username'   => 'john.doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ];

        /** @var User $user */
        $user = $this->userModel->create($attributes);
        $user = $this->userModel->where('id', $user->id)->first();

        $this->assertFalse($user->is_active);
        $this->assertFalse($user->isActive());

        $this->assertTrue($user->activate());
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->isActive());

        $this->assertTrue($user->deactivate());
        $this->assertFalse($user->is_active);
        $this->assertFalse($user->isActive());
    }

    /** @test */
    public function it_can_attach_and_detach_a_role()
    {
        $this->checkFiredEvents([
            'created', 'creating', 'saved', 'saving',
            'attaching-role', 'attached-role', 'detaching-role', 'detached-role',
        ]);

        $user      = $this->createUser();
        $adminRole = Role::create([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);
        $moderatorRole = Role::create([
            'name'        => 'Moderator',
            'description' => 'Moderator role descriptions.',
        ]);

        $this->assertCount(0, $user->roles);

        $user->attachRole($adminRole);
        $this->assertCount(1, $user->roles);
        $this->assertTrue($user->hasRole($adminRole));

        $user->attachRole($moderatorRole);
        $this->assertCount(2, $user->roles);
        $this->assertTrue($user->hasRole($adminRole));
        $this->assertTrue($user->hasRole($moderatorRole));

        // Assert the pivot table
        foreach ($user->roles as $role) {
            /** @var  \Arcanedev\LaravelAuth\Models\Role  $role */
            $this->assertInstanceOf(RoleUser::class, $role->pivot);
            $this->assertSame($user->id, $role->pivot->user_id);
            $this->assertSame($role->id, $role->pivot->role_id);
        }

        $user->detachRole($adminRole);
        $this->assertCount(1, $user->roles);
        $this->assertFalse($user->hasRole($adminRole));
        $this->assertTrue($user->hasRole($moderatorRole));

        $user->detachRole($moderatorRole);
        $this->assertCount(0, $user->roles);
        $this->assertFalse($user->hasRole($adminRole));
        $this->assertFalse($user->hasRole($moderatorRole));
    }

    /** @test */
    public function it_can_prevent_attaching_a_duplicated_role()
    {
        $this->checkFiredEvents([
            'created', 'creating', 'saved', 'saving', 'attaching-role', 'attached-role',
        ]);

        $user      = $this->createUser();
        $adminRole = Role::create([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);

        $this->assertCount(0, $user->roles);

        for ($i = 0; $i < 5; $i++) {
            $user->attachRole($adminRole);
            $this->assertCount(1, $user->roles);
            $this->assertTrue($user->hasRole($adminRole));
        }
    }

    /** @test */
    public function it_can_sync_roles_by_its_slugs()
    {
        $this->checkFiredEvents([
            'created', 'creating', 'saved', 'saving',
            'syncing-roles', 'synced-roles',
        ]);

        $user  = $this->createUser();
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

        $this->assertCount(0, $user->roles);

        $synced = $user->syncRoles(
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
        $this->checkFiredEvents([
            'created', 'creating', 'saved', 'saving',
            'attaching-role', 'attached-role', 'detaching-roles', 'detached-roles',
        ]);

        $user      = $this->createUser();
        $adminRole = Role::create([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);
        $moderatorRole = Role::create([
            'name'        => 'Moderator',
            'description' => 'Moderator role descriptions.',
        ]);

        $this->assertCount(0, $user->roles);

        $user->attachRole($adminRole);
        $user->attachRole($moderatorRole);

        $this->assertCount(2, $user->roles);

        $user->detachAllRoles();

        $this->assertCount(0, $user->roles);
    }

    /** @test */
    public function it_can_find_an_unconfirmed_user()
    {
        $user        = $this->createUser();
        $unconfirmed = $this->userModel->findUnconfirmed($user->confirmation_code);

        $this->assertEquals($user, $unconfirmed);
    }

    /** @test */
    public function it_must_throw_an_exception_on_not_found_unconfirmed_user()
    {
        $expectations = [
            \Illuminate\Database\Eloquent\ModelNotFoundException::class,
            \Arcanedev\LaravelAuth\Exceptions\UserConfirmationException::class,
        ];

        try {
            $this->userModel->findUnconfirmed(str_random(30));
        }
        catch(\Exception $e) {
            foreach ($expectations as $expected) {
                $this->assertInstanceOf($expected, $e);
            }

            $this->assertSame('Unconfirmed user was not found.', $e->getMessage());
        }
    }

    /** @test */
    public function it_can_confirm()
    {
        $this->checkFiredEvents([
            'created', 'creating', 'saved', 'saving', 'updated', 'updating',
            'confirming', 'confirmed'
        ]);

        $user = $this->createUser();

        $this->assertFalse($user->is_confirmed);
        $this->assertFalse($user->isConfirmed());
        $this->assertNotNull($user->confirmation_code);
        $this->assertNull($user->confirmed_at);

        $user = $this->userModel->confirm($user);

        $this->assertTrue($user->is_confirmed);
        $this->assertTrue($user->isConfirmed());
        $this->assertNull($user->confirmation_code);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->confirmed_at);
    }

    /** @test */
    public function it_can_confirm_by_code()
    {
        $this->checkFiredEvents([
            'created', 'creating', 'saved', 'saving', 'updated', 'updating',
            'confirming', 'confirmed'
        ]);

        $user = $this->createUser();

        $this->assertFalse($user->is_confirmed);
        $this->assertFalse($user->isConfirmed());
        $this->assertNotNull($user->confirmation_code);
        $this->assertNull($user->confirmed_at);

        $user = $this->userModel->confirm($user->confirmation_code);

        $this->assertTrue($user->is_confirmed);
        $this->assertTrue($user->isConfirmed());
        $this->assertNull($user->confirmation_code);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->confirmed_at);
    }

    /** @test */
    public function it_can_delete()
    {
        $this->checkFiredEvents([
            'created', 'creating', 'saved', 'saving', 'deleting', 'deleted',
        ]);

        $user   = $this->createUser();
        $userId = $user->id;

        $this->assertFalse($user->trashed());
        $this->assertTrue($user->delete());

        $user = $this->userModel->find($userId);

        $this->assertNull($user);

        /** @var User $user */
        $user = $this->userModel->onlyTrashed()->find($userId);

        $this->assertTrue($user->trashed());

        $user->forceDelete();

        $user = $this->userModel->find($userId);

        $this->assertNull($user);
    }

    /** @test */
    public function it_can_not_delete_admin()
    {
        $this->checkFiredEvents([
            'created', 'creating', 'saved', 'saving', 'updating', 'updated',
        ]);

        $user           = $this->createUser();
        $adminId        = $user->id;
        $user->is_admin = true;
        $user->save();

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isModerator());
        $this->assertFalse($user->isMember());
        $this->assertFalse($user->trashed());
        $this->assertFalse($user->delete());

        $user = $this->userModel->find($adminId);

        $this->assertNotNull($user);
    }

    /** @test */
    public function it_can_restore()
    {
        $this->checkFiredEvents([
            'created', 'creating', 'saved', 'saving', 'updating', 'updated',
            'deleting', 'deleted', 'restoring', 'restored',
        ]);

        $user   = $this->createUser();
        $userId = $user->id;

        $this->assertFalse($user->trashed());
        $this->assertTrue($user->delete());

        $user = $this->userModel->find($userId);

        $this->assertNull($user);

        /** @var User $user */
        $user = $this->userModel->onlyTrashed()->find($userId);

        $this->assertTrue($user->trashed());

        $user->restore();

        $user = $this->userModel->find($userId);

        $this->assertNotNull($user);
        $this->assertFalse($user->trashed());
    }

    /** @test */
    public function it_can_check_has_same_role()
    {
        $user      = $this->createUser();

        $this->assertFalse($user->hasRoleSlug('admin'));

        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $user->attachRole($adminRole);

        $this->assertTrue($user->hasRoleSlug('Admin'));
        $this->assertTrue($user->hasRoleSlug('admin'));
    }

    /** @test */
    public function it_can_check_has_any_role()
    {
        $user = $this->createUser();

        /** @var  \Illuminate\Support\Collection  $failedRoles */
        $this->assertFalse($user->isOne(['admin', 'member'], $failedRoles));
        $this->assertCount(2, $failedRoles);
        $this->assertSame(['admin', 'member'], $failedRoles->all());

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $adminRole */
        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $user->attachRole($adminRole);

        $failedRoles = [];
        $this->assertTrue($user->isOne(['admin', 'member'], $failedRoles));
        $this->assertCount(1, $failedRoles);
        $this->assertSame(['member'], $failedRoles->all());
    }

    /** @test */
    public function it_can_check_has_all_roles()
    {
        $user = $this->createUser();

        /** @var  \Illuminate\Support\Collection  $failedRoles */
        $this->assertFalse($user->isAll(['admin', 'member'], $failedRoles));
        $this->assertCount(2, $failedRoles);
        $this->assertSame(['admin', 'member'], $failedRoles->all());

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $adminRole */
        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $user->attachRole($adminRole);

        $this->assertFalse($user->isAll(['admin', 'member'], $failedRoles));
        $this->assertCount(1, $failedRoles);
        $this->assertSame(['member'], $failedRoles->all());

        /** @var  \Arcanesoft\Contracts\Auth\Models\Role  $memberRole */
        $memberRole = Role::create([
            'name'        => 'Member',
            'slug'        => 'member',
            'description' => 'Member role descriptions.',
        ]);

        $user->attachRole($memberRole);

        $this->assertTrue($user->isAll(['admin', 'member'], $failedRoles));
        $this->assertEmpty($failedRoles);
    }

    /** @test */
    public function it_can_check_if_has_permission()
    {
        $adminRole     = $this->createAdminRole();
        $moderatorRole = $this->createModeratorRole();
        $user          = $this->createUser();

        $user->attachRole($adminRole);
        $user->attachRole($moderatorRole);

        $this->assertCount(4, $user->permissions);

        $this->assertTrue($user->may('auth.users.create'));
        $this->assertTrue($user->may('auth.users.update'));
        $this->assertTrue($user->may('blog.posts.create'));
        $this->assertTrue($user->may('blog.posts.update'));
    }

    /** @test */
    public function it_can_check_if_has_one_of_permissions()
    {
        $adminRole     = $this->createAdminRole();
        $moderatorRole = $this->createModeratorRole();
        $user          = $this->createUser();

        $user->attachRole($adminRole);
        $user->attachRole($moderatorRole);

        $this->assertCount(4, $user->permissions);

        $permissionToCheck = [
            'auth.users.create',
            'auth.users.update',
            'blog.posts.create',
            'blog.posts.update',
        ];

        /** @var  \Illuminate\Support\Collection  $failed */
        $this->assertTrue($user->mayOne($permissionToCheck, $failed));
        $this->assertEmpty($failed);

        $permissionToCheck = array_merge($permissionToCheck, ['auth.users.delete', 'blog.posts.delete']);

        $this->assertTrue($user->mayOne($permissionToCheck, $failed));
        $this->assertCount(2, $failed);
        $this->assertSame(['auth.users.delete', 'blog.posts.delete'], $failed->all());
    }

    /** @test */
    public function it_can_check_if_has_all_permissions()
    {
        $adminRole     = $this->createAdminRole();
        $moderatorRole = $this->createModeratorRole();
        $user          = $this->createUser();

        $user->attachRole($adminRole);
        $user->attachRole($moderatorRole);

        $this->assertCount(4, $user->permissions);

        $permissionToCheck = [
            'auth.users.create',
            'auth.users.update',
            'blog.posts.create',
            'blog.posts.update',
        ];

        /** @var  \Illuminate\Support\Collection  $failed */
        $this->assertTrue($user->mayAll($permissionToCheck, $failed));
        $this->assertEmpty($failed);

        $permissionToCheck = array_merge($permissionToCheck, ['auth.users.delete', 'blog.posts.delete']);

        $this->assertFalse($user->mayAll($permissionToCheck, $failed));
        $this->assertCount(2, $failed);
        $this->assertSame(['auth.users.delete', 'blog.posts.delete'], $failed->all());
    }

    /** @test */
    public function it_can_track_last_active_users()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $this->assertNull(auth()->user()->last_activity);

        $this->call('GET', '/');

        $authUser = auth()->user();
        $this->assertNotNull($authUser->last_activity);
        $this->assertInstanceOf(\Carbon\Carbon::class, $authUser->last_activity);

        $users = User::lastActive()->get();

        $this->assertCount(1, $users);
        $this->assertSame($authUser->id, $users->first()->id);
    }

    /* -----------------------------------------------------------------
     |  Helpers
     | -----------------------------------------------------------------
     */
    /**
     * Create a user.
     *
     * @param  array  $attributes
     *
     * @return User
     */
    private function createUser(array $attributes = [])
    {
        if (empty($attributes)) {
            $attributes = $this->getUserAttributes();
        }

        /** @var User $user */
        $user = $this->userModel->create($attributes);

        return $this->userModel->find($user->id);
    }

    /**
     * Get a dummy user attributes.
     *
     * @return array
     */
    private function getUserAttributes()
    {
        return [
            'username'   => 'john.doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ];
    }

    /**
     * Create an admin role.
     *
     * @return Role
     */
    private function createAdminRole()
    {
        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $adminRole->attachPermission(Permission::create([
            'name'  => 'Create users permission',
            'slug'  => 'auth.users.create',
        ]));

        $adminRole->attachPermission(Permission::create([
            'name'  => 'Update users permission',
            'slug'  => 'auth.users.update',
        ]));

        return $adminRole;
    }

    /**
     * Create a moderator role.
     *
     * @return Role
     */
    private function createModeratorRole()
    {
        $moderatorRole = Role::create([
            'name'        => 'Blog Moderator',
            'slug'        => 'blog.moderator',
            'description' => 'Blog Moderator role descriptions.',
        ]);

        $moderatorRole->attachPermission(Permission::create([
            'name'  => 'Create posts permission',
            'slug'  => 'blog.posts.create',
        ]));

        $moderatorRole->attachPermission(Permission::create([
            'name'  => 'Update posts permission',
            'slug'  => 'blog.posts.update',
        ]));

        return $moderatorRole;
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
