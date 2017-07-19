<?php

namespace Modules\Auth\Http\API;

use Gindowin\Status;
use Gindowin\Request;
use Gindowin\Services\SMS;
use function GuzzleHttp\Psr7\str;
use Yunpian\Sdk\YunpianClient;
use Modules\Auth\Models\Guest;
use Modules\Auth\Models\Member;
use Modules\Auth\Services\Code;
use Illuminate\Routing\Controller;
use Modules\Auth\Services\Captcha;
use Illuminate\Support\Facades\Mail;
use Modules\Auth\Models\AccessToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Modules\Auth\Emails\RegisterCode;
use Modules\Auth\Emails\ChangeEmailLink;
use Illuminate\Support\Facades\Validator;
use Modules\Auth\Events\MemberUpdateEvent;
use Modules\Auth\Emails\ResetPasswordLink;
use Modules\Auth\Events\MemberRegisterEvent;


class AuthController extends Controller
{
    /**
     * 客户端请求
     *
     * @var Request
     */
    public $request;

    /**
     * 注册验证码标签
     *
     * @var string
     */
    protected $cache_register_tag = 'auth.register.code';

    /**
     * 注册验证码计时器
     *
     * @var string
     */
    protected $cache_register_timer = 'auth.register.timer';

    /**
     * 重置验证码标签
     *
     * @var string
     */
    protected $cache_reset_password_tag = 'auth.reset.code';

    /**
     * 重置验证码计时器
     *
     * @var string
     */
    protected $cache_reset_password_timer = 'auth.reset.timer';

    /**
     * 变更邮箱验证码标签
     *
     * @var string
     */
    protected $cache_change_email_tag = 'auth.change.email.code';

    /**
     * 变更邮箱验证码计时器
     *
     * @var string
     */
    protected $cache_change_email_timer = 'auth.change.email.timer';

    /**
     * 构造函数
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     *  检查注册通道
     *
     * @param string $channel
     *
     * @return bool
     */
    protected function checkRegisterChannel($channel)
    {
        return in_array($channel, config('auth::config.register_channels'));
    }

    /**
     *  检查登录通道
     *
     * @param string $channel
     *
     * @return bool
     */
    protected function checkLoginChannel($channel)
    {
        return in_array($channel, config('auth::config.login_channels'));
    }

    /**
     *  检查找回密码通道
     *
     * @param string $channel
     *
     * @return bool
     */
    protected function checkFindPasswordChannel($channel)
    {
        return in_array($channel, config('auth::config.find_password_channels'));
    }

    /**
     * 检查手机登录是否要求已验证手机号
     *
     * @param Member $member
     *
     * @return bool
     */
    protected function checkLoginMobileAuth(Member $member)
    {
        return config('auth::config.login_mobile_auth', true);
    }

    /**
     * 检查手机登录是否要求已验证手机号
     *
     * @param Member $member
     *
     * @return bool
     */
    protected function checkLoginEmailAuth(Member $member)
    {
        return config('auth::config.login_email_auth', false);
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
     * 发送邮箱验证码
     *
     * @param integer $code
     */
    protected function sendEmailCode($code)
    {
        Mail::to($this->request->input('member_email'))->queue(new RegisterCode(($code)));
    }


    /**
     * 发送邮箱重置密码链接, 链接里面加密了验证码
     *
     * @param string $email
     * @param integer $code
     */
    protected function sendPasswordResetLinkEmail($email, $code)
    {
        $link = url('/api/auth/reset/password/' . Crypt::encryptString($email) . '/' . Crypt::encryptString($code));

        Mail::to($email)->queue(new ResetPasswordLink(($link)));
    }

    /**
     * 发送更换邮箱链接
     *
     * @param $member_id
     * @param $email
     * @param $code
     */
    protected function sendChangeEmailLinkEmail($member_id, $email, $code)
    {
        $encrypt = Crypt::encryptString(json_encode([
            'member_id' => $member_id,
            'email' => $email,
            'code' => $code
        ]));

        $link = url('/api/auth/change/email/' . $encrypt);

        Mail::to($email)->queue(new ChangeEmailLink(($link)));
    }

    /**
     * 发送注册验证码接口
     *
     * @return Status
     */
    public function postRegisterCode()
    {
        validate($this->request->input(), [
            'member_email' => 'sometimes|email|max:255',
            'member_mobile' => 'sometimes|size:11',
            'register_channel' => 'required'
        ]);

        if (!$this->checkRegisterChannel($this->request->input('register_channel'))) {

            exception(2000);
        }

        switch ($this->request->input('register_channel')) {

            case 'email';

                if (!$this->request->input('member_email')) {
                    exception(1002);
                }

                if (Member::where('member_email', $this->request->input('member_email'))->first()) {
                    exception(3002);
                }

                $key = $this->request->input('member_email', null);

                $code = Code::generateCode();

                Code::checkCodeFrequency($this->cache_register_timer, $key);

                Code::cacheCode($this->cache_register_tag, $key, $code);

                $this->sendEmailCode($code);

                Code::setCodeFrequency($this->cache_register_timer, $key);

                break;

            case 'mobile';

                if (!$this->request->input('member_mobile')) {

                    exception(1001);
                }

                if (Member::where('member_mobile', $this->request->input('member_mobile'))->first()) {
                    exception(3003);
                }

                $key = $this->request->input('member_mobile', null);

                $code = Code::generateCode();

                Code::checkCodeFrequency($this->cache_register_timer, $key);

                $result = SMS::text(['code' => $code])->to($this->request->input('member_mobile'))->send();

                if ($result === true) {

                    Code::cacheCode($this->cache_register_tag, $key, $code);

                    Code::setCodeFrequency($this->cache_register_timer, $key);

                } else {

                    exception(1003, ['detail' => $result]);
                }

                break;
        }

        return status(200);
    }

    /**
     * 校验验证码，支持 cache_tag 所包含的验证码类型。
     *
     * @return Status
     */
    public function postCheckCode()
    {
        $rule = [
            'code' => 'required',
            'auth_type' => 'required',
            'auth_scene' => 'required',
        ];

        $key = null;

        switch ($this->request->input('auth_type')) {
            case 'mobile':

                $key = $this->request->input('member_mobile');

                $rule = array_merge($rule, ['member_mobile' => 'required|size:11']);

                break;
            case 'email':

                $key = $this->request->input('member_email');

                $rule = array_merge($rule, ['member_email' => 'required|email']);

                break;
        }

        validate($this->request->input(), $rule);

        if (!Code::checkCacheCode($this->request->input('auth_scene'), $key, $this->request->input('code'))) {

            exception(1300);
        }

        return status(200);
    }

    /**
     * 注册用户
     *
     * 注意：注册的用户的时候不能同时填充 member_mobile, member_email, member_account 字段，以保证登录账号不会重复。
     * 它们三者同时存在的时候，需要严格进行值检查。
     *
     * @return Status
     */
    public function postRegister()
    {
        validate($this->request->input(), [
            'register_channel' => 'required',
            'member_email' => 'sometimes|email|max:255',
            'member_mobile' => 'sometimes|size:11',
            'member_password' => 'sometimes|min:6',
        ]);

        $register_channel = $this->request->input('register_channel');

        // 检查注册类型是否开启
        if (!$this->checkRegisterChannel($register_channel)) {

            exception(2000);
        }

        $member = new Member;

        switch ($register_channel) {

            case 'email':

                if (Member::where('member_email', $this->request->input('member_email'))->first()) {
                    exception(3002);
                }

                if (config('auth::config.register_email_auth', false)) {

                    if (!Code::checkCacheCode(
                            $this->cache_register_tag,
                            $this->request->input('member_email'),
                            $this->request->input('register_code'))
                    ) {
                        exception(1300);
                    }
                }

                $member->member_email = $this->request->input('member_email');

                break;

            case 'mobile':

                if (Member::where('member_mobile', $this->request->input('member_mobile'))->first()) {
                    exception(3003);
                }

                if (config('auth::config.register_mobile_auth', true)) {

                    if (!Code::checkCacheCode(
                            $this->cache_register_tag,
                            $this->request->input('member_mobile'),
                            $this->request->input('register_code'))
                    ) {
                        exception(1300);
                    }
                }

                $member->member_mobile = $this->request->input('member_mobile');

                break;

            case 'username':

                if (Member::where('member_account', $this->request->input('member_account'))->first()) {
                    exception(3004);
                }

                if (!$this->request->input('member_password')) {

                    exception(3005);
                }

                $member->member_account = $this->request->input('member_account');

                break;
        }

        $member->member_password = $this->request->input('member_password', uniqid());
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
            'member_mobile' => 'sometimes|size:11',
            'member_email' => 'sometimes|email|max:255',
            'member_password' => 'required|min:6',
            'login_type' => 'required',
        ]);

        $login_type = $this->request->input('login_type');

        if (!$this->checkLoginType($login_type)) {

            exception(2000);
        }

        $member = null;

        switch ($login_type) {
            case 'email':
                validate($this->request->input(), ['member_email' => 'required']);
                $member = Member::where('member_email', $this->request->input('member_email'))->first();
                break;
            case 'mobile':
                validate($this->request->input(), ['member_mobile' => 'required']);
                $member = Member::where('member_mobile', $this->request->input('member_mobile'))->first();
                break;
            case 'username':
                validate($this->request->input(), ['member_account' => 'required']);
                $member = Member::where('member_account', $this->request->input('member_account'))->first();
                break;
        }

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

        validate($this->request->input(), [
            'member_email' => 'sometimes|email|max:255',
            'member_mobile' => 'sometimes|email|size:255',
            'member_password' => 'required|min:6',
            'register_code' => 'required',
            'register_type' => 'required',
        ]);

        $register_type = $this->request->input('register_type');

        if (!$this->checkRegisterType($register_type)) {
            exception(2000);
        }

        $member = Guest::instance();

        $key = null;

        switch ($register_type) {

            case 'email':

                $key = $this->request->input('member_email');
                break;

            case 'mobile':

                $this->request->input('member_mobile');
                break;
        }

        if (!Code::checkCacheCode(
                $this->cache_register_tag,
                $key,
                $this->request->input('register_code'))
        ) {
            exception(1300);
        }

        $member->member_password = $this->request->input('member_password');

        $member->save();

        $this->forgetCode('register_code', $key);

        return status(200);
    }


    /**
     * 发送忘记密码重置验证码，邮箱的发发送重置链接
     *
     * @return Status
     */
    public function postResetPasswordCode()
    {
        validate($this->request->input(), [
            'member_email' => 'sometimes|email|max:255',
            'member_mobile' => 'sometimes|size:11',
            'find_password_channel' => 'required',
        ]);

        $find_password_channel = $this->request->input('find_password_channel');

        if (!$this->checkFindPasswordChannel($find_password_channel)) {

            exception(2000);
        }

        switch ($find_password_channel) {

            case 'email':

                $key = $this->request->input('member_email');

                if (!Member::where('member_email', $key)->first()) {
                    exception(2010);
                }

                Code::checkCodeFrequency($this->cache_reset_password_timer, $key);

                $code = Code::generateCode();

                Code::cacheCode($this->cache_reset_password_tag, $key, $code);

                $this->sendPasswordResetLinkEmail($key, $code);

                Code::setCodeFrequency($this->cache_reset_password_timer, $key);

                break;

            case 'mobile':

                $key = $this->request->input('member_mobile');

                if (!Member::where('member_mobile', $key)->first()) {
                    exception(2010);
                }

                Code::checkCodeFrequency($this->cache_reset_password_timer, $key);

                $code = Code::generateCode();

                Code::cacheCode($this->cache_reset_password_tag, $key, $code);

                SMS::text(['code' => $code])->to($this->request->input('member_mobile'))->send();

                Code::setCodeFrequency($this->cache_reset_password_timer, $key);

                break;
        }

        return status(200);
    }

    /**
     * 重置密码链接跳转
     *
     * @param string $encrypt_email
     * @param string $encrypt_code
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function getResetPasswordLinkRedirect($encrypt_email, $encrypt_code)
    {
        $email = Crypt::decryptString($encrypt_email);

        $url = config('auth::config.reset_password_redirect_link') . "?email=$email&code=$encrypt_code";

        return redirect($url);
    }

    /**
     * 重置忘记密码
     *
     * @return Status
     */
    public function putResetPassword()
    {
        validate($this->request->input(), [
            'member_email' => 'sometimes|email|max:255',
            'member_mobile' => 'sometimes|size:11',
            'find_password_channel' => 'required',
            'reset_code' => 'required',
            'member_password' => 'required',
        ]);

        $find_password_channel = $this->request->input('find_password_channel');

        if (!$this->checkFindPasswordChannel($find_password_channel)) {

            exception(2000);
        }

        $member = null;

        switch ($find_password_channel) {

            case 'email':

                $code = Crypt::decryptString($this->request->input('reset_code'));

                if (!Code::checkCacheCode(
                        $this->cache_reset_password_tag,
                        $this->request->input('member_email'),
                        $code)
                ) {

                    exception(1300);
                }

                $member = Member::where('member_email', $this->request->input('member_email'))->first();

                break;

            case 'mobile':

                if (!Code::checkCacheCode(
                        $this->cache_reset_password_tag,
                        $this->request->input('member_mobile'),
                        $this->request->input('reset_code'))
                ) {
                    exception(1300);

                }

                $member = Member::where('member_mobile', $this->request->input('member_mobile'))->first();

                break;
        }

        if (!$member) {

            exception('1001');
        }

        $member->member_password = $this->request->input('member_password');

        $member->save();

        return status(200);
    }


    /**
     * 更改密码
     *
     * @return View
     */
    public function putPassword()
    {

        validate($this->request->input(), [
            'origin_password' => 'required|min:6',
            'new_password' => 'required|min:6',
        ]);

        $member = Guest::instance();
        $new_password = $this->request->input('new_password');
        $origin_password = $this->request->input('origin_password');

        if ($member->encryptMemberPassword($origin_password) != $member->member_password) {
            exception(1001);
        }


        if ($new_password == $origin_password || $member->encryptMemberPassword($new_password) == $member->member_password) {
            exception(1002);
        }

        $member->member_password = $new_password;

        $member->save();

        return status(200);
    }


    /**
     * 用户更换邮箱接口
     *
     * @return mixed
     */
    public function postChangeEmailLink()
    {
        validate($this->request->input(), [
            'member_email' => 'required|email'
        ]);

        $email = $this->request->input('member_email');

        if ($email == Guest::instance()->member_email) {

            exception(1001);
        }

        if (Member::where('member_email', $email)->first()) {

            exception(1002);
        }

        Code::checkCodeFrequency($this->cache_change_email_timer, $email);

        $code = Code::generateCode();

        Code::cacheCode($this->cache_change_email_tag, $email, $code);

        $this->sendChangeEmailLinkEmail(Guest::instance()->member_id, $email, $code);

        Code::setCodeFrequency($this->cache_change_email_timer, $email);

        return status(200);
    }

    /**
     * 用户点击更换邮箱链接跳转
     *
     * @param $encrypt
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function getChangeEmail($encrypt)
    {

        $params = json_decode(Crypt::decryptString($encrypt));

        $code = $params->code;
        $email = $params->email;
        $member_id = $params->member_id;

        $status = 'failed';

        if (Code::checkCacheCode($this->cache_change_email_tag, $email, $code)) {

            $status = 'success';

            $member = Member::where('member_id', $member_id)->first();
            $member->member_email = $email;
            $member->save();

            event(new MemberUpdateEvent($member, $this->request->input()));
        }

        $url = config('auth::config.change_email_redirect_link') . "?email=$email&status=$status";

        return redirect($url);
    }

    /**
     * 获取图形验证码信息
     *
     * @param Captcha $captcha
     *
     * @return mixed
     */
    public function getCaptcha(Captcha $captcha)
    {
        return status(200, $captcha->token());
    }

    /**
     * 获取图形验证码图片
     *
     * @param Captcha $captcha
     * @param string $config
     *
     * @return \Intervention\Image\ImageManager
     */
    public function getCaptchaImage(Captcha $captcha, $config = 'default')
    {
        return $captcha->create($config);
    }

    /**
     * 校验图形验证码
     *
     * @param Captcha $captcha
     *
     * @return Status
     */
    public function postCheckCaptchaCode(Captcha $captcha)
    {
        if (!$captcha->check($this->request->input('captcha_code'))) {

            return status(1001);
        }

        return status(200);
    }


    /**
     * 获取当前登录用户信息
     *
     * @return Status
     */
    public function getGuest()
    {
        $guest = Guest::instance();

        unset($guest->member_password);

        return status(200, $guest);
    }

    /**
     * 检查用户是否存在
     *
     * @return Status
     */
    public function checkMemberExists()
    {
        validate($this->request->input(), ['check_type' => 'required']);

        $check_type = $this->request->input('check_type');

        switch ($check_type) {
            case 'mobile':
                validate($this->request->input(), ['member_mobile' => 'required']);
                if (Member::where('member_mobile', $this->request->input('member_mobile'))->first()) {
                    return status(200);
                }
                break;
            case 'email':
                validate($this->request->input(), ['member_email' => 'required']);
                if (Member::where('member_email', $this->request->input('member_email'))->first()) {
                    return status(200);
                }
                break;
            case 'username':
                validate($this->request->input(), ['member_account' => 'required']);
                if (Member::where('member_account', $this->request->input('member_account'))->first()) {
                    return status(200);
                }
                break;
        }

        return status(1001);
    }

}

