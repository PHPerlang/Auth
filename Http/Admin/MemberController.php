<?php

namespace Modules\Auth\Http\Admin;

use Gindowin\Request;
use Illuminate\Routing\Controller;


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
        return view('auth::admin.members');
    }
}