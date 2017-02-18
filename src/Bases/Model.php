<?php namespace Arcanedev\LaravelAuth\Bases;

use Arcanedev\Support\Traits\PrefixedModel;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * Class     Model
 *
 * @package  Arcanedev\LaravelAuth\Base
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @method  static  \Illuminate\Contracts\Pagination\LengthAwarePaginator  paginate(int $perPage = null, array $columns = ['*'], string $pageName = 'page', int|null $page = null)
 * @method  static  \Illuminate\Contracts\Pagination\Paginator             simplePaginate(int $perPage = null, array $columns = ['*'], string $pageName = 'page', int|null $page = null)
 * @method  static  \Illuminate\Database\Eloquent\Model                    create(array $attribute)
 * @method  static  \Illuminate\Database\Eloquent\Model                    forceCreate(array $attributes)
 */
abstract class Model extends BaseModel
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
