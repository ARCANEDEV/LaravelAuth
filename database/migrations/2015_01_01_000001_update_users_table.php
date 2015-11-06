<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class     CreateUsersTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UpdateUsersTable extends Migration
{
    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'username');
            $table->string('first_name');
            $table->string('last_name');
            $table->boolean('active');

            if (config('laravel-auth.confirm-users')) {
                $this->addConfirmationColumns($table);
            }

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('username', 'name');
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');

            if (config('laravel-auth.confirm-users')) {
                $this->dropConfirmationColumns($table);
            }

            $table->dropColumn('deleted_at');
        });
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

    /**
     * Drop confirmation columns.
     *
     * @param  Blueprint  $table
     */
    private function dropConfirmationColumns(Blueprint $table)
    {
        $table->dropColumn('confirmed');
        $table->dropColumn('confirmation_code');
        $table->dropColumn('confirmed_at');
    }
}
