<?php

namespace Modules\Auth\Events;

use Illuminate\Queue\SerializesModels;

class SendEmailCode
{
    use SerializesModels;

    public $input;

    public $member;

    public $token;

    /**
     * 创建一个事件实例。
     *
     * @param $member
     * @param $input
     */
    public function __construct($member, $input)
    {
        $this->input = $input;
        $this->member = $member;

        if (isset($input['handler_token'])) {
            $this->token = $input['handler_token'];
        }

    }
}