<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_nodes', function (Blueprint $table) {
            $table->bigInteger('parent_role_id');                   // 父角色 ID
            $table->bigInteger('child_role_id');                    // 子角色 ID
            $table->integer('tree_depth');                          // 节点数深度
            $table->timestamp('created_at')->nullable();            // 节点创建时间
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_nodes');
    }
}
