<?php

namespace Modules\Auth\Http\API;

use Gindowin\Status;
use Gindowin\Request;
use Gindowin\Services\SMS;
use Modules\Auth\Models\Guest;
use Modules\Auth\Models\Member;
use Modules\Auth\Services\Code;
use Illuminate\Routing\Controller;
use Modules\Auth\Services\Captcha;
use Illuminate\Support\Facades\Mail;
use Modules\Auth\Models\AccessToken;
use Illuminate\Support\Facades\Crypt;
use Modules\Auth\Emails\RegisterCode;
use Modules\Auth\Emails\ChangeEmailLink;
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
    public function postCode()
    {
        $rule = [
            'member_email' => 'required|email|max:255',
            'member_mobile' => 'required|size:11',
            'send_channel' => 'required'
        ];

        $collect = collect($this->request->inputFilter([
            'member_mobile',
            'member_email',
        ]));

        switch ($collect->get('send_channel')) {

            case 'email';

                $rule['member_mobile'] = 'sometimes|' . $rule['member_mobile'];

                validate($collect->all(), $rule);

                if (!$collect->get('member_email')) {
                    exception(1002);
                }

                if (Member::where('member_email', $collect->get('member_email'))->first()) {
                    exception(3002);
                }

                $key = $collect->get('member_email', null);

                $code = Code::generateCode();

                Code::checkCodeFrequency($key);

                Code::cacheCode($key, $code);

                $this->sendEmailCode($code);

                Code::setCodeFrequency($key);

                break;

            case 'mobile';

                $rule['member_email'] = 'sometimes|' . $rule['member_email'];

                validate($collect->all(), $rule);

                if (!$collect->get('member_mobile')) {

                    exception(1001);
                }

                if (Member::where('member_mobile', $collect->get('member_mobile'))->first()) {
                    exception(3003);
                }

                $key = $collect->get('member_mobile', null);

                $code = Code::generateCode();

                Code::checkCodeFrequency($key);

                $result = SMS::text(['code' => $code])->to($collect->get('member_mobile'))->send();

                if ($result === true) {

                    Code::cacheCode($key, $code);

                    Code::setCodeFrequency($key);

                } else {

                    exception(1200, ['detail' => $result]);
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
            'auth_code' => 'required',
            'auth_type' => 'required',
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

        if (!Code::checkCacheCode($key, $this->request->input('auth_code'))) {

            exception(1300);
        }

        return status(200);
    }

    /**
     * 注册用户
     *
     * @return Status
     */
    public function postRegister()
    {
        $rule = [
            'member_account' => 'required|unique:auth_members|unique:home_orgs,org_name',
            'member_mobile' => 'required|unique:auth_members|size:11',
            'member_email' => 'required|unique:auth_members|email|max:255',
            'member_password' => 'required|min:6',
            'register_channel' => 'required',
        ];

        $collect = collect($this->request->inputFilter([
            'member_mobile',
            'member_email',
            'member_account',
        ]));

        $register_channel = $collect->get('register_channel');

        if (!$this->checkRegisterChannel($register_channel)) {

            exception(2000);
        }

        $member = new Member;

        switch ($register_channel) {

            case 'email':

                $rule['member_mobile'] = 'sometimes|' . $rule['member_mobile'];
                $rule['member_account'] = 'sometimes|' . $rule['member_account'];
                $rule['member_password'] = 'sometimes|' . $rule['member_password'];

                validate($collect->all(), $rule);

                $member->email_status = 'unverified';

                if (config('auth::config.register_email_auth', false)) {

                    if (!Code::checkCacheCode(
                        $collect->get('member_email'),
                        $collect->get('register_code'))
                    ) {
                        exception(1300);
                    }

                    $member->email_status = 'verified';
                }

                $member->register_type = 'email';

                $member->member_email = $collect->get('member_email');

                $member->mobile_status = $collect->has('member_mobile') ? 'unverified' : 'none';

                break;

            case 'mobile':

                $rule['member_email'] = 'sometimes|' . $rule['member_email'];
                $rule['member_account'] = 'sometimes|' . $rule['member_account'];
                $rule['member_password'] = 'sometimes|' . $rule['member_password'];

                validate($collect->all(), $rule);

                $member->mobile_status = 'unverified';

                if (config('auth::config.register_mobile_auth', true)) {

                    if (!Code::checkCacheCode(
                        $collect->get('member_mobile'),
                        $collect->get('register_code'))
                    ) {
                        exception(1300);
                    }

                    $member->mobile_status = 'verified';
                }

                $member->register_type = 'mobile';

                $member->member_mobile = $collect->get('member_mobile');

                $member->email_status = $collect->get('member_email') ? 'unverified' : 'none';

                break;

            case 'username':

                $rule['member_mobile'] = 'sometimes|' . $rule['member_mobile'];
                $rule['member_email'] = 'sometimes|' . $rule['member_email'];

                validate($collect->all(), $rule);

                $member->register_type = 'username';
                $member->email_status = $collect->get('member_email') ? 'unverified' : 'none';
                $member->mobile_status = $collect->get('member_mobile') ? 'unverified' : 'none';
                $member->member_account = $collect->get('member_account');

                break;
        }

        $member->member_password = $collect->get('member_password', uniqid());
        $member->member_avatar = $collect->get('member_avatar');
        $member->member_nickname = $collect->get('member_nickname');
        $member->member_status = 'normal';

        $member->save();

        event(new MemberRegisterEvent($member, $collect->all()));

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

        $rule = [
            'member_mobile' => 'required|size:11',
            'member_email' => 'required|email|max:255',
            'member_account' => 'required',
            'member_password' => 'required|min:6',
            'login_channel' => 'required',
        ];

        $collect = collect($this->request->inputFilter([
            'member_mobile',
            'member_email',
            'member_account'
        ]));

        $login_channel = $collect->get('login_channel');

        if (!$this->checkLoginChannel($login_channel)) {

            exception(2000);
        }

        $member = null;

        switch ($login_channel) {
            case 'email':
                $rule['member_mobile'] = 'sometimes|' . $rule['member_mobile'];
                $rule['member_account'] = 'sometimes|' . $rule['member_account'];
                validate($collect->all(), $rule);
                $member = Member::where('member_email', $collect->get('member_email'))->first();
                break;
            case 'mobile':
                $rule['member_email'] = 'sometimes|' . $rule['member_email'];
                $rule['member_account'] = 'sometimes|' . $rule['member_account'];
                validate($collect->all(), $rule);
                $member = Member::where('member_mobile', $collect->get('member_mobile'))->first();
                break;
            case 'username':
                $rule['member_mobile'] = 'sometimes|' . $rule['member_mobile'];
                $rule['member_email'] = 'sometimes|' . $rule['member_email'];
                validate($collect->all(), $rule);
                $member = Member::where('member_account', $collect->get('member_account'))->first();
                break;
        }

        if (!$member) {

            exception('1001');
        }

        if (config('auth::config.login_email_auth', false)) {

            if ($member->email_status != 'unverified') {
                exception('2010');
            }

        }

        if (config('auth::config.login_mobile_auth', true)) {

            if ($member->mobile_status != 'unverified') {
                exception('2020');
            }

        }

        if ($member->member_password != $member->encryptMemberPassword($collect->get('member_password'))) {

            exception('1001');
        }


        return status(200, $this->saveMemberToken($member));
    }

    /**
     * 为新用户设置密码.
     */
    public function postNewPassword()
    {
        $rule = [
            'member_email' => 'required|email|max:255',
            'member_mobile' => 'required|size:255',
            'member_password' => 'required|min:6',
            'register_channel' => 'required',
            'register_code' => 'required',
        ];

        $collect = collect($this->request->inputFilter([
            'member_mobile',
            'member_email',
        ]));

        $key = null;

        $member = Guest::instance();

        $code = $collect->get('register_code');

        $register_channel = $collect->get('register_channel');

        if (!$this->checkRegisterChannel($register_channel)) {

            exception(2000);
        }

        switch ($register_channel) {
            case 'email':
                $key = $collect->get('member_email');
                $rule['member_mobile'] = 'sometimes|' . $rule['member_mobile'];
                break;
            case 'mobile':
                $key = $collect->get('member_mobile');
                $rule['member_email'] = 'sometimes|' . $rule['member_email'];
                break;
        }

        validate($collect->all(), $rule);

        if (!Code::checkCacheCode($key, $code)) {

            exception(1300);
        }

        $member->member_password = $collect->get('member_password');

        $member->save();

        Code::forgetCode($key);

        return status(200);
    }


    /**
     * 发送忘记密码重置验证码，邮箱的发发送重置链接
     *
     * @return Status
     */
    public function postResetPasswordCode()
    {
        $rule = [
            'member_email' => 'required|email|max:255',
            'member_mobile' => 'required|size:11',
            'find_password_channel' => 'required',
        ];

        $collect = collect($this->request->inputFilter([
            'member_mobile',
            'member_email',
        ]));


        $find_password_channel = $collect->get('find_password_channel');

        if (!$this->checkFindPasswordChannel($find_password_channel)) {

            exception(2000);
        }

        switch ($find_password_channel) {

            case 'email':

                $rule['member_mobile'] = 'sometimes|' . $rule['member_mobile'];

                validate($collect->all(), $rule);

                $key = $collect->get('member_email');

                if (!Member::where('member_email', $key)->first()) {
                    exception(2010);
                }

                Code::checkCodeFrequency($key);

                $code = Code::generateCode();

                Code::cacheCode($key, $code);

                $this->sendPasswordResetLinkEmail($key, $code);

                Code::setCodeFrequency($key);

                break;

            case 'mobile':

                $rule['member_email'] = 'sometimes|' . $rule['member_email'];

                validate($collect->all(), $rule);

                $key = $collect->get('member_mobile');

                if (!Member::where('member_mobile', $key)->first()) {
                    exception(2010);
                }

                Code::checkCodeFrequency($key);

                $code = Code::generateCode();

                Code::cacheCode($key, $code);

                SMS::text(['code' => $code])->to($collect->get('member_mobile'))->send();

                Code::setCodeFrequency($key);

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
        $rule = [
            'member_email' => 'required|email|max:255',
            'member_mobile' => 'required|size:11',
            'member_password' => 'required',
            'find_password_channel' => 'required',
            'reset_code' => 'required',
        ];

        $collect = collect($this->request->inputFilter([
            'member_mobile',
            'member_email',
        ]));

        $member = null;

        if (!$this->checkFindPasswordChannel($collect->get('find_password_channel'))) {

            exception(2000);
        }

        switch ($collect->get('find_password_channel')) {

            case 'email':

                $rule['member_mobile'] = 'sometimes|' . $rule['member_mobile'];

                validate($collect->all(), $rule);

                $code = Crypt::decryptString($collect->get('reset_code'));

                if (!Code::checkCacheCode($collect->get('member_email'), $code)) {

                    exception(1300);
                }

                $member = Member::where('member_email', $collect->get('member_email'))->first();

                break;

            case 'mobile':

                $rule['member_email'] = 'sometimes|' . $rule['member_email'];

                validate($collect->all(), $rule);

                if (!Code::checkCacheCode($collect->get('member_mobile'), $collect->get('reset_code'))) {
                    exception(1300);
                }

                $member = Member::where('member_mobile', $collect->get('member_mobile'))->first();

                break;
        }

        if (!$member) {

            exception('1001');
        }

        $member->member_password = $collect->get('member_password');

        $member->save();

        return status(200);
    }


    /**
     * 更改密码
     *
     * @return Status
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

        Code::checkCodeFrequency($email);

        $code = Code::generateCode();

        Code::cacheCode($email, $code);

        $this->sendChangeEmailLinkEmail(Guest::instance()->member_id, $email, $code);

        Code::setCodeFrequency($email);

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

        if (Code::checkCacheCode($email, $code)) {

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

