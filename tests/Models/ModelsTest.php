<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Models\Permission;
use Arcanedev\LaravelAuth\Models\Role;
use Arcanedev\LaravelAuth\Models\User;
use Arcanedev\LaravelAuth\Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

/**
 * Class     ModelsTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class ModelsTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    protected $modelEvents = [];

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp()
    {
        parent::setUp();

        $this->migrate();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     |  Database Methods
     | -----------------------------------------------------------------
     */

    /**
     * Create a user model.
     *
     * @param  array  $attributes
     *
     * @return \Arcanedev\LaravelAuth\Models\User|\Arcanesoft\Contracts\Auth\Models\User|mixed
     */
    protected static function createNewUser(array $attributes)
    {
        return User::query()->create($attributes);
    }

    /**
     * Create a role model.
     *
     * @param  array  $attributes
     *
     * @return \Arcanedev\LaravelAuth\Models\Role|\Arcanesoft\Contracts\Auth\Models\Role|mixed
     */
    protected static function createNewRole(array $attributes)
    {
        return Role::query()->create($attributes);
    }

    /**
     * Create a permission model.
     *
     * @param  array  $attributes
     *
     * @return \Arcanedev\LaravelAuth\Models\Permission|\Arcanesoft\Contracts\Auth\Models\Permission|mixed
     */
    protected static function createNewPermission(array $attributes)
    {
        return Permission::query()->create($attributes);
    }

    /* -----------------------------------------------------------------
     |  Custom assertions
     | -----------------------------------------------------------------
     */

    /**
     * Check the fired & unfired events.
     *
     * @param  array  $keys
     */
    protected function assertFiredEvents(array $keys)
    {
        foreach (Collection::make($this->modelEvents)->only($keys)->values()->toArray() as $event) {
            Event::assertDispatched($event);
        }
    }
}
