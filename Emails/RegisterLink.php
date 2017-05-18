<?php

namespace Modules\Core\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterLink extends Mailable
{
    use Queueable, SerializesModels;

    protected $code;
    
    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->view('Core::mails.register-link')->with([
            'code' => $this->code,
        ]);
    }

}