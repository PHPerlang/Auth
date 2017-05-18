<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_versions', function (Blueprint $table) {
            $table->bigInteger('client_id');                    // 客户端 ID
            $table->string('version_tag');                      // 客户端版本名称
            $table->text('version_description');              // 客户端版本描述
            $table->timestamp('created_at')->nullable();        // 客户端版本创建时间
            $table->timestamp('updated_at')->nullable();        // 客户端版本更新时间
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_versions');
    }
}
