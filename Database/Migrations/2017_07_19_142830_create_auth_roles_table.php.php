<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_roles', function (Blueprint $table) {
            $table->bigIncrements('role_id')->comment('角色ID');
            $table->string('module')->nullable()->comment('来源模块');
            $table->bigInteger('creator_id')->default(0)->nullable()->comment('创建角色的系统用户ID');
            $table->string('role_name')->nullable()->comment('角色名称');
            $table->text('role_desc')->nullable()->comment('角色描述');
            $table->integer('permission_amount')->default(0)->nullable()->comment('角色权限数量');
            $table->tinyInteger('role_type')->default(1)->comment('角色类型,1 为永久角色、2 为临时角色、3 动态角色');
            $table->tinyInteger('role_status')->default(1)->comment('角色状态, 1 为激活状态、2 为停用状态');
            $table->timestamp('started_at')->nullable()->comment('角色生效时间');
            $table->timestamp('expired_at')->nullable()->comment('角色失效时间');
            $table->timestamp('created_at')->nullable()->comment('角色创建时间');
            $table->timestamp('updated_at')->nullable()->comment('角色最近更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth_roles');
    }
}
