<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Manager;
use Illuminate\Contracts\Notifications\Factory as FactoryContract;
use Illuminate\Contracts\Notifications\Dispatcher as DispatcherContract;

class SmsManager extends Manager
{
    protected $defaultSmsService = 'yunpian';

    /**
     * Get the default channel driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->defaultSmsService;
    }

    public function createYunpianDriver()
    {

    }

}