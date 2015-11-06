<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Class     CreatePermissionRoleTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CreateThrottlesTable extends Migration
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
        if ($this->isThrottlable()) {
            Schema::create('throttles', function (Blueprint $table) {
                $table->increments('id');

                $table->string('ip_address')->nullable();
                $table->string('email', 255);
                $table->timestamp('last_attempt_at')->nullable();

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
            Schema::dropIfExists('throttles');
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
        return config('laravel-auth.throttles', false);
    }
}
