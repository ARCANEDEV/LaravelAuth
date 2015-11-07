<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Models\Role;
use Arcanedev\LaravelAuth\Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class     RoleTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class RoleTest extends TestCase
{
    /** @var Role */
    protected $role;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $this->role = new Role;
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($this->role);
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
            $this->assertInstanceOf($expected, $this->role);
        }
    }

    /** @test */
    public function it_has_relationships()
    {
        $usersRelationship       = $this->role->users();
        $permissionsRelationship = $this->role->permissions();

        $this->assertInstanceOf(BelongsToMany::class, $usersRelationship);
        $this->assertInstanceOf(BelongsToMany::class, $permissionsRelationship);

        /**
         * @var  \Arcanedev\LaravelAuth\Models\User        $user
         * @var  \Arcanedev\LaravelAuth\Models\Permission  $permission
         */
        $user       = $usersRelationship->getRelated();
        $permission = $permissionsRelationship->getRelated();

        $this->assertInstanceOf(\Arcanedev\LaravelAuth\Models\User::class,       $user);
        $this->assertInstanceOf(\Arcanedev\LaravelAuth\Models\Permission::class, $permission);
    }
}
