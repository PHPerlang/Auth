<?php

namespace Modules\Auth\Events\Handlers;

use Modules\Auth\Services\Code;
use Modules\Auth\Models\Member;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Modules\Auth\Emails\ResetPasswordLink;
use Modules\Auth\Events\SendEmailCodeEvent;

class SendResetPasswordEmailCode
{

    public function handle(SendEmailCodeEvent $event)
    {

        if ($event->handler_token == 'auth.forgot.password') {

            $key = $event->email;

            if (!Member::where('member_email', $key)->first()) {
                exception(2010);
            }

            Code::checkCodeFrequency($key);

            $code = Code::generateCode();

            Code::cacheCode($key, $code);

            $link = url('/api/auth/reset/password/' . Crypt::encryptString($event->email) . '/' . Crypt::encryptString($code));

            Mail::to($event->email)->queue(new ResetPasswordLink(($link)));

            Code::setCodeFrequency($key);

        }

    }
}