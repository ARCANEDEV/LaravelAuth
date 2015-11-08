<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class     PermissionTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PermissionTest extends ModelsTest
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var Permission */
    protected $permission;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $this->migrate();

        $this->permission = new Permission;
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($this->permission);
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
            \Arcanedev\LaravelAuth\Contracts\Permission::class,
            \Arcanedev\LaravelAuth\Models\Permission::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->permission);
        }
    }

    /** @test */
    public function it_has_relationships()
    {
        $rolesRelationship = $this->permission->roles();

        $this->assertInstanceOf(BelongsToMany::class, $rolesRelationship);

        /** @var  \Arcanedev\LaravelAuth\Models\Role  $role */
        $role = $rolesRelationship->getRelated();

        $this->assertInstanceOf(\Arcanedev\LaravelAuth\Models\Role::class, $role);
    }

    /** @test */
    public function it_can_create()
    {
        $attributes = [
            'name'        => 'Create users',
            'slug'        => 'users.create',
            'description' => 'Allow to create users',
        ];

        $this->permission->create($attributes);

        $this->seeInDatabase('permissions', $attributes);
    }
}
