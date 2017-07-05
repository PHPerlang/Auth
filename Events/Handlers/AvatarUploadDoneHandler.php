<?php

namespace Modules\Auth\Events\Handlers;

use Modules\Storage\Events\UploadDoneEvent;

class AvatarUploadDoneHandler
{
    /**
     * 处理事件
     *
     * @param  UploadDoneEvent $event
     * @return void
     */
    public function handle(UploadDoneEvent $event)
    {
        dd($event);
        if ($event->token == 'auth.upload.avatar') {

            // 保存用户头像
            $event->member->member_avatar = $event->file->public_path;
            $event->member->save();
        }

    }
}