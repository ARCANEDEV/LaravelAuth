<?php

use Arcanedev\LaravelAuth\Bases\Migration;
use Arcanedev\LaravelAuth\Services\SocialAuthenticator;
use Arcanedev\LaravelAuth\Services\UserConfirmator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class     CreateUsersTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CreateAuthUsersTable extends Migration
{
    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Make a migration instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTable(config('laravel-auth.users.table', 'users'));
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::connection($this->connection)->create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('username');
            $table->string('first_name', 30)->nullable();
            $table->string('last_name', 30)->nullable();
            $this->addCredentialsColumns($table);
            $table->rememberToken();
            $table->boolean('is_admin')->default(0);
            $table->boolean('is_active')->default(0);
            $this->addConfirmationColumns($table);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Add credentials columns.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $table
     */
    private function addCredentialsColumns(Blueprint $table)
    {
        // Basic columns
        $table->string('email')->unique();

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

    /**
     * Add confirmation columns.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $table
     */
    private function addConfirmationColumns(Blueprint $table)
    {
        if (UserConfirmator::isEnabled()) {
            $table->boolean('is_confirmed')->default(0);
            $table->string('confirmation_code', UserConfirmator::getLength())->nullable();
            $table->timestamp('confirmed_at')->nullable();
        }
    }
}
