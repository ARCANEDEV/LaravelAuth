<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class     CreateUsersTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CreateUsersTable extends Migration
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = '';

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Make a migration instance.
     */
    public function __construct()
    {
        $this->table = config('laravel-auth.users.table', 'users');
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
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('username');
            $table->string('first_name', 30);
            $table->string('last_name', 30);
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->rememberToken();
            $table->boolean('active');

            if (config('laravel-auth.user-confirmation.enabled', false)) {
                $this->addConfirmationColumns($table);
            }

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Add confirmation columns.
     *
     * @param  Blueprint  $table
     */
    public function addConfirmationColumns(Blueprint $table)
    {
        $table->boolean('confirmed')->default(false);
        $table->string('confirmation_code', 30)->nullable();
        $table->timestamp('confirmed_at')->nullable();
    }
}
