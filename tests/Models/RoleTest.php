<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\Role;
use Arcanedev\LaravelAuth\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class     RoleTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RoleTest extends ModelsTest
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var Role */
    protected $roleModel;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
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

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    /** @test */
    public function it_can_be_instantiated()
    {
        $expectations = [
            \Illuminate\Database\Eloquent\Model::class,
            \Arcanedev\LaravelAuth\Contracts\Role::class,
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
         * @var  User        $user
         * @var  Permission  $permission
         */
        $user       = $usersRelationship->getRelated();
        $permission = $permissionsRelationship->getRelated();

        $this->assertInstanceOf(User::class,       $user);
        $this->assertInstanceOf(Permission::class, $permission);
    }

    /** @test */
    public function it_can_create()
    {
        $attributes = [
            'name'        => 'Custom role',
            'slug'        => str_slug('Custom role', config('laravel-auth.slug-separator')),
            'description' => 'Custom role descriptions.',
        ];

        $role = $this->createRole($attributes);

        $this->assertEquals($attributes['name'],        $role->name);
        $this->assertEquals($attributes['slug'],        $role->slug);
        $this->assertEquals($attributes['description'], $role->description);
        $this->assertTrue($role->is_active);
        $this->assertTrue($role->isActive());
        $this->assertFalse($role->is_locked);
        $this->assertFalse($role->isLocked());
    }

    /** @test */
    public function it_can_update()
    {
        $attributes = $this->getAdminRoleAttributes();

        $role = $this->createRole();

        $this->seeInDatabase('roles', $attributes);
        $this->seeInDatabase('roles', $role->toArray());

        $this->assertTrue($role->is_active);
        $this->assertTrue($role->isActive());
        $this->assertFalse($role->is_locked);
        $this->assertFalse($role->isLocked());

        $updatedAttributes = [
            'name'        => 'Custom role',
            'description' => 'Custom role descriptions.',
        ];

        $role->update($updatedAttributes);

        $this->dontSeeInDatabase('roles', $attributes);
        $this->seeInDatabase('roles', $updatedAttributes);
        $this->seeInDatabase('roles', $role->toArray());

        $this->assertTrue($role->is_active);
        $this->assertTrue($role->isActive());
        $this->assertFalse($role->is_locked);
        $this->assertFalse($role->isLocked());
    }

    /** @test */
    public function it_can_delete()
    {
        $role = $this->createRole();

        $this->seeInDatabase('roles', $role->toArray());

        $role->delete();

        $this->dontSeeInDatabase('roles', $role->toArray());
    }

    /** @test */
    public function it_can_attach_and_detach_user()
    {
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

        $role->detachAllUsers();

        $this->assertCount(0, $role->users);
        $this->assertFalse($role->hasUser($admin));
        $this->assertFalse($role->hasUser($member));
    }

    /** @test */
    public function it_can_attach_and_detach_permission()
    {
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
        $role                 = $this->createRole();

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
        $role               = $this->createRole();
        $failedPermissions  = [];
        $permissionsToCheck = [
            'auth.users.create',
            'auth.users.update',
            'auth.users.delete',
        ];

        $this->assertFalse($role->canAny($permissionsToCheck, $failedPermissions));
        $this->assertCount(3, $failedPermissions);
        $this->assertEquals($permissionsToCheck, $failedPermissions);

        $createUserPermission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);

        $role->attachPermission($createUserPermission);

        $failedPermissions  = [];

        $this->assertTrue($role->canAny($permissionsToCheck, $failedPermissions));
        $this->assertCount(2, $failedPermissions);
        $this->assertEquals([
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
        $this->assertEquals(['auth.users.delete'], $failedPermissions);

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
        $role               = $this->createRole();
        $failedPermissions  = [];
        $permissionsToCheck = [
            'auth.users.create',
            'auth.users.update',
            'auth.users.delete',
        ];

        $this->assertFalse($role->canAll($permissionsToCheck, $failedPermissions));
        $this->assertCount(3, $failedPermissions);
        $this->assertEquals($permissionsToCheck, $failedPermissions);

        $createUserPermission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);

        $role->attachPermission($createUserPermission);

        $failedPermissions  = [];

        $this->assertFalse($role->canAll($permissionsToCheck, $failedPermissions));
        $this->assertCount(2, $failedPermissions);
        $this->assertEquals([
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
        $this->assertEquals(['auth.users.delete'], $failedPermissions);

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

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
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
}
