<?php

use Arcanedev\LaravelAuth\Bases\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class     CreatePermissionRoleTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CreateThrottlesTable extends Migration
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
            config('laravel-auth.throttles.table', 'throttles')
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
        if ($this->isThrottlable()) {
            Schema::connection($this->connection)->create($this->table, function (Blueprint $table) {
                $table->increments('id');

                $table->integer('user_id')->unsigned()->nullable();
                $table->string('type');
                $table->string('ip')->nullable();

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if ($this->isThrottlable()) {
            parent::down();
        }
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if throttles is enabled.
     *
     * @return bool
     */
    private function isThrottlable()
    {
        return config('laravel-auth.throttles.enabled', false);
    }
}
