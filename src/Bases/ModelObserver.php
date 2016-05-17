<?php namespace Arcanedev\LaravelAuth\Bases;

use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class     ModelObserver
 *
 * @package  Arcanedev\LaravelAuth\Bases
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class ModelObserver
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $event;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
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
