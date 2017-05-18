<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_tokens', function (Blueprint $table) {
            $table->bigInteger('member_id');                    // 系统用户 ID
            $table->string('access_token');                     // 系统用户 Token
            $table->string('client_group');                     // 客户端分组，实现多点登录
            $table->string('client_id');                        // 客户端 ID
            $table->string('client_version');                   // 客户端 版本
            $table->timestamp('created_at')->nullable();        // Token 创建时间
            $table->timestamp('updated_at')->nullable();        // Token 创建时间
            $table->timestamp('expired_at')->nullable();          // Token 失效时间

            $table->primary(['member_id', 'client_group']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_tokens');
    }
}
