<?php

namespace Modules\Auth\Events\Handlers;

use Gindowin\Services\SMS;
use Modules\Auth\Models\Member;
use Modules\Auth\Services\Code;
use Modules\Auth\Events\SendSMSCodeEvent;

class SendRegisterSMSCode
{

    public function handle(SendSMSCodeEvent $event)
    {

        if ($event->handler_token == 'auth.register') {

            codes([
                1500 => '短信服务商错误',
                1600 => '该手机号已注册',
                2010 => '发送太频繁，请 60 秒后再试',
                2020 => '验证码已超出最大发送次数，请明天再试',
            ]);

            if (Member::where('member_mobile', $event->mobile)->first()) {

                exception(1400);
            }

            $key = $event->mobile;

            $code = Code::generateCode();

            if (!Code::checkCodeFrequency($key)) {

                Code::log([
                    'type' => 'mobile',
                    'key' => $event->mobile,
                    'code' => $code,
                    'tag' => 'auth.register',
                    'status' => 'failed',
                    'description' => '发送太频繁，请 60 秒后再试',
                ]);

                exception(2010);
            }

            $result = true;

            if (env('APP_ENV') == 'production') {
                $result = SMS::template(config('sms.yunpian.template'), ['code' => $code])->to($event->mobile)->send();
            }

            if ($result === true) {

                Code::cacheCode($key, $code);

                Code::setCodeFrequency($key);

            } else {

                Code::log([
                    'type' => 'mobile',
                    'key' => $event->mobile,
                    'code' => $code,
                    'tag' => 'auth.register',
                    'status' => 'failed',
                    'description' => $result,
                ]);

                exception(1200, ['detail' => $result]);
            }

            Code::log([
                'type' => 'mobile',
                'key' => $event->mobile,
                'code' => $code,
                'tag' => 'auth.register',
                'status' => 'sent',
                'description' => '',
            ]);

            return status(200);
        }

    }
}