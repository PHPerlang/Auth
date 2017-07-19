<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthMembersTable extends Migration
{
    public function up()
    {
        Schema::create('auth_members', function (Blueprint $table) {
            $table->bigIncrements('member_id')->comment('系统用户 ID');
            $table->bigInteger('role_id')->nullable()->comment('用户角色 ID');
            $table->boolean('multi_roles')->default(false)->comment('用户是否拥有多个角色');
            $table->string('member_account')->nullable()->comment('系统用户用户名');
            $table->string('member_email')->unique()->nullable()->comment('系统用户邮箱');
            $table->string('member_mobile')->nullable()->comment('系统用户手机号码');
            $table->string('member_password')->comment('系统用户密码');
            $table->string('member_avatar')->nullable()->comment('系统用户头像');
            $table->string('member_nickname')->nullable()->comment('系统用户昵称');
            $table->string('member_status')->default('normal')->comment('系统用户状态');
            $table->string('register_type')->comment('注册类型');
            $table->enum('email_status', ['none', 'unverified', 'verified'])->comment('邮箱状态');
            $table->enum('mobile_status', ['none', 'unverified', 'verified'])->comment('手机状态');
            $table->timestamp('created_at')->nullable()->comment('系统用户创建时间');
            $table->timestamp('updated_at')->nullable()->comment('系统用户最近更新时间');
        });
    }

    public function down()
    {
        Schema::dropIfExists('auth_members');
    }
}
