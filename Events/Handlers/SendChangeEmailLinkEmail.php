<?php

namespace Modules\Auth\Events\Handlers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Modules\Auth\Emails\ChangeEmailLink;
use Modules\Auth\Events\SendEmailCodeEvent;
use Modules\Auth\Services\Code;

class SendChangeEmailLinkEmail
{

    public function handle(SendEmailCodeEvent $event)
    {

        if ($event->handler_token == 'auth.change.email') {

            $code = Code::generateCode();

            $encrypt = Crypt::encryptString(json_encode([
                'email' => $event->email,
                'code' => $code
            ]));

            $link = url('/api/auth/change/email/' . $encrypt);

            Mail::to($event->email)->queue(new ChangeEmailLink(($link)));
        }

    }
}