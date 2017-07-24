<?php

namespace Modules\Auth\Events\Handlers;

use Modules\Storage\Events\UploadDoneEvent;

class AvatarUploadDone
{
    public function handle(UploadDoneEvent $event)
    {
        if ($event->token == 'auth.upload.avatar') {

            $event->member->member_avatar = $event->file->public_path;

            $event->member->save();
        }

    }
}