<?php

namespace Modules\Core\Providers;

use Modules\Core\Foundation\Context;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Contracts\Context as ContextInterface;

class ContextServiceProvider extends ServiceProvider
{
    public function register()
    {

        $this->app->singleton(ContextInterface::class, function ($app) {

            return new Context($app->request);
        });
    }

}
