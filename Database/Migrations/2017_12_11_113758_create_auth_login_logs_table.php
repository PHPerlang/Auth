<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthLoginLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_login_logs', function (Blueprint $table) {
            $table->bigIncrements('log_id');
            $table->bigInteger('member_id');
            $table->string('ip')->comment('登录IP');
            $table->string('latitude')->nullable()->comment('登录纬度');
            $table->string('longitude')->nullable()->comment('登录经度');
            $table->string('province')->nullable()->comment('登录定位省份');
            $table->string('city')->nullable()->comment('登录定位城市');
            $table->string('address')->nullable()->comment('登录定位地址');
            $table->string('error')->nullable()->comment('定位错误原因');
            $table->timestamp('created_at')->comment('登录时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth_login_logs');
    }
}
