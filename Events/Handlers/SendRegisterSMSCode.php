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
                1200 => '短信服务商错误',
                1400 => '该手机号已注册',
                2010 => '验证码已超出最大发送次数，请明天再试',
                2020 => '发送太频繁，请 60 秒后再试',
                3010 => '图形验证码不正确',
            ]);

            if (Member::where('member_mobile', $event->mobile)->first()) {

                exception(1400);
            }

            $key = $event->mobile;

            $code = Code::generateCode();

            if (!Code::checkCodeFrequency($key)) {

                exception(2020);
            }

            $result = SMS::text(['code' => $code])->to($event->mobile)->send();

            if ($result === true) {

                Code::cacheCode($key, $code);

                Code::setCodeFrequency($key);

            } else {

                exception(1200, ['detail' => $result]);
            }

            return status(200);
        }

    }
}