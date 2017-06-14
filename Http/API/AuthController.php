<?php

namespace Modules\Auth\Http\API;

use Carbon\Carbon;
use Jindowin\Status;
use Jindowin\Request;
use Yunpian\Sdk\YunpianClient;
use Modules\Auth\Models\Member;
use Modules\Auth\Models\SmsCode;
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

    protected $registerCodeTimerTag;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->registerCodeCacheTag = ['register', 'code'];
        $this->registerCodeTimerTag = ['register', 'timer'];
    }

    /**
     * 检查邮箱注册码
     *
     * @param string $key
     * @param string $input
     *
     * @return bool
     */
    protected function checkRegisterCode($key, $input)
    {

        $required = Cache::tags($this->registerCodeCacheTag)->get($key, uniqid());

        if ($input != $required) {

            if (config('app.env') == 'production' || $input != '888888') {

                exception(1300);

            }
        }
    }

    /**
     *  检查注册类型
     *
     * @param string $input
     *
     * @return bool
     */
    protected function checkRegisterType($input)
    {
        return in_array($input, config('auth::config.register_types'));
    }

    /**
     *  检查登录类型
     *
     * @param string $input
     *
     * @return bool
     */
    protected function checkLoginType($input)
    {
        return in_array($input, config('auth::config.login_types'));
    }

    /**
     *  检查找回密码方式
     *
     * @param string $input
     *
     * @return bool
     */
    protected function checkFindPasswordType($input)
    {
        return in_array($input, config('auth::config.find_password_types'));
    }

    /**
     * 获取缓存的 KEY 值
     *
     * @return mixed
     */
    protected function getCacheKey()
    {
        return $this->request->input('register_type') == 'email' ? $this->request->input('member_email', null) : $this->request->input('member_phone', null);
    }


    /**
     * 缓存注册验证码
     *
     * @param string $key
     * @param integer $code
     */
    protected function cacheRegisterCode($key, $code)
    {
        Cache::tags($this->registerCodeCacheTag)->put($key, $code, 10);

        $last = Cache::tags($this->registerCodeTimerTag)->get($key);

        if (!$last) {

            Cache::tags($this->registerCodeTimerTag)->put($key, [0, time()], $this->getTheDayLeftMinutes());

        } else {

            Cache::tags($this->registerCodeTimerTag)->put($key, [$last[0]++, time()], $this->getTheDayLeftMinutes());
        }

    }

    /**
     * 检查发送注册验证码频率
     *
     * @param string $key
     */
    protected function checkSendRegisterCodeFrequency($key)
    {
        $last = Cache::tags($this->registerCodeTimerTag)->get($key);

        if ($last) {

            if ($last[0] > config('auth::config.send_code_max_times') - 1) {

                exception(2001);
            }

            if (time() - $last[1] <= 60) {

                exception(2002);
            }
        }
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
     * 获取离第二天前剩余的分钟数
     */
    protected function getTheDayLeftMinutes()
    {
        return (strtotime(date("Y-m-d", strtotime("+1 day")) . ' 00:00:00') - time()) / 60;
    }

    /**
     * 发送邮箱注册码接口
     *
     * @return Status
     */
    public function postRegisterCode()
    {

        validate($this->request->input(), [
            'member_email' => 'sometimes|email|max:255',
            'member_phone' => 'sometimes|size:11',
            'register_type' => 'required'
        ]);

        // 检查注册类型是否开启
        if (!$this->checkRegisterType($this->request->input('register_type'))) {
            exception(2000);
        }

        if ($this->request->input('member_email') || $this->request->input('member_phone')) {

            // 获取缓存键值
            $key = $this->getCacheKey();

            // 检查注册验证码发送频率
            $this->checkSendRegisterCodeFrequency($key);

            // 生成随机验证码
            $code = $this->generateCode();

            // 缓存注册验证码
            $this->cacheRegisterCode($key, $code);

            switch ($this->request->input('register_type')) {

                case 'email';

                    if (Member::where('member_email', $this->request->input('member_email'))->first()) {
                        exception(3002);
                    }

                    Mail::to($this->request->input('member_email'))->queue(new RegisterLink(($code)));

                    $email_code = new EmailCode;
                    $email_code->code = $code;
                    $email_code->email = $this->request->input('member_email');
                    $email_code->type = 'register';
                    $email_code->expired_at = timestamp(10 * 60);
                    $email_code->save();

                    break;

                case 'sms';

                    if (Member::where('member_phone', $this->request->input('member_phone'))->first()) {
                        exception(3003);
                    }

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

                    break;
            }

            return status(200);
        }

        return status(1001);
    }

    /**
     * 注册用户
     *
     * 注意：注册的用户的时候不能同时填充 member_phone, member_email, member_account 字段，以保证登录账号不会重复。
     * 它们三者同时存在的时候，需要严格进行值检查。
     *
     * @return Status
     */
    public function postRegister()
    {
        validate($this->request->input(), [
            'register_type' => 'required',
            'member_email' => 'sometimes|email|max:255',
            'member_phone' => 'sometimes|size:11',
            'member_password' => 'sometimes|min:6',
        ]);

        $register_type = $this->request->input('register_type');

        // 检查注册类型是否开启
        if (!$this->checkRegisterType($register_type)) {
            exception(2000);
        }

        $member = new Member;

        switch ($register_type) {

            case 'email':

                if (Member::where('member_email', $this->request->input('member_email'))->first()) {
                    exception(3002);
                }

                $this->checkRegisterCode($this->request->input('member_email'), $this->request->input('register_code'));

                $member->member_email = $this->request->input('member_email');

                break;

            case 'sms':

                if (Member::where('member_phone', $this->request->input('member_phone'))->first()) {
                    exception(3003);
                }

                $this->checkRegisterCode($this->request->input('member_phone'), $this->request->input('register_code'));

                $member->member_phone = $this->request->input('member_phone');


                break;

            case 'username':

                if (Member::where('member_account', $this->request->input('member_account'))->first()) {
                    exception(3004);
                }

                $member->member_account = $this->request->input('member_account');

                break;
        }

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

