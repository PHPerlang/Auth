<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Member;
use Modules\Auth\Models\Constant;
use Modules\Auth\Models\MemberRole;
use Modules\Auth\Models\RolePermission;

class DeployTableSeeder extends Seeder
{
    public function up()
    {

        // 注册超级角色
        $root = new Role;
        $root->role_name = config('auth::roles.root.name');
        $root->module = 'Auth';
        $root->role_desc = config('auth::roles.root.desc');
        $root->permission_amount = count(config('auth::roles.root.permissions'));
        $root->save();

        $staff = new Role;
        $staff->role_name = config('auth::roles.staff.name');
        $staff->role_desc = config('auth::roles.staff.desc');
        $root->permission_amount = count(config('auth::roles.staff.permissions'));

        $staff->save();

        // 绑定超级用户的权限
        $permissions = config('auth::roles.root.permissions');

        foreach ($permissions as $permission) {

            $rootPermission = new RolePermission;
            $rootPermission->role_id = $root->role_id;
            $rootPermission->permission_id = $permission;
            $rootPermission->created_at = timestamp();
            $rootPermission->save();
        }

        // 注册超级用户
        $member = new Member;
        $member->member_account = env('ROOT_MEMBER_ACCOUNT');
        $member->member_password = env('ROOT_MEMBER_PASSWORD');
        $member->member_avatar = null;
        $member->member_name = env('ROOT_MEMBER_NAME');;
        $member->member_status = 'normal';
        $member->register_channel = 'deploy';
        $member->save();

        // 为超级用户绑定创建者 ID
        $root->creator_id = $member->member_id;
        $root->save();

        // 绑定超级用户的角色
        $memberRole = new MemberRole;
        $memberRole->member_id = $member->member_id;
        $memberRole->role_id = $root->role_id;
        $memberRole->save();

        // 注册系统常量
        Constant::setValue('ROOT_ROLE_ID', $root->role_id);
        Constant::setValue('ROOT_MEMBER_ID', $member->member_id);
        Constant::setValue('STAFF_ROLE_ID', $staff->role_id);
    }

    public function down()
    {
        // 获取系统常量
        $root_member_id = Constant::getValue('ROOT_MEMBER_ID');
        $root_role_id = Constant::getValue('ROOT_ROLE_ID');
        $staff_role_id = Constant::getValue('STAFF_ROLE_ID');

        // 删除超级用户的角色
        MemberRole::where('member_id', $root_member_id)->delete();

        // 删除超级用户
        Member::find($root_member_id)->delete();

        // 删除超级角色的权限
        RolePermission::where('role_id', $root_role_id)->delete();

        // 删除超级角色
        Role::find($root_role_id)->delete();

        // 删除职员角色
        Role::find($staff_role_id)->delete();

        // 移除系统常量
        Constant::remove('ROOT_MEMBER_ID');
        Constant::remove('ROOT_ROLE_ID');
        Constant::remove('STAFF_ROLE_ID');

    }

}
