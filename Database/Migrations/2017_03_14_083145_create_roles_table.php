<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('role_id');                   // 角色 ID
            $table->string('module_id')->nullable();            // 预注册角色模块 ID
            $table->bigInteger('role_creator_id')->nullable();  // 创建角色的系统用户 ID
            $table->string('role_name');                        // 角色名称
            $table->text('role_desc')->nullable();              // 角色描述
            $table->integer('permission_amount')->nullable();   // 角色权限数量
            $table->enum('role_type', ['forever', 'temp']);       // 角色类型, 永久角色、临时角色
            $table->enum('role_status', ['active', 'inactive']);  // 角色状态, 激活状态、停用状态
            $table->timestamp('started_at')->nullable();        // 角色生效时间
            $table->timestamp('expired at')->nullable();        // 角色失效时间
            $table->timestamp('created_at')->nullable();        // 角色创建时间
            $table->timestamp('updated_at')->nullable();        // 角色最近更新时间
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
