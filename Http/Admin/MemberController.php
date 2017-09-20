<?php

namespace Modules\Auth\Http\Admin;

use Gindowin\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Models\Member;


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

        return view('auth::admin.members', [
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
}