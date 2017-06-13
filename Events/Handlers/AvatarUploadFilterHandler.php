<?php

namespace Modules\Auth\Events\Handlers;

use Modules\Storage\Filter;
use Modules\Storage\Events\UploadFilterEvent;

class AvatarUploadFilterHandler
{
    /**
     * 处理事件
     *
     * @param  UploadFilterEvent $event
     *
     * @return Filter
     */
    public function handle(UploadFilterEvent $event)
    {
        $upload_filter = new Filter;

        if ($event->token == 'auth.avatar') {

            $upload_filter->folder = '/avatars';
            $upload_filter->max_size = 100000000;
            $upload_filter->mine_types = [
                'image/jpeg'
            ];

        }

        return $upload_filter;
    }
}