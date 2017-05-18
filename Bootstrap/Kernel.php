<?php

namespace Modules\Core\Bootstrap;

use Illuminate\Console\Scheduling\Schedule;

class Kernel
{

    /*
    |--------------------------------------------------------------------------
    | Auto loaded module files
    |--------------------------------------------------------------------------
    |
    */
    public $files = [];

    /*
    |--------------------------------------------------------------------------
    | Auto loaded service providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */
    public $providers = [
        \Modules\Core\Providers\CoreServiceProvider::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Class aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */
    public $aliases = [
    ];


    /*
    |--------------------------------------------------------------------------
    | The artisan commands provided by your module
    |--------------------------------------------------------------------------
    |
    */
    public $commands = [
        \Modules\Core\Console\ModuleMakeCodesCommand::class,
        \Modules\Core\Console\ModuleRouteListCommand::class,
        \Modules\Core\Console\ModuleMakePermissionsCommand::class,
        \Modules\Core\Console\ModulePermissionsCommand::class,
        \Modules\Core\Console\ModulePrepareCommand::class,
        \Modules\Core\Console\ModuleDeployCommand::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Define the application's command schedule
    |--------------------------------------------------------------------------
    |
    */
    public function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    /*
    |--------------------------------------------------------------------------
    | Register the Closure based commands for the module
    |--------------------------------------------------------------------------
    |
    */
    public function commands()
    {
        require base_path('routes/console.php');
    }

    /*
    |--------------------------------------------------------------------------
    | The application's global HTTP middleware stack
    |--------------------------------------------------------------------------
    |
    | These middleware are run during every request to your application.
    |
    */
    public $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Modules\Core\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];


    /*
    |--------------------------------------------------------------------------
    | The application's route middleware groups
    |--------------------------------------------------------------------------
    |
    */
    public $middlewareGroups = [

        'api' => [
            'throttle:60,1',
            'bindings',
            \Modules\Core\Http\Middleware\ResolveStatusMiddleware::class,
            \Modules\Core\Http\Middleware\ResolveClientMiddleware::class,
            \Modules\Core\Http\Middleware\PermissionGuardMiddleware::class,
        ]
    ];


    /*
    |--------------------------------------------------------------------------
    | The application's route middleware
    |--------------------------------------------------------------------------
    |
    | These middleware may be assigned to groups or used individually
    |
    */
    public $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \Modules\Core\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    ];

}