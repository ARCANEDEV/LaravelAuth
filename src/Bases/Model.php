<?php namespace Arcanedev\LaravelAuth\Bases;

use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * Class     Model
 *
 * @package  Arcanedev\LaravelAuth\Base
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class Model extends BaseModel
{
    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($connection = config('laravel-auth.database.connection')) {
            $this->setConnection($connection);
        }
    }
}
