<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Events\Users as UserEvents;
use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\Pivots\RoleUser;
use Arcanedev\LaravelAuth\Models\Role;
use Arcanedev\LaravelAuth\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        Event::fake();

        $attributes = $this->getUserAttributes();
        $user       = $this->createUser();

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

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
        Event::fake();

        $attributes = [
            'username'   => 'john.doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ];

        /** @var User $user */
        $user = $this->userModel->create($attributes);

        $this->assertFiredEvents(['creating', 'created', 'saving', 'saved']);

        $user = $this->userModel->where('id', $user->id)->first();

        $this->assertFalse($user->is_active);
        $this->assertFalse($user->isActive());

        $this->assertTrue($user->activate());

        $this->assertFiredEvents(['activating', 'activated', 'updating', 'updated', 'saving', 'saved']);

        $this->assertTrue($user->is_active);
        $this->assertTrue($user->isActive());

        $this->assertTrue($user->deactivate());

        $this->assertFiredEvents(['deactivating', 'deactivated', 'updating', 'updated', 'saving', 'saved']);

        $this->assertFalse($user->is_active);
        $this->assertFalse($user->isActive());
    }

    /** @test */
    public function it_can_attach_and_detach_a_role()
    {
        Event::fake();

        $user = $this->createUser();
        $this->assertFiredEvents(['creating', 'crated', 'saving', 'saved']);

        $adminRole     = Role::create([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);
        $moderatorRole = Role::create([
            'name'        => 'Moderator',
            'description' => 'Moderator role descriptions.',
        ]);

        $this->assertCount(0, $user->roles);

        $user->attachRole($adminRole);
        $this->assertFiredEvents(['attaching-role', 'attached-role']);

        $this->assertCount(1, $user->roles);
        $this->assertTrue($user->hasRole($adminRole));

        $user->attachRole($moderatorRole);
        $this->assertFiredEvents(['attaching-role', 'attached-role']);

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
        $this->assertFiredEvents(['detaching-role', 'detached-role']);

        $this->assertCount(1, $user->roles);
        $this->assertFalse($user->hasRole($adminRole));
        $this->assertTrue($user->hasRole($moderatorRole));

        $user->detachRole($moderatorRole);
        $this->assertCount(0, $user->roles);
        $this->assertFalse($user->hasRole($adminRole));
        $this->assertFalse($user->hasRole($moderatorRole));
    }

    /** @test */
    public function it_must_deny_access_if_role_is_not_active()
    {
        Event::fake();

        $user = $this->createUser();
        $this->assertFiredEvents(['creating', 'crated', 'saving', 'saved']);

        $moderatorRole = Role::create([
            'name'        => 'Moderator',
            'description' => 'Moderator role descriptions.',
        ]);

        $this->assertCount(0, $user->roles);

        $user->attachRole($moderatorRole);
        $this->assertFiredEvents(['attaching-role', 'attached-role']);

        $this->assertCount(1, $user->roles);

        $this->assertTrue($user->hasRole($moderatorRole));
        $this->assertCount(1, $user->active_roles);
        $this->assertTrue($user->isOne([$moderatorRole->slug]));

        $moderatorRole->deactivate();

        $user = $user->fresh(['roles']);

        $this->assertCount(1, $user->roles);

        $this->assertFalse($user->hasRole($moderatorRole));
        $this->assertCount(0, $user->active_roles);
        $this->assertFalse($user->isOne([$moderatorRole->slug]));
    }

    /** @test */
    public function it_can_prevent_attaching_a_duplicated_role()
    {
        Event::fake();

        $user      = $this->createUser();

        $this->assertFiredEvents(['creating', 'crated', 'saving', 'saved']);

        $adminRole = Role::create([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);

        $this->assertCount(0, $user->roles);

        for ($i = 0; $i < 5; $i++) {
            $user->attachRole($adminRole);
            $this->assertFiredEvents(['attaching-role', 'attached-role']);
            $this->assertCount(1, $user->roles);
            $this->assertTrue($user->hasRole($adminRole));
        }
    }

    /** @test */
    public function it_can_sync_roles_by_its_slugs()
    {
        Event::fake();

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
        Event::fake();

        $user = $this->createUser();

        $this->assertConfirmationCodeGenerationListener($user);

        $this->assertFalse($user->is_confirmed);
        $this->assertFalse($user->isConfirmed());
        $this->assertNull($user->confirmed_at);

        Event::assertDispatched(UserEvents\CreatedUser::class);
        Event::assertDispatched(UserEvents\SavingUser::class);
        Event::assertDispatched(UserEvents\SavedUser::class);

        $user = $this->userModel->confirm($user);

        $this->assertTrue($user->is_confirmed);
        $this->assertTrue($user->isConfirmed());
        $this->assertNull($user->confirmation_code);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->confirmed_at);

        Event::assertDispatched(UserEvents\ConfirmingUser::class);
        Event::assertDispatched(UserEvents\ConfirmedUser::class);
        Event::assertDispatched(UserEvents\UpdatingUser::class);
        Event::assertDispatched(UserEvents\UpdatedUser::class);
        Event::assertDispatched(UserEvents\SavingUser::class);
        Event::assertDispatched(UserEvents\SavedUser::class);
    }

    /** @test */
    public function it_can_confirm_by_code()
    {
        Event::fake();

        $user = $this->createUser();

        $this->assertConfirmationCodeGenerationListener($user);

        $this->assertFalse($user->is_confirmed);
        $this->assertFalse($user->isConfirmed());
        $this->assertNull($user->confirmed_at);

        Event::assertDispatched(UserEvents\CreatedUser::class);
        Event::assertDispatched(UserEvents\SavingUser::class);
        Event::assertDispatched(UserEvents\SavedUser::class);

        $user = $this->userModel->confirm($user->confirmation_code);

        $this->assertTrue($user->is_confirmed);
        $this->assertTrue($user->isConfirmed());
        $this->assertNull($user->confirmation_code);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->confirmed_at);

        Event::assertDispatched(UserEvents\ConfirmingUser::class);
        Event::assertDispatched(UserEvents\ConfirmedUser::class);
        Event::assertDispatched(UserEvents\UpdatingUser::class);
        Event::assertDispatched(UserEvents\UpdatedUser::class);
        Event::assertDispatched(UserEvents\SavingUser::class);
        Event::assertDispatched(UserEvents\SavedUser::class);
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

        $this->assertFalse($user->trashed());
        $this->assertTrue($user->delete());

        Event::assertDispatched(UserEvents\DeletingUser::class);
        Event::assertDispatched(UserEvents\DeletedUser::class);

        $this->assertNull($this->userModel->find($userId));

        /** @var User $user */
        $user = $this->userModel->onlyTrashed()->find($userId);

        $this->assertTrue($user->trashed());

        $user->forceDelete();

        Event::assertDispatched(UserEvents\DeletingUser::class);
        Event::assertDispatched(UserEvents\DeletedUser::class);

        $user = $this->userModel->find($userId);

        $this->assertNull($user);
    }

    /** @test */
    public function it_must_detach_all_relations_on_user_force_delete()
    {
        Event::fake();

        $user = $this->createUser();
        $role = Role::query()->create([
            'name'        => 'Member',
            'slug'        => 'member',
            'description' => 'Member role descriptions.',
        ]);

        $user->attachRole($role);

        $this->assertSame(1, $role->users()->count());

        $user->forceDelete();

        Event::assertDispatched(UserEvents\DeletingUser::class);
        Event::assertDispatched(UserEvents\DeletedUser::class);
        $this->assertSame(0, $role->users()->count());
    }

    /** @test */
    public function it_can_not_delete_admin()
    {
        Event::fake();

        $user           = $this->createUser();

        Event::assertDispatched(UserEvents\CreatingUser::class);
        Event::assertDispatched(UserEvents\CreatedUser::class);
        Event::assertDispatched(UserEvents\SavingUser::class);
        Event::assertDispatched(UserEvents\SavedUser::class);

        $adminId        = $user->id;
        $user->is_admin = true;
        $user->save();

        Event::assertDispatched(UserEvents\SavingUser::class);
        Event::assertDispatched(UserEvents\SavedUser::class);
        Event::assertDispatched(UserEvents\UpdatingUser::class);
        Event::assertDispatched(UserEvents\UpdatedUser::class);

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isModerator());
        $this->assertFalse($user->isMember());
        $this->assertFalse($user->trashed());

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

        $user   = $this->createUser();

        Event::assertDispatched(UserEvents\CreatingUser::class);
        Event::assertDispatched(UserEvents\CreatedUser::class);
        Event::assertDispatched(UserEvents\SavingUser::class);
        Event::assertDispatched(UserEvents\SavedUser::class);

        $userId = $user->id;

        $this->assertFalse($user->trashed());
        $this->assertTrue($user->delete());

        Event::assertDispatched(UserEvents\DeletingUser::class);
        Event::assertDispatched(UserEvents\DeletedUser::class);

        $user = $this->userModel->find($userId);

        $this->assertNull($user);

        /** @var User $user */
        $user = $this->userModel->onlyTrashed()->find($userId);

        $this->assertTrue($user->trashed());

        $user->restore();

        Event::assertDispatched(UserEvents\RestoringUser::class);
        Event::assertDispatched(UserEvents\RestoredUser::class);

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

    /** @test */
    public function it_must_sanitize_user_name_with_mutator()
    {
        // First name
        $firstNames = ['john', 'JOHN', 'JoHn', 'jOhN'];
        $expected   = 'John';

        foreach ($firstNames as $firstName) {
            $user = $this->userModel->fill(['first_name' => $firstName]);

            $this->assertSame($expected, $user->first_name);
        }

        $firstNames = ['john oliver', 'JOHN OLIVER', 'JoHn OlIvEr', 'jOhN oLiVeR'];
        $expected   = 'John Oliver';

        foreach ($firstNames as $firstName) {
            $user = $this->userModel->fill(['first_name' => $firstName]);

            $this->assertSame($expected, $user->first_name);
        }

        // Last name
        $lastNames = ['doe', 'Doe', 'DOE', 'DoE'];
        $expected  = 'DOE';

        foreach ($lastNames as $lastName) {
            $user = $this->userModel->fill(['last_name' => $lastName]);

            $this->assertSame($expected, $user->last_name);
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

            $this->assertSame($expected, $user->full_name);
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

            $this->assertSame($expected, $user->email);
        }
    }

    /** @test */
    public function it_can_be_impersonated()
    {
        $user = $this->createUser();

        $this->assertTrue($user->canBeImpersonated());

        $user->forceFill(['is_admin' => true])->save();

        $this->assertFalse($user->canBeImpersonated());
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
     * @param  User  $user
     */
    private function assertConfirmationCodeGenerationListener($user)
    {
        Event::assertDispatched(UserEvents\CreatingUser::class, function (UserEvents\CreatingUser $e) use ($user) {
            $this->assertSame($e->user->id, $user->id);
            $this->assertNull($e->user->confirmation_code);

            (new \Arcanedev\LaravelAuth\Listeners\Users\GenerateConfirmationCode)->handle($e);

            return ! is_null($e->user->confirmation_code);
        });
    }
}
