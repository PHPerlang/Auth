<?php

namespace Modules\Auth\Events\Handlers;

use Modules\Auth\Events\SendEmailCodeEvent;

class SendForgotPasswordEmailCode
{

    public function handle(SendEmailCodeEvent $event)
    {

        if ($event->handler_token == 'auth.forgot.password') {

        }

    }
}