<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthMemberRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('auth_member_roles', function (Blueprint $table) {
        $table->bigInteger('member_id');                    // 系统用户 ID
        $table->bigInteger('role_id');                      // 角色 ID
        $table->bigInteger('creator_id')->nullable();       // 创建此记录的用户 ID
        $table->enum('role_type', ['master', 'attached']);  // 角色类型
        $table->timestamp('created_at')->nullable();        // 创建时间
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::dropIfExists('auth_member_roles');
}
}
