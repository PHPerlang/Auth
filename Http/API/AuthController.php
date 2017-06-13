<?php

namespace Modules\Auth\Http\API;

use Jindowin\Status;
use Jindowin\Request;
use Modules\Auth\Models\SmsCode;
use Yunpian\Sdk\YunpianClient;
use Modules\Auth\Models\Member;
use Modules\Auth\Models\EmailCode;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Auth\Models\AccessToken;
use Modules\Auth\Emails\RegisterLink;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Modules\Auth\Emails\RestPasswordLink;
use Modules\Auth\Events\MemberRegisterEvent;

class AuthController extends Controller
{

    public $request;

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

        $client = $this->request->client;

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
     * 生成验证码
     *
     * @return string
     */
    protected function generateCode()
    {
        return mt_rand(100000, 999999);
    }


    /**
     * 发送邮箱注册码接口
     *
     * @return Status
     */
    public function postRegisterEmailCode()
    {
        validate($this->request->input(), ['member_email' => 'required|email|max:255']);

        if (Member::where('member_email', $this->request->input('member_email'))->first()) {

            exception(1002);
        }

        $code = $this->generateCode();

        $key = $this->request->input('member_email');

        Cache::tags($this->registerCodeCacheTag)->put($key, $code, 10);

        Mail::to($this->request->input('member_email'))->queue(new RegisterLink(($code)));

        $email_code = new EmailCode;
        $email_code->code = $code;
        $email_code->email = $this->request->input('member_email');
        $email_code->type = 'register';
        $email_code->expired_at = timestamp(10 * 60);
        $email_code->save();

        return status(200);
    }

    /**
     * 发送短信注册码接口
     *
     * 短信验证码 1 分钟内不得重新发送，同一个手机号一天只能发 5 次，可以在配置文件中配置。
     *
     * @return Status
     */
    public function postRegisterSmsCode()
    {

        validate($this->request->input(), ['member_phone' => 'required|size:11']);

        if (Member::where('member_phone', $this->request->input('member_phone'))->first()) {

            exception(1002);
        }

        $code = $this->generateCode();

        $key = $this->request->input('member_phone');

        Cache::tags($this->registerCodeCacheTag)->put($key, $code, 10);

        $yunpian = YunpianClient::create(config('auth::config.yunpian_apikey'));

        $param = [
            YunpianClient::MOBILE => $this->request->input('member_phone'),
            YunpianClient::TEXT => str_replace('#code#', $code, config('auth::config.yunpian_code_template')),
        ];

        $send_sms_result = $yunpian->sms()->single_send($param);

        if ($send_sms_result->code() !== 0) {

            return status(1003, $send_sms_result);
        }

        $email_code = new SmsCode();
        $email_code->code = $code;
        $email_code->phone = $this->request->input('member_phone');
        $email_code->type = 'register';
        $email_code->expired_at = timestamp(10 * 60);
        $email_code->save();

        return status(200);

    }


    /**
     * 通过邮箱注册用户接口
     *
     * @return Status
     */
    public function postEmailRegister()
    {
        validate($this->request->input(), [
            'member_email' => 'required|email|max:255',
            'member_password' => 'sometimes|min:6',
            'email_code' => 'required|size:6',
        ]);

        if (Member::where('member_email', $this->request->input('member_email'))->first()) {

            exception(1001);
        }

        $key = $this->request->input('member_email');

        $code = Cache::tags($this->registerCodeCacheTag)->get($key, null);

        if (!$this->checkRegisterCode($this->request->input('email_code'), $code)) {

            exception(1002);
        }

        $member = new Member;

        $member->member_email = $this->request->input('member_email');
        $member->member_password = $this->request->input('member_password');
        $member->member_avatar = $this->request->input('member_avatar');
        $member->member_nickname = $this->request->input('member_nickname');
        $member->member_status = 'normal';

        $member->save();

        event(new MemberRegisterEvent($member, $this->request->input()));

        $accessToken = $this->saveMemberToken($member);

        return status(200, $accessToken);
    }


    /**
     * 通过短信注册用户接口
     *
     * @return Status
     */
    public function postSmsRegister()
    {
        validate($this->request->input(), [
            'member_phone' => 'required|size:11',
            'member_password' => 'sometimes|min:6',
            'sms_code' => 'required|size:6',
        ]);

        if (Member::where('member_phone', $this->request->input('member_phone'))->first()) {

            exception(1001);
        }

        $key = $this->request->input('member_phone');

        $code = Cache::tags($this->registerCodeCacheTag)->get($key, null);

        if (!$this->checkRegisterCode($this->request->input('member_phone'), $code)) {

            exception(1002);
        }

        $member = new Member;

        $member->member_phone = $this->request->input('member_phone');
        $member->member_password = $this->request->input('member_password');
        $member->member_avatar = $this->request->input('member_avatar');
        $member->member_nickname = $this->request->input('member_nickname');
        $member->member_status = 'normal';

        $member->save();

        event(new MemberRegisterEvent($member, $this->request->input()));

        $accessToken = $this->saveMemberToken($member);

        return status(200, $accessToken);
    }

    /**
     * 用户登录接口.
     *
     * @return Status
     */
    public function postLogin()
    {

        validate($this->request->input(), [
            'member_email' => 'required|email|max:255',
            'member_password' => 'required|min:6',
            'captcha' => 'sometimes|size:6',
        ]);

        $member = Member::where('member_email', $this->request->input('member_email'))->first();

        if (!$member || $member->member_password != $member->encryptMemberPassword($this->request->input('member_password'))) {

            exception('1001');
        }

        return status(200, $this->saveMemberToken($member));
    }

    /**
     * 为新用户设置密码.
     */
    public function postNewPassword()
    {
        $member_id = $this->request->input('member_id');

        validate($this->request->input(), [
            'member_email' => 'required',
            'member_password' => 'required|min:6',
            'email_code' => 'required',
        ]);

        $member = Member::find($member_id);

        if (!$member) {

            exception(1001);
        }

        $key = $this->request->input('member_email');

        $code = Cache::tags($this->registerCodeCacheTag)->get($key, null);

        if (!$this->checkRegisterCode($this->request->input('email_code'), $code)) {

            exception(1002);
        }

        Cache::tags($this->registerCodeCacheTag)->forget($key);

        $member->member_password = $this->request->input('member_password');

        $member->save();

        return status(200);
    }


    /**
     * 发送密码重置链接.
     *
     * @return Status
     */
    public function postForgotPassword()
    {
        validate($this->request->input(), [
            'member_email' => 'required|email|max:255',
        ]);

        $token = md5(time());

        $salt = Crypt::encryptString(json_encode([
            'member_email' => $this->request->input('member_eamil'),
            'token' => $token,
        ]));

        $link = 'http://test.kong.com/' . $salt;

        Mail::to($this->request->input('member_email'))->queue(new RestPasswordLink($link));

        Log::info('Send member reset password link', array_merge($this->request->input(), ['link' => $link]));

        return status(200);
    }

}

