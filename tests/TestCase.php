<?php namespace Arcanedev\LaravelAuth\Tests;

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
        /** @var \Illuminate\Config\Repository $config */
        $config = $app['config'];

        // Laravel App Configs
        $config->set('database.default', 'testing');
        $config->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $config->set('auth.model', \Arcanedev\LaravelAuth\Models\User::class);

        // Laravel Auth Configs
        $config->set('laravel-auth.database.connection', 'testing');
        $config->set('laravel-auth.users.model', \Arcanedev\LaravelAuth\Models\User::class);
        $config->set('laravel-auth.user-confirmation.enabled', true);
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
     * Reset all migrations.
     */
    protected function resetMigration()
    {
        $this->artisan('migrate:reset');
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
