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
    public function it_can_prevent_attach_duplicated_roles()
    {
        $role                 = $this->createRole();
        $createUserPermission = Permission::create([
            'name'        => 'Create users',
            'slug'        => 'auth.users.create',
            'description' => 'Create users permission description.',
        ]);

        $this->assertCount(0, $role->permissions);

        foreach (range(0, 5) as $time) {
            $role->attachPermission($createUserPermission);
            $this->assertCount(1, $role->permissions);
            $this->assertTrue($role->hasPermission($createUserPermission));
        }
    }

    /** @test */
    public function it_can_detach_all_roles()
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
