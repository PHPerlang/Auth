<?php

namespace Modules\Auth\Events\Handlers;

use Modules\Auth\Events\SendSMSCodeEvent;

class SendForgotPasswordSMSCode
{

    public function handle(SendSMSCodeEvent $event)
    {

        if ($event->handler_token == 'auth.forgot.password') {

        }

    }
}