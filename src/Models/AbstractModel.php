<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\Support\Database\Model;

/**
 * Class     Model
 *
 * @package  Arcanedev\LaravelAuth\Base
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class AbstractModel extends Model
{
    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setConnection(config('laravel-auth.database.connection'))
             ->setPrefix(config('laravel-auth.database.prefix'));

        parent::__construct($attributes);
    }
}
