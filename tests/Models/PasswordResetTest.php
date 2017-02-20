<?php namespace Arcanedev\LaravelAuth\Tests\Models;
use Arcanedev\LaravelAuth\Models\PasswordReset;

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

}
