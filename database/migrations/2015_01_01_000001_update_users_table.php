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
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 30)->after('username');
            $table->string('last_name', 30)->after('first_name');
            $table->boolean('active')->after('remember_token');

            if (config('laravel-auth.confirm-users')) {
                $this->addConfirmationColumns($table);
            }

            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('username', 'name');
            $table->dropColumn('first_name')->after('username');
            $table->dropColumn('last_name')->after('first_name');
            $table->dropColumn('active');

            if (config('laravel-auth.confirm-users')) {
                $this->dropConfirmationColumns($table);
            }

            $table->dropSoftDeletes();
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
        $table->boolean('confirmed')->default(false)->after('active');
        $table->string('confirmation_code', 30)->nullable()->after('confirmed');
        $table->timestamp('confirmed_at')->nullable()->after('confirmation_code');
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
