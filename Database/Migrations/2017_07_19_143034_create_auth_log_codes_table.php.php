<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthLogCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_log_sms_codes', function (Blueprint $table) {
            $table->increments('id')->comment('验证码 ID');
            $table->string('code_type')->comment('验证码类型');
            $table->string('code_to')->comment('接收对象');
            $table->string('code_tag')->comment('验证码标签');
            $table->string('code_content')->comment('验证码内容');
            $table->string('code_status')->comment('验证码状态');
            $table->timestamp('expired_at')->nullable()->comment('验证码 ID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth_log_sms_codes');
    }
}
