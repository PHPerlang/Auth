<?php

namespace Modules\Auth\Events\Handlers;


use Modules\Storage\Events\UploadDoneEvent;

class AvatarUploadDoneHandler
{
    /**
     * 创建事件监听器。
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * 处理事件
     *
     * @param  UploadDoneEvent $event
     * @return void
     */
    public function handle(UploadDoneEvent $event)
    {

        if ($event->token == 'auth.avatar') {



        }

    }
}