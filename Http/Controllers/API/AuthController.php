<?php

namespace Modules\Core\Http\Controllers\API;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Modules\Core\Models\Status;
use Modules\Core\Models\Member;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Contracts\Context;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Models\AccessToken;
use Modules\Core\Emails\RegisterLink;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Emails\RestPasswordLink;

class AuthController extends Controller
{

    protected $context;

    protected $registerCodeCacheTag;

    public function __construct(Context $context)
    {
        $this->context = $context;
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

        $client = $this->context->request->client;

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

        validate($this->context->data(), ['member_email' => 'required|email|max:255']);

        if (Member::where('member_email', $this->context->data('member_email'))->first()) {

            exception(1002);
        }

        $code = mt_rand(100000, 999999);

        $key = $this->context->data('member_email');

        Cache::tags($this->registerCodeCacheTag)->put($key, $code, 10);

        Mail::to($this->context->data('member_email'))->queue(new RegisterLink(($code)));

        Log::info('Send member register code', array_merge($this->context->data(), ['code' => $code]));

        return $this->context->status(200);
    }

    /**
     * 注册用户接口
     *
     * @return Status
     */
    public function postRegister()
    {
        validate($this->context->data(), [
            'member_email' => 'required|email|max:255',
            'member_password' => 'sometimes|min:6',
            'email_code' => 'required|size:6',
        ]);

        if (Member::where('member_email', $this->context->data('member_email'))->first()) {

            exception(1002);
        }

        $key = $this->context->data('member_email');

        $code = Cache::tags($this->registerCodeCacheTag)->get($key, null);

        if (!$this->checkRegisterCode($this->context->data('email_code'), $code)) {

            exception(1001);
        }

        Cache::tags($this->registerCodeCacheTag)->forget($key);

        $member = new Member;

        $member->member_email = $this->context->data('member_email');
        $member->member_password = $this->context->data('member_password');
        $member->member_avatar = $this->context->data('member_avatar');
        $member->member_nickname = $this->context->data('member_nickname');
        $member->member_role_id = 100;
        $member->member_status = 'normal';

        $member->save();

        $accessToken = $this->saveMemberToken($member);

        return $this->context->status(200, $accessToken);
    }

    /**
     * 用户登录接口.
     *
     * @return Status
     */
    public function postLogin()
    {

        validate($this->context->data(), [
            'member_email' => 'required|email|max:255',
            'member_password' => 'required|min:6',
            'captcha' => 'sometimes|size:6',
        ]);

        $member = Member::where('member_email', $this->context->data('member_email'))->first();

        if (!$member || $member->member_password != $member->encryptMemberPassword($this->context->data('member_password'))) {

            exception('1001');
        }

        return $this->context->status(200, $this->saveMemberToken($member));
    }

    /**
     * 为新用户设置密码.
     */
    public function postNewPassword()
    {
        $member_id = $this->context->query('member_id');

        validate($this->context->data(), [
            'member_password' => 'required|min:6',
        ]);

        $member = Member::find($member_id);

        if (!$member) {

            exception(1001);
        }

        $member->member_password = $this->context->data('member_password');

        $member->save();

        return $this->context->status(200);
    }


    /**
     * 发送密码重置链接.
     *
     * @return Status
     */
    public function postForgotPassword()
    {
        validate($this->context->data(), [
            'member_email' => 'required|email|max:255',
        ]);

        $token = md5(time());

        $salt = Crypt::encryptString(json_encode([
            'member_email' => $this->context->data('member_eamil'),
            'token' => $token,
        ]));

        $link = 'http://test.kong.com/' . $salt;

        Mail::to($this->context->data('member_email'))->queue(new RestPasswordLink($link));

        Log::info('Send member reset password link', array_merge($this->context->data(), ['link' => $link]));

        return $this->context->status(200);
    }

}

