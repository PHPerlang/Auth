<?php

namespace Modules\Auth\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangeEmailLink extends Mailable
{
    use Queueable, SerializesModels;

    protected $link;

    public function __construct($link)
    {
        $this->link = $link;
    }

    public function build()
    {
        return $this->view('auth::mails.change-email-link')->with([
            'link' => $this->link,
        ]);
    }

}