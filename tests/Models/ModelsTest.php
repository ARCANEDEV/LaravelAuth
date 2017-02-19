<?php namespace Arcanedev\LaravelAuth\Tests\Models;

use Arcanedev\LaravelAuth\Tests\TestCase;

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

    /* -----------------------------------------------------------------
     |  Helpers
     | -----------------------------------------------------------------
     */
    /**
     * Check the fired & unfired events.
     *
     * @param  array  $keys
     */
    protected function checkFiredEvents(array $keys)
    {
        $events = collect($this->modelEvents);

        $missing = collect($keys)->diff($events->keys()->toArray());

        if ( ! $missing->isEmpty())
            throw new \InvalidArgumentException('Missing model events ["'.$missing->implode('", "').'"]');

        $this->expectsEvents(
            $events->only($keys)->values()->toArray()
        );

        $this->doesntExpectEvents(
            $events->except($keys)->values()->toArray()
        );
    }
}
