<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements('member_id');                      // 系统用户 ID
            $table->bigInteger('member_role_id');                    // 系统用户主要角色 ID
            $table->string('member_account')->nullable();            // 系统用户用户名
            $table->string('member_email')->unique()->nullable();    // 系统用户邮箱
            $table->string('member_phone')->nullable();              // 系统用户电话号码
            $table->string('member_password');                       // 系统用户密码
            $table->string('member_avatar')->nullable();             // 系统用户头像
            $table->string('member_nickname')->nullable();           // 系统用户昵称
            $table->string('member_status')->default('normal');      // 系统用户状态
            $table->timestamp('created_at')->nullable();             // 系统用户创建时间
            $table->timestamp('updated_at')->nullable();             // 系统用户最近更新时间
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
