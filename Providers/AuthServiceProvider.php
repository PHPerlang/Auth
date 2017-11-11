<?php

namespace Modules\Auth\Providers;

use Modules\Auth\Models\Guest;
use Modules\Auth\Models\AccessToken;
use Illuminate\Support\Facades\Schema;
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

        Schema::defaultStringLength(191);

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();


        if (env('APP_ENV') != 'production') {

            $log_file = base_path('storage/logs/mysql-' . date('Y-m-d') . '.log');
            file_put_contents($log_file, PHP_EOL . PHP_EOL, FILE_APPEND);

            \Illuminate\Support\Facades\DB::listen(function ($query) use ($log_file) {

                $sql = $query->sql;

                foreach ($query->bindings as $replace) {
                    $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
                    $sql = preg_replace('/\?/', $value, $sql, 1);
                }
                chmod($log_file, 0777);
                file_put_contents($log_file, timestamp() . ': ' . $sql . PHP_EOL, FILE_APPEND);
            });

        }
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

                $path = str_replace('.php', '', str_replace('/', '.', $config));

                $config = $this->app['config']->get("auth.$path", []);

                $this->app['config']->set("auth::$path", array_merge(require $file, $config));

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
        }, \Config::get('view.paths')), [$sourcePath]), 'auth');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/auth');

        if (is_dir($langPath)) {

            $this->loadTranslationsFrom($langPath, 'auth');

        } else {

            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'auth');
        }

    }

    /**
     * Register the Guest
     */
    protected function registerGuest()
    {
        try {

            Guest::parse(app('request')->header('X-Access-Token'));

        } catch (\Exception $e) {

            // If the database is not ready, ignore the database error.
        }

        $this->app->singleton(Guest::class, function ($app) {

            return Guest::instance();
        });

    }
}
