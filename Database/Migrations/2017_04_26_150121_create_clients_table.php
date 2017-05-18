<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->string('client_id');                        // 客户端 ID
            $table->string('client_group');                     // 客户端分组
            $table->text('client_description');                 // 客户端描述
            $table->timestamp('created_at')->nullable();        // 客户端创建时间
            $table->timestamp('updated_at')->nullable();        // 客户端更新时间

            $table->primary('client_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
