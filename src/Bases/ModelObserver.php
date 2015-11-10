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
     * @var Dispatcher
     */
    protected $event;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * UserObserver constructor.
     *
     * @param  Dispatcher  $event
     */
    public function __construct(Dispatcher $event)
    {
        $this->event = $event;
    }
}
