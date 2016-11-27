<?php namespace Arcanedev\LaravelAuth\Tests\Services;

use Arcanedev\LaravelAuth\Services\SocialAuthenticator;
use Arcanedev\LaravelAuth\Tests\TestCase;

/**
 * Class     SocialAuthenticatorTest
 *
 * @package  Arcanedev\LaravelAuth\Tests\Services
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SocialAuthenticatorTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    /** @test */
    public function it_must_be_enabled()
    {
        $this->assertTrue(SocialAuthenticator::isEnabled());
    }

    /** @test */
    public function it_can_get_supported_drivers()
    {
        $expected = ['facebook', 'google', 'twitter'];

        $this->assertSame($expected, SocialAuthenticator::drivers());
    }

    /** @test */
    public function it_can_check_supported_drivers()
    {
        foreach (['facebook', 'google', 'twitter'] as $driver) {
            $this->assertTrue(SocialAuthenticator::isSupported($driver));
        }

        foreach (['bitbucket', 'github', 'linkedin'] as $driver) {
            $this->assertFalse(SocialAuthenticator::isSupported($driver));
        }
    }
}
