<?php

namespace Modules\Auth\Events;

use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;

class SendSMSCodeEvent
{
    use SerializesModels;

    public $mobile;

    public $input;

    public $handler_token;

    public function __construct($mobile, Collection $input)
    {
        $this->mobile = $mobile;
        $this->input = $input;
        $this->handler_token = $input->get('handler_token');
    }
}