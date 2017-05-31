<?php

namespace Modules\Auth\Events;

use Illuminate\Queue\SerializesModels;

class MemberUpdateEvent
{
    use SerializesModels;

    public $member;

    public $input;

    /**
     * 创建一个事件实例。
     *
     * @param $member
     * @param $input
     */
    public function __construct($member, $input)
    {
        $this->member = $member;
        $this->input = $input;
    }
}