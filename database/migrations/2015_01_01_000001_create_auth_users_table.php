<?php

use Arcanedev\LaravelAuth\Bases\Migration;
use Arcanedev\LaravelAuth\Services\SocialAuthenticator;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class     CreateUsersTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @see  \Arcanedev\LaravelAuth\Models\User
 */
class CreateAuthUsersTable extends Migration
{
    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Make a migration instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTable(config('laravel-auth.users.table', 'users'));
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->createSchema(function (Blueprint $table) {
            $table->increments('id');
            $table->string('username');
            $table->string('first_name', 30)->nullable();
            $table->string('last_name', 30)->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $this->addCredentialsColumns($table);
            $table->rememberToken();
            $table->boolean('is_admin')->default(0);
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();
            $table->timestamp('activated_at')->nullable();
            $table->softDeletes();
        });
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Add credentials columns.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $table
     */
    private function addCredentialsColumns(Blueprint $table)
    {
        if (SocialAuthenticator::isEnabled()) {
            $table->string('password')->nullable();
            // Social network columns
            $table->string('social_provider')->nullable();
            $table->string('social_provider_id')->unique()->nullable();
        }
        else {
            $table->string('password');
        }
    }
}
