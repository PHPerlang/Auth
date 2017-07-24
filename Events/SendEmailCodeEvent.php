<?php

namespace Modules\Auth\Events;

use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;

class SendEmailCodeEvent
{
    use SerializesModels;

    public $email;

    public $input;

    public $handler_token;

    public function __construct($email, Collection $input)
    {
        $this->email = $email;
        $this->input = $input;
        $this->handler_token = $input->get('handler_token');
    }
}