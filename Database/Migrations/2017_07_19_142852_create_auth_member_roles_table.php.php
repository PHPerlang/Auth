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
