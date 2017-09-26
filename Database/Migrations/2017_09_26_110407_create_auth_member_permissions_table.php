<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthMemberPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_member_permissions', function (Blueprint $table) {
            $table->bigInteger('member_id')->comment('角色ID');
            $table->string('permission_id')->comment('权限ID');
            $table->mediumText('permission_scope')->nullable()->comment('权限限制范围');
            $table->tinyInteger('permission_type')->default(0)->nullable()->comment('权限类型，1 为永久权限，2 为临时权限');
            $table->timestamp('started_at')->nullable()->comment('权限生效时间');
            $table->timestamp('expired_at')->nullable()->comment('权限失效时间');
            $table->timestamp('created_at')->nullable()->comment('权限注册时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth_member_permissions');
    }
}
