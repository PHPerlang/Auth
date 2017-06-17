<?php

namespace Modules\Auth\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterCode extends Mailable
{
    use Queueable, SerializesModels;

    protected $code;
    
    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->view('Auth::mails.register-link')->with([
            'code' => $this->code,
        ]);
    }

}