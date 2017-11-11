<?php

namespace Modules\Auth\Console;

use Illuminate\Console\Command;
use Modules\Auth\Foundation\Router;
use Symfony\Component\Console\Input\InputArgument;

class ModulePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'module:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect specified module all pure permissions.';

    /**
     * The router instance.
     *
     * @var \Modules\Auth\Foundation\Router
     */
    protected $router;

    /**
     * An array of all the registered routes.
     *
     * @var \Illuminate\Routing\RouteCollection
     */
    protected $routes;

    /**
     * The code collection file name.
     *
     * @var string
     */
    protected $codeFileName = 'codes.php';


    /**
     * Create a new command instance.
     */
    public function __construct(Router $router)
    {
        parent::__construct();

        $this->router = $router;
        $this->routes = $router->getRoutes();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::OPTIONAL, 'The name of the module'],
        ];
    }

    /**
     * Convert the module name to uppercase.
     *
     * @return string
     */
    protected function getModuleName()
    {
        return $this->argument('name');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->fire();
    }

    /**
     * Execute the console command.
     */
    public function fire()
    {
        $this->info('[');
        foreach ($this->routes as $route) {

            if (preg_match('/^api\/' . strtolower($this->getModuleName()) . '\/.*/', $route->uri)) {

                $this->info('  \'' . strtolower($route->methods()[0]) . '@' . $route->uri() . '\',');
            }
        }
        $this->info(']');
    }

}