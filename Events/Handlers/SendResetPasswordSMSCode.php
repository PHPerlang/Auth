<?php

namespace Modules\Auth\Events\Handlers;

use Gindowin\Services\SMS;
use Modules\Auth\Services\Code;
use Modules\Auth\Events\SendSMSCodeEvent;

class SendResetPasswordSMSCode
{

    public function handle(SendSMSCodeEvent $event)
    {

        if ($event->handler_token == 'auth.reset.password') {

            codes([
                1500 => '短信服务商错误',
                2010 => '发送太频繁，请 60 秒后再试',
                2020 => '验证码已超出最大发送次数，请明天再试',
            ]);

            $code = Code::generateCode();

            if(!Code::checkCodeFrequency($event->mobile)){

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


            $result = SMS::text(['code' => $code])->to($event->mobile)->send();

            if ($result !== true) {

                Code::log([
                    'type' => 'mobile',
                    'key' => $event->mobile,
                    'code' => $code,
                    'tag' => 'auth.register',
                    'status' => 'failed',
                    'description' => $result,
                ]);

                exception(1500, ['detail' => $result]);

            }

            Code::cacheCode($event->mobile, $code);

            Code::setCodeFrequency($event->mobile);

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