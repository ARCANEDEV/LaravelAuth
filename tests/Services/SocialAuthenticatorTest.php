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
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_must_be_enabled()
    {
        static::assertTrue(SocialAuthenticator::isEnabled());
    }

    /** @test */
    public function it_can_get_supported_drivers()
    {
        $expected = ['bitbucket', 'facebook', 'github', 'google', 'linkedin', 'twitter'];
        $drivers  = SocialAuthenticator::drivers();

        static::assertSame($expected, $drivers->keys()->toArray());
    }

    /** @test */
    public function it_can_get_enabled_drivers()
    {
        $expected = ['facebook', 'google', 'twitter'];
        $drivers  = SocialAuthenticator::enabledDrivers();

        static::assertSame($expected, $drivers->keys()->toArray());
    }

    /** @test */
    public function it_can_check_supported_drivers()
    {
        foreach (['facebook', 'google', 'twitter'] as $driver) {
            static::assertTrue(SocialAuthenticator::isSupported($driver));
        }

        foreach (['bitbucket', 'github', 'linkedin'] as $driver) {
            static::assertFalse(SocialAuthenticator::isSupported($driver));
        }
    }
}
