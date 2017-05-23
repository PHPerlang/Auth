<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->bigInteger('role_id');                              // 角色 ID
            $table->string('permission_id');                            // 权限 ID
            $table->string('permission_route');                         // 权限绑定的路由
            $table->string('restrict_fields', 512)->nullable();  // 权限限制的字段信息
            $table->enum('scope', ['descendant', 'self']);        // 权限 ID
            $table->json('restrict_fields_parse')->nullable();    // 解析存储的权限限制的字段信息
            $table->enum('permission_type', ['forever', 'temp']); // 权限类型,  永久权限、临时权限
            $table->string('permission_name')->nullable();        // 权限名称
            $table->text('permission_desc')->nullable();          // 权限名称
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
        Schema::dropIfExists('role_permissions');
    }
}
