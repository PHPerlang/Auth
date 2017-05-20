<?php

namespace Modules\Auth\Http\Controllers\API;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Modules\Auth\Models\Status;
use Modules\Auth\Models\Member;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Contracts\Request;
use Illuminate\Support\Facades\Mail;
use Modules\Auth\Models\AccessToken;
use Modules\Auth\Emails\RegisterLink;
use Illuminate\Support\Facades\Cache;
use Modules\Auth\Emails\RestPasswordLink;

class AuthController extends Controller
{

    protected $request;

    protected $registerCodeCacheTag;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->registerCodeCacheTag = ['register', 'code'];
    }

    /**
     * 检查邮箱注册码
     *
     * @param string $input
     * @param string $required
     *
     * @return bool
     */
    protected function checkRegisterCode($input, $required)
    {

        if ($input == $required) {

            return true;

        } else if (config('app.env') != 'production' && $input == '888888') {

            return true;
        }

        return false;
    }

    /**
     * 保存用户接入秘钥
     *
     * @param Member $member
     *
     * @return AccessToken
     */
    protected function saveMemberToken(Member $member)
    {

        $client = $this->request->request->client;

        $accessToken = new AccessToken;

        $accessToken->member_id = $member->member_id;
        $accessToken->access_token = $member->member_id . $member->member_password;
        $accessToken->client_group = $client->group;
        $accessToken->client_id = $client->id;
        $accessToken->client_version = $client->version;
        $accessToken->expired_at = timestamp();

        $accessToken->autoSave();

        return $accessToken;
    }


    /**
     * 发送邮箱注册码接口
     *
     * @return Status
     */
    public function postRegisterCode()
    {

        validate($this->request->data(), ['member_email' => 'required|email|max:255']);

        if (Member::where('member_email', $this->request->data('member_email'))->first()) {

            exception(1002);
        }

        $code = mt_rand(100000, 999999);

        $key = $this->request->data('member_email');

        Cache::tags($this->registerCodeCacheTag)->put($key, $code, 10);

        Mail::to($this->request->data('member_email'))->queue(new RegisterLink(($code)));

        Log::info('Send member register code', array_merge($this->request->data(), ['code' => $code]));

        return $this->request->status(200);
    }

    /**
     * 注册用户接口
     *
     * @return Status
     */
    public function postRegister()
    {
        validate($this->request->data(), [
            'member_email' => 'required|email|max:255',
            'member_password' => 'sometimes|min:6',
            'email_code' => 'required|size:6',
        ]);

        if (Member::where('member_email', $this->request->data('member_email'))->first()) {

            exception(1002);
        }

        $key = $this->request->data('member_email');

        $code = Cache::tags($this->registerCodeCacheTag)->get($key, null);

        if (!$this->checkRegisterCode($this->request->data('email_code'), $code)) {

            exception(1001);
        }

        Cache::tags($this->registerCodeCacheTag)->forget($key);

        $member = new Member;

        $member->member_email = $this->request->data('member_email');
        $member->member_password = $this->request->data('member_password');
        $member->member_avatar = $this->request->data('member_avatar');
        $member->member_nickname = $this->request->data('member_nickname');
        $member->member_role_id = 100;
        $member->member_status = 'normal';

        $member->save();

        $accessToken = $this->saveMemberToken($member);

        return $this->request->status(200, $accessToken);
    }

    /**
     * 用户登录接口.
     *
     * @return Status
     */
    public function postLogin()
    {

        validate($this->request->data(), [
            'member_email' => 'required|email|max:255',
            'member_password' => 'required|min:6',
            'captcha' => 'sometimes|size:6',
        ]);

        $member = Member::where('member_email', $this->request->data('member_email'))->first();

        if (!$member || $member->member_password != $member->encryptMemberPassword($this->request->data('member_password'))) {

            exception('1001');
        }

        return $this->request->status(200, $this->saveMemberToken($member));
    }

    /**
     * 为新用户设置密码.
     */
    public function postNewPassword()
    {
        $member_id = $this->request->query('member_id');

        validate($this->request->data(), [
            'member_password' => 'required|min:6',
        ]);

        $member = Member::find($member_id);

        if (!$member) {

            exception(1001);
        }

        $member->member_password = $this->request->data('member_password');

        $member->save();

        return $this->request->status(200);
    }


    /**
     * 发送密码重置链接.
     *
     * @return Status
     */
    public function postForgotPassword()
    {
        validate($this->request->data(), [
            'member_email' => 'required|email|max:255',
        ]);

        $token = md5(time());

        $salt = Crypt::encryptString(json_encode([
            'member_email' => $this->request->data('member_eamil'),
            'token' => $token,
        ]));

        $link = 'http://test.kong.com/' . $salt;

        Mail::to($this->request->data('member_email'))->queue(new RestPasswordLink($link));

        Log::info('Send member reset password link', array_merge($this->request->data(), ['link' => $link]));

        return $this->request->status(200);
    }

}

