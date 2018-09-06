<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Events\Users as UserEvents;
use Arcanedev\LaravelAuth\Models\Pivots\RoleUser;
use Arcanedev\LaravelAuth\Models\Role;
use Arcanedev\LaravelAuth\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

/**
 * Class     UserTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @todo: Cleaning/Refactoring the event/listeners assertions.
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
        'creating'        => UserEvents\CreatingUser::class,
        'created'         => UserEvents\CreatedUser::class,
        'saving'          => UserEvents\SavingUser::class,
        'saved'           => UserEvents\SavedUser::class,
        'updating'        => UserEvents\UpdatingUser::class,
        'updated'         => UserEvents\UpdatedUser::class,
        'deleting'        => UserEvents\DeletingUser::class,
        'deleted'         => UserEvents\DeletedUser::class,
        'restoring'       => UserEvents\RestoringUser::class,
        'restored'        => UserEvents\RestoredUser::class,

        // Custom events
        'activating'      => UserEvents\ActivatingUser::class,
        'activated'       => UserEvents\ActivatedUser::class,
        'confirming'      => UserEvents\ConfirmingUser::class,
        'confirmed'       => UserEvents\ConfirmedUser::class,
        'syncing-roles'   => UserEvents\SyncingUserWithRoles::class,
        'synced-roles'    => UserEvents\SyncedUserWithRoles::class,
        'attaching-role'  => UserEvents\AttachingRoleToUser::class,
        'attached-role'   => UserEvents\AttachedRoleToUser::class,
        'detaching-role'  => UserEvents\DetachingRoleFromUser::class,
        'detached-role'   => UserEvents\DetachedRoleFromUser::class,
        'detaching-roles' => UserEvents\DetachingRolesFromUser::class,
        'detached-roles'  => UserEvents\DetachedRolesFromUser::class,
    ];

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp()
    {
        parent::setUp();

        $this->userModel = new User;
    }

    public function tearDown()
    {
        unset($this->userModel);

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
            static::assertInstanceOf($expected, $this->userModel);
        }
    }

    /** @test */
    public function it_has_relationships()
    {
        $rolesRelationship = $this->userModel->roles();

        static::assertInstanceOf(BelongsToMany::class, $rolesRelationship);

        /** @var  \Arcanedev\LaravelAuth\Models\Role  $roleModel */
        $roleModel = $rolesRelationship->getRelated();

        static::assertInstanceOf(Role::class, $roleModel);
    }

    /** @test */
    public function it_can_create()
    {
        Event::fake();

        $attributes = $this->getUserAttributes();
        $user       = $this->createUser();

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        static::assertSame($attributes['username'],    $user->username);
        static::assertSame($attributes['first_name'],  $user->first_name);
        static::assertSame($attributes['last_name'],   $user->last_name);
        static::assertSame(
            $attributes['first_name'].' '.$attributes['last_name'], $user->full_name
        );
        static::assertSame($attributes['email'],       $user->email);
        static::assertNotSame($attributes['password'], $user->password);

        static::assertFalse($user->is_admin);
        static::assertFalse($user->isAdmin());
        static::assertFalse($user->isModerator());
        static::assertTrue($user->isMember());
        static::assertFalse($user->is_active);
        static::assertFalse($user->isActive());
        static::assertFalse($user->hasVerifiedEmail());

        static::assertCount(0, $user->roles);
    }

    /** @test */
    public function it_can_activate_and_deactivate()
    {
        Event::fake();

        $attributes = [
            'username'   => 'john.doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ];

        /** @var User $user */
        $user = $this->userModel->newQuery()->create($attributes);

        static::assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $user = $this->userModel->newQuery()->where('id', $user->id)->first();

        static::assertFalse($user->is_active);
        static::assertFalse($user->isActive());

        static::assertTrue($user->activate());

        static::assertFiredEvents(['activating', 'activated', 'updating', 'updated', 'saving', 'saved']);

        static::assertTrue($user->is_active);
        static::assertTrue($user->isActive());

        static::assertTrue($user->deactivate());

        static::assertFiredEvents(['deactivating', 'deactivated', 'updating', 'updated', 'saving', 'saved']);

        static::assertFalse($user->is_active);
        static::assertFalse($user->isActive());
    }

    /** @test */
    public function it_can_attach_and_detach_a_role()
    {
        Event::fake();

        $user = $this->createUser();
        static::assertFiredEvents(['creating', 'crated', 'saving', 'saved']);

        $adminRole     = static::createNewRole([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);
        $moderatorRole = static::createNewRole([
            'name'        => 'Moderator',
            'description' => 'Moderator role descriptions.',
        ]);

        static::assertCount(0, $user->roles);

        $user->attachRole($adminRole);
        static::assertFiredEvents(['attaching-role', 'attached-role']);

        static::assertCount(1, $user->roles);
        static::assertTrue($user->hasRole($adminRole));

        $user->attachRole($moderatorRole);
        static::assertFiredEvents(['attaching-role', 'attached-role']);

        static::assertCount(2, $user->roles);
        static::assertTrue($user->hasRole($adminRole));
        static::assertTrue($user->hasRole($moderatorRole));

        // Assert the pivot table
        foreach ($user->roles as $role) {
            /** @var  \Arcanedev\LaravelAuth\Models\Role  $role */
            static::assertInstanceOf(RoleUser::class, $role->pivot);
            static::assertSame($user->id, $role->pivot->user_id);
            static::assertSame($role->id, $role->pivot->role_id);
        }

        $user->detachRole($adminRole);
        static::assertFiredEvents(['detaching-role', 'detached-role']);

        static::assertCount(1, $user->roles);
        static::assertFalse($user->hasRole($adminRole));
        static::assertTrue($user->hasRole($moderatorRole));

        $user->detachRole($moderatorRole);
        static::assertCount(0, $user->roles);
        static::assertFalse($user->hasRole($adminRole));
        static::assertFalse($user->hasRole($moderatorRole));
    }

    /** @test */
    public function it_must_deny_access_if_role_is_not_active()
    {
        Event::fake();

        $user = $this->createUser();
        static::assertFiredEvents(['creating', 'crated', 'saving', 'saved']);

        $moderatorRole = static::createNewRole([
            'name'        => 'Moderator',
            'description' => 'Moderator role descriptions.',
        ]);

        static::assertCount(0, $user->roles);

        $user->attachRole($moderatorRole);
        static::assertFiredEvents(['attaching-role', 'attached-role']);

        static::assertCount(1, $user->roles);

        static::assertTrue($user->hasRole($moderatorRole));
        static::assertCount(1, $user->active_roles);
        static::assertTrue($user->isOne([$moderatorRole->slug]));

        $moderatorRole->deactivate();

        $user = $user->fresh(['roles']);

        static::assertCount(1, $user->roles);

        static::assertFalse($user->hasRole($moderatorRole));
        static::assertCount(0, $user->active_roles);
        static::assertFalse($user->isOne([$moderatorRole->slug]));
    }

    /** @test */
    public function it_can_prevent_attaching_a_duplicated_role()
    {
        Event::fake();

        $user = $this->createUser();

        static::assertFiredEvents(['creating', 'crated', 'saving', 'saved']);

        $adminRole = static::createNewRole([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);

        static::assertCount(0, $user->roles);

        for ($i = 0; $i < 5; $i++) {
            $user->attachRole($adminRole);
            static::assertFiredEvents(['attaching-role', 'attached-role']);
            static::assertCount(1, $user->roles);
            static::assertTrue($user->hasRole($adminRole));
        }
    }

    /** @test */
    public function it_can_sync_roles_by_its_slugs()
    {
        Event::fake();

        $user  = $this->createUser();
        $roles = new Collection([
            static::createNewRole([
                'name'        => 'Admin',
                'description' => 'Admin role descriptions.',
            ]),
            static::createNewRole([
                'name'        => 'Moderator',
                'description' => 'Moderator role descriptions.',
            ])
        ]);

        static::assertCount(0, $user->roles);

        $synced = $user->syncRoles(
            $roles->pluck('slug')->toArray()
        );

        static::assertCount($roles->count(), $synced['attached']);
        static::assertSame($roles->pluck('id')->toArray(), $synced['attached']);
        static::assertEmpty($synced['detached']);
        static::assertEmpty($synced['updated']);

        Event::assertDispatched(UserEvents\CreatingUser::class);
        Event::assertDispatched(UserEvents\CreatedUser::class);
        Event::assertDispatched(UserEvents\SavingUser::class);
        Event::assertDispatched(UserEvents\SavedUser::class);
        Event::assertDispatched(UserEvents\SyncingUserWithRoles::class);
        Event::assertDispatched(UserEvents\SyncedUserWithRoles::class);
    }

    /** @test */
    public function it_can_detach_all_roles()
    {
        Event::fake();

        $user      = $this->createUser();
        $adminRole = static::createNewRole([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);
        $moderatorRole = static::createNewRole([
            'name'        => 'Moderator',
            'description' => 'Moderator role descriptions.',
        ]);

        static::assertCount(0, $user->roles);

        $user->attachRole($adminRole);
        $user->attachRole($moderatorRole);

        static::assertCount(2, $user->roles);

        $user->detachAllRoles();

        static::assertCount(0, $user->roles);

        Event::assertDispatched(UserEvents\CreatingUser::class);
        Event::assertDispatched(UserEvents\CreatedUser::class);
        Event::assertDispatched(UserEvents\SavingUser::class);
        Event::assertDispatched(UserEvents\SavedUser::class);
        Event::assertDispatched(UserEvents\AttachingRoleToUser::class);
        Event::assertDispatched(UserEvents\AttachedRoleToUser::class);
        Event::assertDispatched(UserEvents\DetachingRolesFromUser::class);
        Event::assertDispatched(UserEvents\DetachedRolesFromUser::class);
    }

    /** @test */
    public function it_can_delete()
    {
        Event::fake();

        $user   = $this->createUser();
        $userId = $user->id;

        Event::assertDispatched(UserEvents\CreatingUser::class);
        Event::assertDispatched(UserEvents\CreatedUser::class);
        Event::assertDispatched(UserEvents\SavingUser::class);
        Event::assertDispatched(UserEvents\SavedUser::class);

        static::assertFalse($user->trashed());
        static::assertTrue($user->delete());

        Event::assertDispatched(UserEvents\DeletingUser::class);
        Event::assertDispatched(UserEvents\DeletedUser::class);

        static::assertNull($this->userModel->newQuery()->find($userId));

        /** @var  \Arcanedev\LaravelAuth\Models\User  $user */
        $user = $this->userModel->newQuery()->onlyTrashed()->find($userId);

        static::assertTrue($user->trashed());

        $user->forceDelete();

        Event::assertDispatched(UserEvents\DeletingUser::class);
        Event::assertDispatched(UserEvents\DeletedUser::class);

        $user = $this->userModel->newQuery()->find($userId);

        static::assertNull($user);
    }

    /** @test */
    public function it_must_detach_all_relations_on_user_force_delete()
    {
        Event::fake();

        $user = $this->createUser();
        $role = static::createNewRole([
            'name'        => 'Member',
            'slug'        => 'member',
            'description' => 'Member role descriptions.',
        ]);

        $user->attachRole($role);

        static::assertSame(1, $role->users()->count());

        $user->forceDelete();

        Event::assertDispatched(UserEvents\DeletingUser::class, function ($e) {
            return (new \Arcanedev\LaravelAuth\Listeners\Users\DetachingRoles)->handle($e) === true;
        });
        Event::assertDispatched(UserEvents\DeletedUser::class);

        static::assertSame(0, $role->users()->count());
    }

    /** @test */
    public function it_can_not_delete_admin()
    {
        Event::fake();

        $user = $this->createUser();

        Event::assertDispatched(UserEvents\CreatingUser::class);
        Event::assertDispatched(UserEvents\CreatedUser::class);
        Event::assertDispatched(UserEvents\SavingUser::class);
        Event::assertDispatched(UserEvents\SavedUser::class);

        $user->is_admin = true;
        $user->save();

        Event::assertDispatched(UserEvents\SavingUser::class);
        Event::assertDispatched(UserEvents\SavedUser::class);
        Event::assertDispatched(UserEvents\UpdatingUser::class);
        Event::assertDispatched(UserEvents\UpdatedUser::class);

        static::assertTrue($user->isAdmin());
        static::assertFalse($user->isModerator());
        static::assertFalse($user->isMember());
        static::assertFalse($user->trashed());

        $user->delete();

        Event::assertDispatched(UserEvents\DeletingUser::class, function ($e) {
            return (new \Arcanedev\LaravelAuth\Listeners\Users\DetachingRoles)->handle($e) === false;
        });
        Event::assertDispatched(UserEvents\DeletedUser::class);
    }

    /** @test */
    public function it_can_restore()
    {
        Event::fake();

        $user = $this->createUser();

        Event::assertDispatched(UserEvents\CreatingUser::class);
        Event::assertDispatched(UserEvents\CreatedUser::class);
        Event::assertDispatched(UserEvents\SavingUser::class);
        Event::assertDispatched(UserEvents\SavedUser::class);

        $userId = $user->id;

        static::assertFalse($user->trashed());
        static::assertTrue($user->delete());

        Event::assertDispatched(UserEvents\DeletingUser::class);
        Event::assertDispatched(UserEvents\DeletedUser::class);

        $user = $this->userModel->newQuery()->find($userId);

        static::assertNull($user);

        /** @var  \Arcanedev\LaravelAuth\Models\User  $user */
        $user = $this->userModel->newQuery()->onlyTrashed()->find($userId);

        static::assertTrue($user->trashed());

        $user->restore();

        Event::assertDispatched(UserEvents\RestoringUser::class);
        Event::assertDispatched(UserEvents\RestoredUser::class);

        $user = $this->userModel->newQuery()->find($userId);

        static::assertNotNull($user);
        static::assertFalse($user->trashed());
    }

    /** @test */
    public function it_can_check_has_same_role()
    {
        $user      = $this->createUser();

        static::assertFalse($user->hasRoleSlug('admin'));

        $adminRole = static::createNewRole([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $user->attachRole($adminRole);

        static::assertTrue($user->hasRoleSlug('Admin'));
        static::assertTrue($user->hasRoleSlug('admin'));
    }

    /** @test */
    public function it_can_check_has_any_role()
    {
        $user = $this->createUser();

        /** @var  \Illuminate\Support\Collection  $failedRoles */
        static::assertFalse($user->isOne(['admin', 'member'], $failedRoles));
        static::assertCount(2, $failedRoles);
        static::assertSame(['admin', 'member'], $failedRoles->all());

        $adminRole = static::createNewRole([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $user->attachRole($adminRole);

        $failedRoles = [];

        static::assertTrue($user->isOne(['admin', 'member'], $failedRoles));
        static::assertCount(1, $failedRoles);
        static::assertSame(['member'], $failedRoles->all());
    }

    /** @test */
    public function it_can_check_has_all_roles()
    {
        $user = $this->createUser();

        /** @var  \Illuminate\Support\Collection  $failedRoles */
        static::assertFalse($user->isAll(['admin', 'member'], $failedRoles));
        static::assertCount(2, $failedRoles);
        static::assertSame(['admin', 'member'], $failedRoles->all());

        $adminRole = static::createNewRole([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $user->attachRole($adminRole);

        static::assertFalse($user->isAll(['admin', 'member'], $failedRoles));
        static::assertCount(1, $failedRoles);
        static::assertSame(['member'], $failedRoles->all());

        $memberRole = static::createNewRole([
            'name'        => 'Member',
            'slug'        => 'member',
            'description' => 'Member role descriptions.',
        ]);

        $user->attachRole($memberRole);

        static::assertTrue($user->isAll(['admin', 'member'], $failedRoles));
        static::assertEmpty($failedRoles);
    }

    /** @test */
    public function it_can_check_if_has_permission()
    {
        $adminRole     = $this->createAdminRole();
        $moderatorRole = $this->createModeratorRole();
        $user          = $this->createUser();

        $user->attachRole($adminRole);
        $user->attachRole($moderatorRole);

        static::assertCount(4, $user->permissions);

        static::assertTrue($user->may('auth.users.create'));
        static::assertTrue($user->may('auth.users.update'));
        static::assertTrue($user->may('blog.posts.create'));
        static::assertTrue($user->may('blog.posts.update'));
    }

    /** @test */
    public function it_can_check_if_has_one_of_permissions()
    {
        $adminRole     = $this->createAdminRole();
        $moderatorRole = $this->createModeratorRole();
        $user          = $this->createUser();

        $user->attachRole($adminRole);
        $user->attachRole($moderatorRole);

        static::assertCount(4, $user->permissions);

        $permissionToCheck = [
            'auth.users.create',
            'auth.users.update',
            'blog.posts.create',
            'blog.posts.update',
        ];

        /** @var  \Illuminate\Support\Collection  $failed */
        static::assertTrue($user->mayOne($permissionToCheck, $failed));
        static::assertEmpty($failed);

        $permissionToCheck = array_merge($permissionToCheck, ['auth.users.delete', 'blog.posts.delete']);

        static::assertTrue($user->mayOne($permissionToCheck, $failed));
        static::assertCount(2, $failed);
        static::assertSame(['auth.users.delete', 'blog.posts.delete'], $failed->all());
    }

    /** @test */
    public function it_can_check_if_has_all_permissions()
    {
        $adminRole     = $this->createAdminRole();
        $moderatorRole = $this->createModeratorRole();
        $user          = $this->createUser();

        $user->attachRole($adminRole);
        $user->attachRole($moderatorRole);

        static::assertCount(4, $user->permissions);

        $permissionToCheck = [
            'auth.users.create',
            'auth.users.update',
            'blog.posts.create',
            'blog.posts.update',
        ];

        /** @var  \Illuminate\Support\Collection  $failed */
        static::assertTrue($user->mayAll($permissionToCheck, $failed));
        static::assertEmpty($failed);

        $permissionToCheck = array_merge($permissionToCheck, ['auth.users.delete', 'blog.posts.delete']);

        static::assertFalse($user->mayAll($permissionToCheck, $failed));
        static::assertCount(2, $failed);
        static::assertSame(['auth.users.delete', 'blog.posts.delete'], $failed->all());
    }

    /** @test */
    public function it_can_scope_verified_emails()
    {
        $user = $this->createUser();

        static::assertSame(1, User::unverifiedEmail()->count());
        static::assertSame(0, User::verifiedEmail()->count());

        $user->markEmailAsVerified();

        static::assertSame(0, User::unverifiedEmail()->count());
        static::assertSame(1, User::verifiedEmail()->count());
    }

    /** @test */
    public function it_can_track_last_active_users()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        static::assertNull(auth()->user()->last_activity);

        $this->call('GET', '/');

        $authUser = auth()->user();
        static::assertNotNull($authUser->last_activity);
        static::assertInstanceOf(\Carbon\Carbon::class, $authUser->last_activity);

        $users = User::lastActive()->get();

        static::assertCount(1, $users);
        static::assertSame($authUser->id, $users->first()->id);
    }

    /** @test */
    public function it_must_sanitize_user_name_with_mutator()
    {
        // First name
        $firstNames = ['john', 'JOHN', 'JoHn', 'jOhN'];
        $expected   = 'John';

        foreach ($firstNames as $firstName) {
            $user = $this->userModel->fill(['first_name' => $firstName]);

            static::assertSame($expected, $user->first_name);
        }

        $firstNames = ['john oliver', 'JOHN OLIVER', 'JoHn OlIvEr', 'jOhN oLiVeR'];
        $expected   = 'John Oliver';

        foreach ($firstNames as $firstName) {
            $user = $this->userModel->fill(['first_name' => $firstName]);

            static::assertSame($expected, $user->first_name);
        }

        // Last name
        $lastNames = ['doe', 'Doe', 'DOE', 'DoE'];
        $expected  = 'DOE';

        foreach ($lastNames as $lastName) {
            $user = $this->userModel->fill(['last_name' => $lastName]);

            static::assertSame($expected, $user->last_name);
        }

        // Full name
        $names = [
            ['john', 'doe'],
            ['JOHN', 'Doe'],
            ['JoHn', 'DOE'],
            ['jOhN', 'DoE'],
        ];
        $expected  = 'John DOE';

        foreach ($names as $name) {
            $user = $this->userModel->fill([
                'first_name' => $name[0],
                'last_name'  => $name[1],
            ]);

            static::assertSame($expected, $user->full_name);
        }
    }

    /** @test */
    public function it_must_sanitize_user_email_with_mutator()
    {
        $emails   = [
            'user@example.com', 'USER@example.com', 'user@EXAMPLE.com', 'user@example.COM',
            'UsEr@ExAmPlE.CoM', 'uSeR@eXaMpLe.cOm', 'USER@EXAMPLE.COM',
        ];
        $expected = 'user@example.com';

        foreach ($emails as $email) {
            $user = $this->userModel->fill(['email' => $email]);

            static::assertSame($expected, $user->email);
        }
    }

    /** @test */
    public function it_can_be_impersonated()
    {
        $user = $this->createUser();

        static::assertTrue($user->canBeImpersonated());

        $user->forceFill(['is_admin' => true])->save();

        static::assertFalse($user->canBeImpersonated());
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
     * @return \Arcanedev\LaravelAuth\Models\User|mixed
     */
    private function createUser(array $attributes = [])
    {
        if (empty($attributes))
            $attributes = $this->getUserAttributes();

        /** @var User $user */
        $user = $this->userModel->forceCreate($attributes);

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
        $adminRole = static::createNewRole([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $adminRole->attachPermission(static::createNewPermission([
            'name' => 'Create users permission',
            'slug' => 'auth.users.create',
        ]));

        $adminRole->attachPermission(static::createNewPermission([
            'name' => 'Update users permission',
            'slug' => 'auth.users.update',
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
        $moderatorRole = static::createNewRole([
            'name'        => 'Blog Moderator',
            'slug'        => 'blog.moderator',
            'description' => 'Blog Moderator role descriptions.',
        ]);

        $moderatorRole->attachPermission(
            static::createNewPermission([
                'name'  => 'Create posts permission',
                'slug'  => 'blog.posts.create',
            ])
        );

        $moderatorRole->attachPermission(
            static::createNewPermission([
                'name'  => 'Update posts permission',
                'slug'  => 'blog.posts.update',
            ])
        );

        return $moderatorRole;
    }

    /**
     * @param  User  $user
     */
    protected static function assertConfirmationCodeGenerationListener($user)
    {
        Event::assertDispatched(UserEvents\CreatingUser::class, function (UserEvents\CreatingUser $e) use ($user) {
            static::assertSame($e->user->id, $user->id);
            static::assertNull($e->user->confirmation_code);

            (new \Arcanedev\LaravelAuth\Listeners\Users\GenerateConfirmationCode)->handle($e);

            static::assertSame($e->user->id, $user->id);
            static::assertNotNull($e->user->confirmation_code);

            $user = $e->user;
            $user->save(); // Save the generated code

            return ! is_null($e->user->confirmation_code);
        });
    }
}
