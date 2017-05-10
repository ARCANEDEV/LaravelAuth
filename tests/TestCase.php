<?php namespace Arcanedev\LaravelAuth\Tests;

use Illuminate\Routing\Router;
use Orchestra\Testbench\BrowserKit\TestCase as BaseTestCase;
use Arcanedev\LaravelAuth\Http\Middleware;

/**
 * Class     TestCase
 *
 * @package  Arcanedev\LaravelAuth\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class TestCase extends BaseTestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Laravel Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Orchestra\Database\ConsoleServiceProvider::class,
            \Arcanedev\LaravelAuth\LaravelAuthServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            //
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     */
    protected function getEnvironmentSetUp($app)
    {
        \Arcanedev\LaravelAuth\Auth::publishMigrations();

        // Laravel App Configs
        $this->setAuthConfigs($app['config']);

        // Laravel Auth Routes
        $this->setAuthRoutes($app['router']);
    }

    /**
     * Set the Auth configs.
     *
     * @param  \Illuminate\Config\Repository  $config
     */
    private function setAuthConfigs($config)
    {
        $config->set('database.default', 'testing');
        $config->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $config->set(
            'auth.providers.users.model',
            \Arcanedev\LaravelAuth\Models\User::class
        );

        // Laravel Auth Configs
        $config->set('laravel-auth.database.connection', 'testing');
        $config->set('laravel-auth.user-confirmation.enabled', true);
        $config->set('laravel-auth.impersonation.enabled', true);
        $config->set('laravel-auth.socialite.enabled', true);
    }

    /**
     * Set the Auth routes.
     *
     * @param  \Illuminate\Routing\Router  $router
     */
    private function setAuthRoutes($router)
    {
        if (method_exists($router, 'aliasMiddleware'))
            $router->aliasMiddleware('track-activity', Middleware\TrackLastActivity::class);
        else
            $router->middleware('track-activity', Middleware\TrackLastActivity::class);

        $router->group(['middleware' => ['web', 'track-activity']], function (Router $router) {
            $router->get('/', function () {
                return \Auth::user()->toJson();
            });
        });
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get table names.
     *
     * @return array
     */
    public function getTablesNames()
    {
        return [
            'users',
            'roles',
            'permissions_groups',
            'permissions',
            'role_user',
            'permission_role',
            'throttles',
        ];
    }

    /**
     * Get the migrations source path.
     *
     * @return string
     */
    protected function getMigrationsSrcPath()
    {
        return realpath(dirname(__DIR__) . '/database/migrations');
    }

    /**
     * Get the migrations destination path.
     *
     * @return string
     */
    protected function getMigrationsDestPath()
    {
        return realpath(database_path('migrations'));
    }

    /**
     * Migrate the migrations.
     */
    protected function migrate()
    {
        $this->artisan('migrate', [
            '--database' => 'testing',
            '--realpath' => $this->getMigrationsSrcPath(),
        ]);
    }

    /**
     * Publish the migrations.
     */
    protected function publishMigrations()
    {
        $this->artisan('vendor:publish', [
            '--tag' => ['migrations'],
        ]);
    }

    /**
     * See in the database with prefixed table.
     *
     * @param  string       $table
     * @param  array        $attributes
     * @param  string|null  $connection
     */
    protected function seeInPrefixedDatabase($table, array $attributes, $connection = null)
    {
        $this->seeInDatabase($this->getTablePrefix().$table, $attributes, $connection);
    }

    /**
     * Don't see in the database with prefixed table.
     *
     * @param  string       $table
     * @param  array        $data
     * @param  string|null  $connection
     */
    protected function dontSeeInPrefixedDatabase($table, array $data, $connection = null)
    {
        $this->dontSeeInDatabase($this->getTablePrefix().$table, $data, $connection);
    }

    /**
     * Get the prefix for auth tables.
     *
     * @return string|null
     */
    protected function getTablePrefix()
    {
        return config('laravel-auth.database.prefix');
    }
}
