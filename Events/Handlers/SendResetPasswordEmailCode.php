<?php

namespace Modules\Auth\Events\Handlers;

use Modules\Auth\Models\Member;
use Modules\Auth\Services\Code;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Modules\Auth\Emails\ResetPasswordLink;
use Modules\Auth\Events\SendEmailCodeEvent;

class SendResetPasswordEmailCode
{

    public function handle(SendEmailCodeEvent $event)
    {

        if ($event->handler_token == 'auth.reset.password') {

            codes([
                2010 => '发送太频繁，请 60 秒后再试',
                2020 => '验证码已超出最大发送次数，请明天再试',
                3010 => '用户不存在',
            ]);

            $key = $event->email;

            if (!Member::where('member_email', $key)->first()) {
                exception(3010);
            }

            if (!Code::checkCodeFrequency($key)) {

                exception(2010);
            }

            $code = Code::generateCode();

            Code::cacheCode($key, $code);

            Code::log([
                'type' => 'email',
                'key' => $event->email,
                'code' => $code,
                'tag' => 'auth.reset.password',
                'status' => 'sent',
            ]);

            $link = url('/api/auth/reset/password/' . Crypt::encryptString($event->email) . '/' . Crypt::encryptString($code));

            Mail::to($event->email)->queue(new ResetPasswordLink(($link)));

            Code::setCodeFrequency($key);

            return status(200);
        }

    }
}