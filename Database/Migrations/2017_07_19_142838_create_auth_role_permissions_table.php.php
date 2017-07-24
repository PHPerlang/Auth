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
            $table->bigInteger('role_id');                              // 角色 ID
            $table->string('permission_id');                            // 权限 ID
            $table->string('limit_params', 512)->nullable();  // 权限限制的字段信息
            $table->text('limit_parse')->nullable();    // 解析存储的权限限制的字段信息
            $table->string('permission_tag')->nullable();  // 权限限制的字段信息
            $table->enum('permission_type', ['forever', 'temp']); // 权限类型,  永久权限、临时权限
            $table->string('permission_name')->nullable();        // 权限名称
            $table->text('permission_desc')->nullable();          // 权限名称
            $table->timestamp('expired_at')->nullable();          // 记录创建时间
            $table->timestamp('created_at')->nullable();          // 记录创建时间
            $table->timestamp('updated_at')->nullable();          // 记录更新时间
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