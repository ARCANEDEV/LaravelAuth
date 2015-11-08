<?php namespace Arcanedev\LaravelAuth\Tests\Models;

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
    protected $user;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $this->user = new User;
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($this->user);
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
            $this->assertInstanceOf($expected, $this->user);
        }
    }

    /** @test */
    public function it_has_relationships()
    {
        $rolesRelationship = $this->user->roles();

        $this->assertInstanceOf(BelongsToMany::class, $rolesRelationship);

        /** @var  \Arcanedev\LaravelAuth\Models\Role  $role */
        $role = $rolesRelationship->getRelated();

        $this->assertInstanceOf(\Arcanedev\LaravelAuth\Models\Role::class, $role);
    }

    /** @test */
    public function it_can_create_a_user()
    {
        $attributes = [
            'username'   => 'john-doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ];

        /** @var User $user */
        $user = $this->user->create($attributes);
        $user = $this->user->where('id', $user->id)->first();

        $this->assertEquals($attributes['username'],         $user->username);
        $this->assertEquals($attributes['first_name'],       $user->first_name);
        $this->assertEquals($attributes['last_name'],        $user->last_name);
        $this->assertEquals($attributes['email'],            $user->email);
        $this->assertNotEquals($attributes['password'],      $user->password);

        $this->assertFalse($user->is_active);
        $this->assertFalse($user->isActive());
        $this->assertFalse($user->is_confirmed);
        $this->assertFalse($user->isConfirmed());
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
        $user = $this->user->create($attributes);
        $user = $this->user->where('id', $user->id)->first();

        $this->assertFalse($user->is_active);
        $this->assertFalse($user->isActive());

        $this->assertTrue($user->activate());
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->isActive());

        $this->assertTrue($user->deactivate());
        $this->assertFalse($user->is_active);
        $this->assertFalse($user->isActive());
    }
}
