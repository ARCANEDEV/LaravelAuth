<?php

use Arcanedev\LaravelAuth\Bases\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class     CreatePermissionsTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CreateAuthPermissionsTable extends Migration
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

        $this->setTable(config('laravel-auth.permissions.table'));
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
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('slug');
            $table->timestamps();

            $table->unique('slug');
        });
    }
}
