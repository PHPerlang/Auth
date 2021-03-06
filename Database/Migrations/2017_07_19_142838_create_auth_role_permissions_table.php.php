<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthRolePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_role_permissions', function (Blueprint $table) {
            $table->bigInteger('role_id')->comment('角色ID');
            $table->string('permission_id')->comment('权限ID');
            $table->mediumText('permission_scope')->nullable()->comment('权限限制范围');
            $table->timestamp('created_at')->nullable()->comment('权限注册时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth_role_permissions');
    }
}
