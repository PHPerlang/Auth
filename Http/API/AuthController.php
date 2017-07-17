<?php

namespace Modules\Auth\Http\API;

use Jindowin\Status;
use Jindowin\Request;
use Modules\Auth\Models\Guest;
use Yunpian\Sdk\YunpianClient;
use Modules\Auth\Models\Member;
use Modules\Auth\Models\SmsCode;
use Modules\Auth\Models\EmailCode;
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
     * 定义当前控制器用到的缓存标签
     *
     * @var array
     */
    protected $cache_tag = [
        'register_code' => ['auth.register', 'code'],
        'register_timer' => ['auth.register', 'timer'],
        'reset_password_code' => ['auth.reset', 'code'],
        'reset_password_timer' => ['auth.reset', 'timer'],
        'change_email_code' => ['auth.change.email', 'code'],
        'change_email_timer' => ['auth.change.email', 'timer'],
    ];

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
     * 检查缓存的验证码
     *
     * @param string $tag
     * @param string $key
     * @param string $input
     * @param bool $exception
     *
     * @return bool
     */
    protected function checkCacheCode($tag, $key, $input, $exception = true)
    {

        $required = Cache::tags($this->cache_tag[$tag])->get($key, uniqid());

        if ($input != $required) {

            if (config('app.env') == 'production' || $input != '888888') {

                if ($exception) {

                    exception(1300);

                } else {

                    return false;
                }

            }
        }

        return true;
    }


    /**
     * 缓存验证码
     *
     * @param string $tag
     * @param string $key
     * @param integer $code
     */
    protected function cacheCode($tag, $key, $code)
    {
        Cache::tags($this->cache_tag[$tag])->put($key, $code, 10);
    }

    /**
     * 设置验证码周期检查
     *
     * @param string $timer
     * @param string $key
     */
    protected function setCodeFrequency($timer, $key)
    {
        $last = Cache::tags($this->cache_tag[$timer])->get($key);

        if (!$last) {

            Cache::tags($this->cache_tag[$timer])->put($key, [0, time()], $this->getTheDayLeftMinutes());

        } else {

            Cache::tags($this->cache_tag[$timer])->put($key, [$last[0]++, time()], $this->getTheDayLeftMinutes());
        }
    }


    /**
     * 清除验证码缓存
     *
     * @param string $tag
     * @param string $key
     */
    protected function forgetCode($tag, $key)
    {
        Cache::tags($this->cache_tag[$tag])->forget($key);
    }


    /**
     * 检查验证码发送频率
     *
     * @param string $timer
     * @param string $key
     */
    protected function checkCodeFrequency($timer, $key)
    {
        $last = Cache::tags($this->cache_tag[$timer])->get($key);

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
     *
     * @return integer
     */
    protected function getTheDayLeftMinutes()
    {
        return (strtotime(date("Y-m-d", strtotime("+1 day")) . ' 00:00:00') - time()) / 60;
    }

    /**
     * 发送邮箱验证码
     *
     * @param integer $code
     * @param string $type
     */
    protected function sendEmailCode($code, $type)
    {
        Mail::to($this->request->input('member_email'))->queue(new RegisterCode(($code)));

        $email_code = new EmailCode;
        $email_code->code = $code;
        $email_code->email = $this->request->input('member_email');
        $email_code->type = $type;
        $email_code->expired_at = timestamp(10 * 60);
        $email_code->save();
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

        $email_code = new EmailCode;
        $email_code->code = $code;
        $email_code->email = $this->request->input('member_email');
        $email_code->type = 'reset';
        $email_code->expired_at = timestamp(10 * 60);
        $email_code->save();
    }

    /**
     * 发送更换邮箱链接
     *
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

        $email_code = new EmailCode;
        $email_code->code = $code;
        $email_code->email = $this->request->input('member_email');
        $email_code->type = 'change.email';
        $email_code->expired_at = timestamp(10 * 60);
        $email_code->save();
    }

    /**
     * 发送马拉松项目验证码
     *
     * @param $code
     *
     * @return bool
     */
    public function sendSmsCodeByUnioncast($code)
    {
        $mobile = $this->request->input('member_phone');
        $serial_no = mt_rand(1000000000, 9999999999) . mt_rand(1000000000, 9999999999);
        $time = date('YmdHis', time());
        $template = config('services.unioncast.template') . $code;
        $param = mb_convert_encoding("product_id=2&serial_no=$serial_no&mobile=$mobile&time=$time&template_id=35&smscontent=$template", 'GBK');
        $customer_no = config('services.unioncast.customer_no');

        $block_size = @mcrypt_get_block_size('tripledes', 'ecb');
        $padding_char = $block_size - (strlen($param) % $block_size);
        $param .= str_repeat(chr($padding_char), $padding_char);

        $token = base64_encode(@mcrypt_encrypt(
            $cipher = MCRYPT_3DES,
            $key = config('services.unioncast.key'),
            $data = $param,
            $mode = 'ecb'
        ));

        $url = "http://123.103.15.165:6000/smscode?encrypt=1&customer_no=$customer_no&reqstr=$token";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        try {

            $p = xml_parser_create();
            xml_parse_into_struct($p, $response, $vals, $index);
            xml_parser_free($p);

            foreach ($vals as $val) {

                if (is_array($val) && $val['type'] == 'complete' && $val['tag'] == 'STATUS' && $val['value'] == 0) {

                    return true;
                }
            }

        } catch (\Exception $error) {
            // nothing
        }

        exception(1003, [
            'reason' => $response
        ]);
    }

    /**
     * 发送短信验证码
     *
     * @param integer $code
     * @param string $type
     *
     * @return bool
     */
    protected function sendSmsCode($code, $type)
    {

//        $yunpian = YunpianClient::create(config('auth::config.yunpian_apikey'));
//
//        $param = [
//            YunpianClient::MOBILE => $this->request->input('member_phone'),
//            YunpianClient::TEXT => str_replace('#code#', $code, config('auth::config.yunpian_code_template')),
//        ];
//
//        $send_sms_result = $yunpian->sms()->single_send($param);
//
//        if ($send_sms_result->code() !== 0) {
//
//            exception(1003, $send_sms_result);
//        }

        $email_code = new SmsCode();
        $email_code->code = $code;
        $email_code->phone = $this->request->input('member_phone');
        $email_code->type = $type;
        $email_code->expired_at = timestamp(10 * 60);
        $email_code->save();

        return $this->sendSmsCodeByUnioncast($code, $type);
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

        if (!$this->checkRegisterType($this->request->input('register_type'))) {

            exception(2000);
        }

        switch ($this->request->input('register_type')) {

            case 'email';

                if (!$this->request->input('member_email')) {
                    exception(1002);
                }

                if (Member::where('member_email', $this->request->input('member_email'))->first()) {
                    exception(3002);
                }

                $key = $this->request->input('member_email', null);

                $this->checkCodeFrequency('register_timer', $key);

                $code = $this->generateCode();

                $this->cacheCode('register_code', $key, $code);

                $this->sendEmailCode($code, 'register');

                $this->setCodeFrequency('register_timer', $key);

                break;

            case 'mobile';

                if (!$this->request->input('member_phone')) {

                    exception(1001);
                }

                if (Member::where('member_phone', $this->request->input('member_phone'))->first()) {
                    exception(3003);
                }

                $key = $this->request->input('member_phone', null);

                $this->checkCodeFrequency('register_timer', $key);

                $code = $this->generateCode();

                $this->cacheCode('register_code', $key, $code);

                if ($this->sendSmsCode($code, 'register')) {

                    $this->setCodeFrequency('register_timer', $key);
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

                $key = $this->request->input('member_phone');

                $rule = array_merge($rule, ['member_phone' => 'required|size:11']);

                break;
            case 'email':

                $key = $this->request->input('member_email');

                $rule = array_merge($rule, ['member_email' => 'required|email']);

                break;
        }

        validate($this->request->input(), $rule);

        $this->checkCacheCode($this->request->input('auth_scene'), $key, $this->request->input('code'));

        return status(200);
    }

    /**
     * 注册用户
     *
     * 注意：注册的用户的时候不能同时填充 member_phone, member_email, member_account 字段，以保证登录账号不会重复。
     * 它们三者同时存在的时候，需要严格进行值检查。
     *
     * @return Status
     */
    public function postRegister(Captcha $captcha)
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

        // 检查是否需要图形验证码
        // if (config('auth::config.captcha_frequency') == 'always') {
        //
        //     if (!$captcha->check($this->request->input('captcha', ''))) {
        //
        //         exception(3001);
        //     }
        // }

        $member = new Member;

        switch ($register_type) {

            case 'email':

                if (Member::where('member_email', $this->request->input('member_email'))->first()) {
                    exception(3002);
                }

                if (config('auth::config.register_email_auth')) {

                    $this->checkCacheCode(
                        'register_code',
                        $this->request->input('member_email'),
                        $this->request->input('register_code')
                    );
                }

                $member->member_email = $this->request->input('member_email');

                break;

            case 'mobile':

                if (Member::where('member_phone', $this->request->input('member_phone'))->first()) {
                    exception(3003);
                }

                if (config('auth::config.register_mobile_auth')) {

                    $this->checkCacheCode(
                        'register_code',
                        $this->request->input('member_phone'),
                        $this->request->input('register_code')
                    );
                }

                $member->member_phone = $this->request->input('member_phone');

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
            'member_phone' => 'sometimes|size:11',
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
                validate($this->request->input(), ['member_phone' => 'required']);
                $member = Member::where('member_phone', $this->request->input('member_phone'))->first();
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
            'member_phone' => 'sometimes|email|size:255',
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

                $this->request->input('member_phone');
                break;
        }

        $this->checkCacheCode('register_code', $key, $this->request->input('register_code'));

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
            'member_phone' => 'sometimes|size:11',
            'find_password_type' => 'required',
        ]);

        $find_password_type = $this->request->input('find_password_type');

        if (!$this->checkFindPasswordType($find_password_type)) {

            exception(2000);
        }

        $code = $this->generateCode();

        switch ($find_password_type) {

            case 'email':

                $key = $this->request->input('member_email');

                if (!Member::where('member_email', $key)->first()) {
                    exception(2010);
                }

                $this->checkCodeFrequency('reset_password_timer', $key);

                $this->cacheCode('reset_password_code', $key, $code);

                $this->sendPasswordResetLinkEmail($key, $code);

                $this->setCodeFrequency('reset_password_timer', $key);

                break;

            case 'mobile':

                $key = $this->request->input('member_phone');

                if (!Member::where('member_phone', $key)->first()) {
                    exception(2010);
                }

                $this->checkCodeFrequency('reset_password_timer', $key);

                $this->cacheCode('reset_password_code', $key, $code);

                $this->sendSmsCode($code, 'reset');

                $this->setCodeFrequency('reset_password_timer', $key);

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
            'member_phone' => 'sometimes|size:11',
            'find_password_type' => 'required',
            'reset_code' => 'required',
            'member_password' => 'required',
        ]);

        $find_password_type = $this->request->input('find_password_type');

        if (!$this->checkFindPasswordType($find_password_type)) {

            exception(2000);
        }

        $member = null;

        switch ($find_password_type) {

            case 'email':

                $code = Crypt::decryptString($this->request->input('reset_code'));

                $this->checkCacheCode('reset_password_code', $this->request->input('member_email'), $code);

                $member = Member::where('member_email', $this->request->input('member_email'))->first();

                break;

            case 'mobile':

                $this->checkCacheCode('reset_password_code', $this->request->input('member_phone'), $this->request->input('reset_code'));

                $member = Member::where('member_phone', $this->request->input('member_phone'))->first();

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

        $code = $this->generateCode();

        $this->cacheCode('change_email_code', $email, $code);

        $this->sendChangeEmailLinkEmail(Guest::instance()->member_id, $email, $code);

        $this->setCodeFrequency('change_email_timer', $email);

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

        if ($this->checkCacheCode('change_email_code', $email, $code, false)) {

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
                validate($this->request->input(), ['member_phone' => 'required']);
                if (Member::where('member_phone', $this->request->input('member_phone'))->first()) {
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

