<?php

namespace Modules\Auth\Http\Admin;

use Gindowin\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Models\Guest;
use Modules\Auth\Models\LoginLog;
use Modules\Auth\Models\Member;
use Modules\Auth\Models\Role;


class MemberController extends Controller
{
    /**
     * 客户端请求
     *
     * @var Request
     */
    public $request;

    /**
     * 构造函数
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getMembersView()
    {
        $members = Member::get();
        $roles = (new Role)->getFixedRoles();

        return view('auth::admin.members', [
            'members' => $members,
            'roles' => $roles,
            'path' => 'members',
        ]);
    }

    public function getUsersView()
    {

        $members = Member::get();

        return view('auth::admin.users', [
            'members' => $members,
            'path' => 'members',
        ]);

    }

    public function getMemberEditor()
    {
        return view('auth::admin.member-editor', [
            'path' => 'members',
        ]);
    }

    public function getProfile()
    {
        return view('auth::admin.base', [
            'path' => 'profile',
            'member' => Guest::instance(),
        ]);
    }

    public function getSetting()
    {

        return view('auth::admin.setting', [
            'path' => 'setting',
            'member' => Guest::instance(),
        ]);
    }

    public function getLoginLog()
    {
        $member = session('admin.member');
        $query = LoginLog::where('member_id', $member->member_id)->orderBy('created_at', 'desc');
        $logs = $query->limit(20)->get();
        $count = $query->count();
        return view('auth::admin.login-log', [
            'logs' => $logs,
            'count' => $count,
            'path' => 'login-log',
        ]);
    }
}