<?php namespace Arcanedev\LaravelAuth\Models\Observers;

use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class     AbstractObserver
 *
 * @package  Arcanedev\LaravelAuth\Models\Observers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class AbstractObserver
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */
    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $event;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * UserObserver constructor.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $event
     */
    public function __construct(Dispatcher $event)
    {
        $this->event = $event;
    }
}
