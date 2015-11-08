<?php

use Arcanedev\LaravelAuth\Bases\Migration;
use Arcanedev\LaravelAuth\Services\UserConfirmator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class     CreateUsersTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CreateUsersTable extends Migration
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

        $this->setTable(
            config('laravel-auth.users.table', 'users')
        );
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
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->rememberToken();
            $table->boolean('is_active')->default(0);

            UserConfirmator::addColumns($table);

            $table->timestamps();
            $table->softDeletes();
        });
    }
}
