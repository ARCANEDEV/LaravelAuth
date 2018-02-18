<?php namespace Arcanedev\LaravelAuth\Tests\Seeders;

use Arcanedev\LaravelAuth\Models\User;
use Arcanedev\LaravelAuth\Seeders\UsersTableSeeder;
use Arcanedev\LaravelAuth\Tests\TestCase;

/**
 * Class     UsersTableSeederTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Seeders
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UsersTableSeederTest extends TestCase
{
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
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_seed_default_user()
    {
        $this->artisan('db:seed', [
            '--class' => UsersTableSeeder::class
        ]);

        $users = User::all();

        static::assertCount(1, $users);

        /** @var \Arcanedev\LaravelAuth\Models\User $user */
        $user = $users->first();

        static::assertEquals('admin', $user->username);
        static::assertEquals('admin@email.com', $user->email);
        static::assertTrue($user->is_active);
        static::assertTrue($user->isActive());
        static::assertTrue($user->is_admin);
        static::assertTrue($user->isAdmin());
    }
}
