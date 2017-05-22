<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('code');
            $table->string('type');
            $table->timestamp('expired_at')->nullable();        // 注册码过期时间
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
        Schema::dropIfExists('email_codes');
    }
}
