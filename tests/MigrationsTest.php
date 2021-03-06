<?php namespace Arcanedev\LaravelAuth\Tests;

use Illuminate\Support\Facades\Schema;

/**
 * Class     MigrationsTest
 *
 * @package  Arcanedev\LaravelAuth\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MigrationsTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_publish_migrations()
    {
        /** @var \Illuminate\Filesystem\Filesystem $filesystem */
        $filesystem = $this->app['files'];
        $src        = $this->getMigrationsSrcPath();
        $dest       = $this->getMigrationsDestPath();

        static::assertCount(0, $filesystem->allFiles($dest));

        $this->publishMigrations();

        static::assertEquals(
            count($filesystem->allFiles($src)),
            count($filesystem->allFiles($dest))
        );

        $filesystem->cleanDirectory($dest);
    }

    /** @test */
    public function it_can_migrate()
    {
        $this->migrate();

        $prefix = config('laravel-auth.database.prefix');

        foreach ($this->getTablesNames() as $table) {
            static::assertTrue(
                Schema::hasTable($prefix.$table),
                "The table [{$prefix}{$table}] not found in the database."
            );
        }

        /**  password_resets table */
        $table = config('auth.passwords.users.table');

        static::assertTrue(
            Schema::hasTable($table),
            "The table [{$table}] not found in the database."
        );
    }
}
