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

            if (Member::where('member_mobile', $event->mobile)->first()) {

                exception(3003);
            }

            $key = $$event->mobile;

            $code = Code::generateCode();

            Code::checkCodeFrequency($key);

            $result = SMS::text(['code' => $code])->to($event->mobile)->send();

            if ($result === true) {

                Code::cacheCode($key, $code);

                Code::setCodeFrequency($key);

            } else {

                exception(1200, ['detail' => $result]);
            }
        }

    }
}