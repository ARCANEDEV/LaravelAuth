<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\Support\Traits\PrefixedModel;
use Illuminate\Database\Eloquent\Model;

/**
 * Class     Model
 *
 * @package  Arcanedev\LaravelAuth\Base
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class AbstractModel extends Model
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use PrefixedModel;

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
        parent::__construct($attributes);

        $this->setConnection(config('laravel-auth.database.connection'));
        $this->setPrefix(config('laravel-auth.database.prefix'));
    }
}
