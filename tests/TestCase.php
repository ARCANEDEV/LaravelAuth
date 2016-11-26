<?php namespace Arcanedev\LaravelAuth\Tests;

use Illuminate\Routing\Router;
use Orchestra\Testbench\TestCase as BaseTestCase;

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
            version_compare('5.2.0', app()->version(), '<=') ? 'auth.providers.users.model' : 'auth.model',
            \Arcanedev\LaravelAuth\Models\User::class
        );

        // Laravel Auth Configs
        $config->set('laravel-auth.database.connection', 'testing');
        $config->set('laravel-auth.user-confirmation.enabled', true);
        $config->set('laravel-auth.impersonation.enabled', true);
    }

    /**
     * Set the Auth routes.
     *
     * @param  \Illuminate\Routing\Router  $router
     */
    private function setAuthRoutes($router)
    {
        $router->middleware('impersonate', \Arcanedev\LaravelAuth\Http\Middleware\Impersonate::class);

        $attributes = version_compare('5.2.0', app()->version(), '<=')
            ? ['middleware' => ['web', 'impersonate']]
            : ['middleware' => 'impersonate'];

        $router->group($attributes, function (Router $router) {
            $router->get('/', function () {
                return \Auth::user()->toJson();
            });

            $router->get('impersonate/start/{id}', function ($id) {
                $status = \Arcanedev\LaravelAuth\Services\UserImpersonator::start(
                    \Arcanedev\LaravelAuth\Models\User::find($id)
                );

                return response()->json([
                    'status' => $status ? 'success' : 'error'
                ]);
            });

            $router->get('impersonate/stop', function () {
                \Arcanedev\LaravelAuth\Services\UserImpersonator::stop();

                return redirect()->to('/');
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
            'password_resets',
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
}
