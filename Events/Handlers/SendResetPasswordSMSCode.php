<?php

namespace Modules\Auth\Events\Handlers;

use Gindowin\Services\SMS;
use Modules\Auth\Services\Code;
use Modules\Auth\Events\SendSMSCodeEvent;

class SendResetPasswordSMSCode
{

    public function handle(SendSMSCodeEvent $event)
    {

        if ($event->handler_token == 'auth.forgot.password') {

            Code::checkCodeFrequency($event->mobile);

            $code = Code::generateCode();

            Code::cacheCode($event->mobile, $code);

            SMS::text(['code' => $code])->to($event->mobile->get('member_mobile'))->send();

            Code::setCodeFrequency($event->mobile);
        }

    }
}