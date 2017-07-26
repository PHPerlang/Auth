<?php

namespace Modules\Auth\Events\Handlers;

use Modules\Auth\Models\Member;
use Modules\Auth\Services\Code;
use Illuminate\Support\Facades\Mail;
use Modules\Auth\Emails\RegisterCode;
use Modules\Auth\Events\SendEmailCodeEvent;

class SendRegisterEmailCode
{

    public function handle(SendEmailCodeEvent $event)
    {

        if ($event->handler_token == 'auth.register') {

            if (Member::where('member_email', $event->email)->first()) {

                exception(3010);
            }

            $key = $event->email;

            $code = Code::generateCode();

            if(!Code::checkCodeFrequency($key)){

                exception(2010);
            }

            Code::cacheCode($key, $code);

            Code::log([
                'type' => 'email',
                'key' => $event->email,
                'code' => $code,
                'tag' => 'auth.register',
                'status' => 'sent',
            ]);

            Mail::to($event->email)->queue(new RegisterCode(($code)));

            Code::setCodeFrequency($key);

            return status(200);
        }

    }
}