<?php

namespace Modules\Auth\Events;

use Illuminate\Support\Collection;
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
    public function __construct($member,Collection $input)
    {
        $this->member = $member;
        $this->input = $input;
    }
}