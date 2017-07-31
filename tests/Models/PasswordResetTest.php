<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Models\PasswordReset;
use Arcanedev\LaravelAuth\Models\User;

/**
 * Class     PasswordResetTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PasswordResetTest extends ModelsTest
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var  \Arcanedev\LaravelAuth\Models\PasswordReset */
    protected $passwordReset;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $this->passwordReset = new PasswordReset;
    }

    public function tearDown()
    {
        unset($this->passwordReset);

        parent::tearDown();
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
            \Arcanedev\LaravelAuth\Models\PasswordReset::class,
        ];

        foreach ($expectations as $expected) {
            $this->instance($expected, $this->passwordReset);
        }
    }

    /** @test */
    public function it_can_get_user_and_check_if_token_was_expired()
    {
        PasswordReset::getTokenRepository()->create($user = $this->createUser());

        /** @var  \Arcanedev\LaravelAuth\Models\PasswordReset  $pr */
        $pr = $this->passwordReset->where('email', $user->email)->first();

        $this->assertEquals($user->toArray(), $pr->user->toArray());
        $this->assertFalse($pr->isExpired());

        $pr->created_at = $pr->created_at->subHours(2);

        $this->assertTrue($pr->isExpired());
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Create a user for tests.
     *
     * @return \Arcanedev\LaravelAuth\Models\User
     */
    protected function createUser()
    {
        $user = User::query()->create([
            'username'   => 'john.doe',
            'first_name' => 'John',
            'last_name'  => 'DOE',
            'email'      => 'j.doe@gmail.com',
            'password'   => 'PaSsWoRd',
        ]);

        $user->refresh();

        return $user;
    }
}
