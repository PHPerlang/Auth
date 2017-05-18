<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_operations', function (Blueprint $table) {
            $table->bigIncrements('op_id');                 // 日志 ID
            $table->string('op_method');                    // 请求方法
            $table->string('op_url');                       // 请求地址
            $table->ipAddress('op_ip');                     // 访问者 IP
            $table->bigInteger('member_id');                // 访问者系统用户 ID
            $table->text('op_input');                       // 入参
            $table->integer('op_http_code');                // http 响应状态码
            $table->integer('op_res_code');                 // 接口响应状态码
            $table->longText('op_output');                  // 出参
            $table->integer('op_started_at');               // 操作开始时间戳
            $table->integer('op_ended_at');                 // 操作结束时间戳
            $table->integer('op_duration');                 // 操作持续时间
            $table->timestamp('created_at')->nullable();    // 操作创建日期
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_operations');
    }
}
