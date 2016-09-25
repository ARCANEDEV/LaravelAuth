<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Models\Permission;
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
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var User */
    protected $userModel;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
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

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
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
            \Arcanedev\LaravelAuth\Bases\User::class,
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
        $attributes = $this->getUserAttributes();
        $user       = $this->createUser();

        $this->assertEquals($attributes['username'],    $user->username);
        $this->assertEquals($attributes['first_name'],  $user->first_name);
        $this->assertEquals($attributes['last_name'],   $user->last_name);
        $this->assertEquals(
            $attributes['first_name'] . ' ' . $attributes['last_name'], $user->full_name
        );
        $this->assertEquals($attributes['email'],       $user->email);
        $this->assertNotEquals($attributes['password'], $user->password);

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
        $user          = $this->createUser();
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
        $this->assertCount(1, $user->roles);
        $this->assertTrue($user->hasRole($adminRole));

        $user->attachRole($moderatorRole);
        $this->assertCount(2, $user->roles);
        $this->assertTrue($user->hasRole($adminRole));
        $this->assertTrue($user->hasRole($moderatorRole));

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
        $user          = $this->createUser();
        $adminRole     = Role::create([
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
    public function it_can_detach_all_roles()
    {
        $user          = $this->createUser();
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
        $user->attachRole($moderatorRole);

        $this->assertCount(2, $user->roles);

        $user->detachAllRoles();

        $this->assertCount(0, $user->roles);
    }

    /** @test */
    public function it_can_find_an_unconfirmed_user()
    {
        $user            = $this->createUser();
        $unconfirmedUser = $this->userModel->findUnconfirmed($user->confirmation_code);

        $this->assertEquals($user, $unconfirmedUser);
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

            $this->assertEquals('Unconfirmed user was not found.', $e->getMessage());
        }
    }

    /** @test */
    public function it_can_confirm()
    {
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
        $user         = $this->createUser();

        $failedRoles  = [];
        $this->assertFalse($user->isOne(['admin', 'member'], $failedRoles));
        $this->assertCount(2, $failedRoles);
        $this->assertEquals(['admin', 'member'], $failedRoles);

        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $user->attachRole($adminRole);

        $failedRoles = [];
        $this->assertTrue($user->isOne(['admin', 'member'], $failedRoles));
        $this->assertCount(1, $failedRoles);
        $this->assertEquals(['member'], $failedRoles);
    }

    /** @test */
    public function it_can_check_has_all_roles()
    {
        $user      = $this->createUser();

        $failedRoles  = [];
        $this->assertFalse($user->isAll(['admin', 'member'], $failedRoles));
        $this->assertCount(2, $failedRoles);
        $this->assertEquals(['admin', 'member'], $failedRoles);

        $adminRole = Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => 'Admin role descriptions.',
        ]);

        $user->attachRole($adminRole);

        $failedRoles = [];
        $this->assertFalse($user->isAll(['admin', 'member'], $failedRoles));
        $this->assertCount(1, $failedRoles);
        $this->assertEquals(['member'], $failedRoles);

        $memberRole = Role::create([
            'name'        => 'Member',
            'slug'        => 'member',
            'description' => 'Member role descriptions.',
        ]);

        $user->attachRole($memberRole);

        $failedRoles = [];
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

        $failedPermissions = [];
        $permissionToCheck = [
            'auth.users.create', 'auth.users.update', 'blog.posts.create', 'blog.posts.update'
        ];

        $this->assertTrue($user->mayOne($permissionToCheck, $failedPermissions));
        $this->assertEmpty($failedPermissions);

        $permissionToCheck = array_merge($permissionToCheck, ['auth.users.delete', 'blog.posts.delete']);

        $this->assertTrue($user->mayOne($permissionToCheck, $failedPermissions));
        $this->assertCount(2, $failedPermissions);
        $this->assertEquals($failedPermissions, ['auth.users.delete', 'blog.posts.delete']);
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

        $failedPermissions = [];
        $permissionToCheck = [
            'auth.users.create', 'auth.users.update', 'blog.posts.create', 'blog.posts.update'
        ];

        $this->assertTrue($user->mayAll($permissionToCheck, $failedPermissions));
        $this->assertEmpty($failedPermissions);

        $permissionToCheck = array_merge($permissionToCheck, ['auth.users.delete', 'blog.posts.delete']);

        $this->assertFalse($user->mayAll($permissionToCheck, $failedPermissions));
        $this->assertCount(2, $failedPermissions);
        $this->assertEquals($failedPermissions, ['auth.users.delete', 'blog.posts.delete']);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
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
}
