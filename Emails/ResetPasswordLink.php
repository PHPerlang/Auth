<?php

namespace Modules\Auth\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordLink extends Mailable
{
    use Queueable, SerializesModels;

    protected $link;

    public function __construct($link)
    {
        $this->link = $link;
    }

    public function build()
    {
        return $this->view('auth::mails.reset-password-link')->with([
            'link' => $this->link,
        ]);
    }

}