<?php

namespace Modules\Core\Database\Seeders;

use Modules\Core\Models\Constant;
use Modules\Core\Models\Role;
use Modules\Core\Models\Member;
use Illuminate\Database\Seeder;
use Modules\Core\Models\Permission;
use Modules\Core\Models\MemberRole;
use Modules\Core\Models\RolePermissions;

class DeployTableSeeder extends Seeder
{
    /**
     * Deploy module data.
     *
     */
    public function up()
    {

        // 注册超级角色
        $root = new Role;
        $root->role_name = config('core::roles.root.name');
        $root->module_id = 'Core';
        $root->role_desc = config('core::roles.root.desc');
        $root->permission_amount = count(config('core::roles.root.permissions'));
        $root->save();

        // 绑定超级用户的权限
        $permissions = Permission::renderTemplate(config('core::roles.root.permissions'));
        foreach ($permissions as $permission) {

            $rootPermission = new RolePermissions;
            $rootPermission->role_id = $root->role_id;
            $rootPermission->permission_id = $permission;
            $rootPermission->created_at = timestamp();
            $rootPermission->save();
        }

        // 注册超级用户
        $member = new Member;
        $member->member_email = 'im@koyeo.io';
        $member->member_password = '123456';
        $member->member_avatar = null;
        $member->member_nickname = '古月';
        $member->member_role_id = 100;
        $member->member_status = 'normal';
        $member->save();

        // 绑定超级用户的角色
        $memberRole = new MemberRole;
        $memberRole->member_id = $member->member_id;
        $memberRole->role_id = $root->role_id;
        $memberRole->role_type = 'master';
        $memberRole->created_at = timestamp();
        $memberRole->save();

        // 注册系统常量
        Constant::set('ROOT_ROLE_ID', $root->role_id);
        Constant::set('ROOT_MEMBER_ID', $member->member_id);
    }

    /**
     * Drop module deploy data.
     */
    public function down()
    {
        // 获取系统常量
        $root_member_id = Constant::get('ROOT_MEMBER_ID');
        $root_role_id = Constant::get('ROOT_ROLE_ID');

        // 删除超级用户的角色
        MemberRole::where('member_id',$root_member_id)->delete();

        // 删除超级用户
        Member::find($root_member_id)->delete();

        // 删除超级角色的权限
        RolePermissions::where('role_id', $root_role_id)->delete();

        // 删除超级角色
        Role::find($root_role_id)->delete();

        // 移除系统常量
        Constant::remove('ROOT_MEMBER_ID');
        Constant::remove('ROOT_ROLE_ID');

    }

}
