<?php namespace Arcanedev\LaravelAuth\Tests;

use Arcanedev\LaravelAuth\LaravelAuthServiceProvider;

/**
 * Class     LaravelAuthServiceProviderTest
 *
 * @package  Arcanedev\LaravelAuth\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class LaravelAuthServiceProviderTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var LaravelAuthServiceProvider */
    private $provider;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $this->provider = $this->app->getProvider(LaravelAuthServiceProvider::class);
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($this->provider);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    /** @test */
    public function it_can_be_instantiated()
    {
        $expectations = [
            \Illuminate\Support\ServiceProvider::class,
            \Arcanedev\Support\ServiceProvider::class,
            \Arcanedev\Support\PackageServiceProvider::class,
            \Arcanedev\LaravelAuth\LaravelAuthServiceProvider::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->provider);
        }
    }

    /** @test */
    public function it_can_provides()
    {
        $expected = [
            //
        ];

        $this->assertEquals($expected, $this->provider->provides());
    }
}
