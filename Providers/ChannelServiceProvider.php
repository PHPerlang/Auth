<?php

namespace Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Auth\Channels\YunPianClient;
use Modules\Auth\Channels\YunpianMessage;
use Modules\Auth\Channels\YunPianChannel;

class ChannelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(YunPianChannel::class)
            ->needs(YunPianClient::class)
            ->give(function () {

                $config = config('services.yunpian');

                if (is_null($config)) {
                    // throw InvalidConfiguration::configurationNotSet();
                }
                return new YunPianClient(
                    $config['key'],
                    $config['token']
                );
            });
    }
}