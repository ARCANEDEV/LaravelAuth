<?php namespace Arcanedev\LaravelAuth\Bases;

use Arcanedev\Support\Bases\Migration as BaseMigration;

/**
 * Class     Migration
 *
 * @package  Arcanedev\LaravelAuth\Bases
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class Migration extends BaseMigration
{
    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */
    /**
     * Migration constructor.
     */
    public function __construct()
    {
        $this->setConnection(config('laravel-auth.database.connection'));
        $this->setPrefix(config('laravel-auth.database.prefix'));
    }
}
