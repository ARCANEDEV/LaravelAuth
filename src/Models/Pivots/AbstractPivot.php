<?php namespace Arcanedev\LaravelAuth\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Model;

/**
 * Class     AbstractPivot
 *
 * @package  Arcanedev\LaravelAuth\Models\Pivots
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class AbstractPivot extends Pivot
{
    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * Create a new pivot model instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  array                                $attributes
     * @param  string                               $table
     * @param  bool                                 $exists
     */
    public function __construct(Model $parent, $attributes, $table, $exists = false)
    {
        parent::__construct($parent, $attributes, $table, $exists);

        $this->registerObserver();
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */
    /**
     * Register the observable events.
     */
    private function registerObserver()
    {
        // TODO: Add the observable events
    }

    /**
     * Get the observer class for the pivot table.
     *
     * @return string|null
     */
    protected function getObserverClass()
    {
        return null;
    }
}
