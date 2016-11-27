<?php namespace Arcanedev\LaravelAuth\Tests\Services;

use Arcanedev\LaravelAuth\Models\User;
use Arcanedev\LaravelAuth\Services\UserImpersonator;
use Arcanedev\LaravelAuth\Tests\TestCase;

/**
 * Class     UserImpersonatorTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Services
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UserImpersonatorTest extends TestCase
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
    public function it_can_check_if_enabled()
    {
        $this->assertTrue(UserImpersonator::isEnabled());
    }

    /** @test */
    public function it_can_impersonate()
    {
        $this->assertFalse(UserImpersonator::isImpersonating());
        $this->assertFalse($this->app['session.store']->has('impersonate'));
        $this->createUsers();

        $this->actingAs($admin = User::find(1))
             ->visit('/')
             ->seeIsAuthenticatedAs($admin);

        $user = User::find(2);
        $this->call('GET', "/impersonate/start/{$user->id}");

        $this->assertTrue($this->app['session.store']->has('impersonate'));
        $this->assertTrue(UserImpersonator::isImpersonating());
        $this->assertSame($user->id, UserImpersonator::getUserId());

        $this->visit('/')
             ->seeIsAuthenticatedAs($user);

        $this->call('GET', '/impersonate/stop');
        $this->assertFalse($this->app['session.store']->has('impersonate'));
        $this->assertFalse(UserImpersonator::isImpersonating());
        $this->assertNull(UserImpersonator::getUserId());
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    protected function createUsers()
    {
        $users = [
            [
                'username'   => 'admin',
                'first_name' => 'Super',
                'last_name'  => 'admin',
                'email'      => 's.admin@gmail.com',
                'password'   => 'SuPeRpAsSwOrD',
            ],
            [
                'username'   => 'member',
                'first_name' => 'John',
                'last_name'  => 'DOE',
                'email'      => 'j.doe@gmail.com',
                'password'   => 'PaSsWoRd',
            ],
        ];

        foreach ($users as $attributes) {
            $this->createUser($attributes);
        }
    }

    private function createUser(array $attributes = [])
    {
        return User::create($attributes);
    }

    /**
     * Assert that the user is authenticated as the given user.
     *
     * @param  $user
     * @param  string|null  $guard
     * @return $this
     */
    public function seeIsAuthenticatedAs($user, $guard = null)
    {
        $expected = $this->app->make('auth')->user();
        $this->assertInstanceOf(
            get_class($expected), $user,
            'The currently authenticated user is not who was expected'
        );
        $this->assertSame(
            $expected->getAuthIdentifier(), $user->getAuthIdentifier(),
            'The currently authenticated user is not who was expected'
        );
        return $this;
    }
}
