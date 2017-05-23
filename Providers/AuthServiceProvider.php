<?php

namespace Modules\Auth\Providers;

use Modules\Auth\Models\Guest;
use Modules\Auth\Models\AccessToken;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerGuest();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $files = list_files(__DIR__ . '/../Config', true);

        foreach ($files as $file) {

            if (strrchr($file, '.php') == '.php') {

                $config = str_replace(__DIR__ . '/../Config/', '', $file);

                $this->publishes([
                    $file => config_path('auth/' . $config),
                ], 'config');

                $this->mergeConfigFrom(
                    $file, 'auth::' . str_replace('.php', '', str_replace('/', '.', $config))
                );

            }
        }
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/Auth');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/Auth';
        }, \Config::get('view.paths')), [$sourcePath]), 'Auth');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/Auth');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'Auth');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'Auth');
        }

    }

    /**
     * Register the Guest
     */
    protected function registerGuest()
    {
        try {
            $access_token = AccessToken::where('access_token', app('request')->getAccessToken())->first();

            if ($access_token) {

                Guest::init($access_token->member_id);
            }
        } catch (\Exception $e) {

            // If the database is not ready, ignore the database error.
        }

        $this->app->singleton(Guest::class, function ($app) {

            return Guest::instance();
        });

    }
}
