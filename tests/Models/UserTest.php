<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Models\User;
use Arcanedev\LaravelAuth\Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class     UserTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UserTest extends TestCase
{
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
}
