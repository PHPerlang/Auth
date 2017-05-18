<?php

namespace Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
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
                    $file => config_path('core/' . $config),
                ], 'config');

                $this->mergeConfigFrom(
                    $file, 'core::' . str_replace('.php', '', str_replace('/', '.', $config))
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
        $viewPath = base_path('resources/views/modules/Core');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/Core';
        }, \Config::get('view.paths')), [$sourcePath]), 'Core');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/Core');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'Core');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'Core');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
