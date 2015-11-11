<?php namespace Arcanedev\LaravelAuth\Tests\Seeders;

use Arcanedev\LaravelAuth\Models\User;
use Arcanedev\LaravelAuth\Tests\TestCase;

/**
 * Class     UsersTableSeederTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Seeders
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UsersTableSeederTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
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

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    /** @test */
    public function it_can_seed_default_user()
    {
        $this->artisan('db:seed', [
            '--class' => \Arcanedev\LaravelAuth\Seeders\UsersTableSeeder::class
        ]);

        $users = User::all();

        $this->assertCount(1, $users);

        /** @var \Arcanedev\LaravelAuth\Models\User $user */
        $user = $users->first();

        $this->assertEquals('admin', $user->username);
        $this->assertEquals('admin@email.com', $user->email);
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->isActive());
        $this->assertTrue($user->is_admin);
        $this->assertTrue($user->isAdmin());
    }
}
