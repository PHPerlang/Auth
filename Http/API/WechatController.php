<?php

namespace Modules\Auth\Http\API;

use Requests;
use Gindowin\Status;
use Gindowin\Request;
use Illuminate\Routing\Controller;

class WechatController extends Controller
{


    protected $request;

    public function __construct(Request $request)
    {

        $this->request = $request;
    }

    /**
     * 微信自动注册用户
     *
     * @return Status
     */
    public function autoRegister()
    {
        $code = $this->request->input('code');

        $app_id = ENV('Auth_WECHAT_APP_ID');
        $secret = ENV('Auth_WECHAT_SECRET');
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$app_id&secret=$secret&js_code=$code&grant_type=authorization_code";

        $request = Requests::get($url);

        if ($request->status_code == 200) {
            $data = json_decode($request->body);
            dd($data);
        } else {
            return status(1500);
        }

        return status(200);
    }


}
