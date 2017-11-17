<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Tests\TestCase;
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
        foreach (collect($this->modelEvents)->only($keys)->values()->toArray() as $event) {
            Event::assertDispatched($event);
        }
    }
}
