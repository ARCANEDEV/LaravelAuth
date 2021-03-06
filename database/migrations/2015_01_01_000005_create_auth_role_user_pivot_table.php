<?php

use Arcanedev\LaravelAuth\Bases\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class     CreateRoleUserTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @see  \Arcanedev\LaravelAuth\Models\Pivots\RoleUser
 */
class CreateAuthRoleUserPivotTable extends Migration
{
    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * CreateAuthRoleUserPivotTable constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTable(config('laravel-auth.role-user.table', 'role_user'));
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
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();

            $table->primary(['user_id', 'role_id']);
        });
    }
}
