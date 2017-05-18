<?php

namespace Modules\Core\Console;

use Modules\Core\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    /**
     * Create a new console kernel instance.
     *
     * @param  \Modules\Core\Foundation\Application $app
     * @param  \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function __construct(Application $app, Dispatcher $events)
    {
        parent::__construct($app, $events);
    }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Closure based command files.
     *
     * @var array
     */
    protected $commandClosures = [];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        foreach ($this->commandClosures as $closure) {

            $closure();
        }
    }

    /**
     * Add the command to the $commands.
     *
     * @param string $command
     */
    public function addCommand($command)
    {

        array_push($this->commands, $command);
    }


    /**
     * Add closure based command files.
     *
     * @param  $closure
     */
    public function addCommandClosures($closure)
    {
        array_push($this->commandClosures, $closure);
    }

}
