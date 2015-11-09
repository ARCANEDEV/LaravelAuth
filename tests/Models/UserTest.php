<?php namespace Arcanedev\LaravelAuth\Tests\Models;

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
            \Illuminate\Database\Eloquent\Model::class,
            \Arcanedev\LaravelAuth\Contracts\User::class,
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
    public function it_can_create_a_user()
    {
        $attributes = $this->getUserAttributes();
        $user       = $this->createUser();

        $this->assertEquals($attributes['username'],    $user->username);
        $this->assertEquals($attributes['first_name'],  $user->first_name);
        $this->assertEquals($attributes['last_name'],   $user->last_name);
        $this->assertEquals($attributes['email'],       $user->email);
        $this->assertNotEquals($attributes['password'], $user->password);

        $this->assertFalse($user->is_admin);
        $this->assertFalse($user->isAdmin());
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
            'username'   => 'john-doe',
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

        $this->assertCount(0, $user->roles);

        $adminRole     = Role::create([
            'name'        => 'Admin',
            'description' => 'Admin role descriptions.',
        ]);
        $moderatorRole = Role::create([
            'name'        => 'Moderator',
            'description' => 'Moderator role descriptions.',
        ]);

        $user->attachRole($adminRole);
        $this->assertCount(1, $user->roles);

        $user->attachRole($moderatorRole);
        $this->assertCount(2, $user->roles);

        $user->detachRole($adminRole);
        $this->assertCount(1, $user->roles);

        $user->detachRole($moderatorRole);
        $this->assertCount(0, $user->roles);

        $user->attachRole($adminRole);
        $this->assertCount(1, $user->roles);

        $user->attachRole($adminRole);       // Prevent the duplication
        $this->assertCount(1, $user->roles);
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
            'username'   => 'john-doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ];
    }
}
