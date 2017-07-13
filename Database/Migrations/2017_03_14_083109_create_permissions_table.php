<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->string('permission_id');                    // 权限 ID
            $table->string('module_id');                                    // 权限所属模块 ID
            $table->string('permission_name')->nullable();                  // 权限名称
            $table->text('involve_resources')->nullable();                  // 权限涉及的资源
            $table->text('permission_desc')->nullable();                    // 权限描述
            $table->string('permission_link')->nullable();                  // 权限详细描述链接
            $table->integer('permission_level')->nullable();                // 权限等级, 0 为最高优先级
            $table->text('permission_relevance')->nullable();               // 关联的权限
            $table->text('permission_like')->nullable();                    // 相似权限
            $table->integer('permission_order')->nullable();                // 权限排序
            $table->timestamp('started_at')->nullable();                    // 权限生效时间
            $table->timestamp('expired at')->nullable();                    // 权限失效时间
            $table->timestamp('created_at')->nullable();;                   // 权限最早创建时间
            $table->timestamp('updated_at')->nullable();;                   // 权限最后更新时间

            $table->primary('permission_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
