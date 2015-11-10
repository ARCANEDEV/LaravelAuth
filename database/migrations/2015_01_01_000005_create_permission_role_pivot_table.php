<?php

use Arcanedev\LaravelAuth\Bases\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class     CreatePermissionRoleTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CreatePermissionRolePivotTable extends Migration
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
    protected $table = 'permission_role';

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
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->timestamps();

            $table->primary(['permission_id', 'role_id']);
        });
    }
}
