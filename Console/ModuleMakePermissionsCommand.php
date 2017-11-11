<?php

namespace Modules\Auth\Console;

use Illuminate\Console\Command;
use Modules\Auth\Foundation\Route;
use Modules\Auth\Foundation\Router;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMakePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'module:make-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate specified module permissions locale files from routes.';

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
     * The permissions collection file name.
     *
     * @var string
     */
    protected $collectionFileName = 'permissions.php';

    /**
     * The permission id collection file name.
     *
     * @var string
     */
    protected $listFileName = 'permissions.list';

    /**
     * The Module Name.
     *
     * @var null
     */
    protected $module = null;

    /**
     * Register in the deploy folder permissions file fields.
     *
     * @var array
     */
    protected $deployFields = [
        'module' => '',
        'name' => '',
        'description' => '',
        'guard' => [],
    ];

    /**
     * Register in the locale files fields.
     *
     * @var array
     */
    protected $localeFields = [
        'name' => '',
        'description' => '',
    ];

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
        $this->module = $this->argument('name');

        if ($this->getModuleName()) {

            $this->makeFiles();
        } else {
            foreach (app('modules')->getOrdered() as $name => $module) {
                $this->module = $name;
                $this->makeFiles();
            };
        }
    }


    /**
     * Convert the module name to uppercase.
     *
     * @return string
     */
    protected function getModuleName()
    {
        return $this->module;
    }

    /**
     * Get the modules base path.
     *
     * @return string
     */
    protected function getModuleBasePath()
    {
        return app('path.base') . DIRECTORY_SEPARATOR . 'modules';
    }

    /**
     * Get the path of the module.
     *
     * @return string
     */
    protected function getModulePath()
    {
        return $this->getModuleBasePath() . DIRECTORY_SEPARATOR . $this->getModuleName();
    }

    /**
     * Get the module deploy folder path.
     *
     * @return string
     */
    protected function getDeployPath()
    {
        return $this->getModulePath() . DIRECTORY_SEPARATOR . 'Config';
    }

    /**
     * Get the module resource lang path.
     *
     * @return string
     */
    protected function getLangPath()
    {

        $dir = $this->getModulePath() . DIRECTORY_SEPARATOR . str_replace(app('path.base'), '', app('path.lang'));

        if (!is_dir($dir)) {

            $this->error('The lang directory is not exist: ' . $dir);
            die();
        }

        return $dir;
    }

    /**
     * Generate the permission id.
     *
     * @param string $method
     * @param string $uri
     *
     * @return string
     */
    protected function generatePermissionId($method, $uri)
    {
        return strtolower($method) . '@' . $uri;

    }

    /**
     * Generate the permission list id.
     *
     * @param string $method
     * @param string $uri
     *
     * @return string
     */
    protected function generatePermissionListId($method, $uri)
    {
        return $this->generatePermissionId($method, $uri);
    }

    /**
     * Check the route is a permission.
     *
     * @param Route $route
     *
     * @return bool
     */
    protected function checkRoute($route)
    {
        $namespace = isset($route->getAction()['namespace']) ? $route->getAction()['namespace'] : null;
        if ($namespace) {
            return preg_match(preg_quote('/Modules\\' . $this->getModuleName() . '/', '\\'), $namespace)
                && (preg_match('/^api\/.*/', $route->uri) || preg_match('/^admin\/.*/', $route->uri))
                && !$route->open;
        }
        return false;
    }

    /**
     * Make deploy file content
     *
     * @return array
     */
    protected function makeDeployContent()
    {

        $content = [];

        foreach ($this->routes as $route) {

            if ($this->checkRoute($route)) {

                $permission_id = $this->generatePermissionId($route->methods()[0], $route->uri());
                $content[$permission_id] = $this->deployFields;
                $content[$permission_id]['module'] = $this->getModuleName();

            }

        }

        return $content;
    }

    /**
     * Make permission list content.
     *
     * @return array
     */
    protected function makeListContent()
    {
        $content = [];

        foreach ($this->routes as $route) {

            if ($this->checkRoute($route)) {

                array_push($content, $this->generatePermissionListId($route->methods()[0], $route->uri()));
            }

        }

        return $content;
    }


    /**
     * Make Locale file content.
     *
     * @return array
     */
    protected function makeLocaleContent()
    {
        $content = [];

        foreach ($this->routes as $route) {

            if ($this->checkRoute($route)) {

                $permission_id = $this->generatePermissionId($route->methods()[0], $route->uri());
                $content[$permission_id] = $this->localeFields;
            }
        }

        return $content;
    }


    /**
     * Create file.
     *
     * @param string $file
     * @param array $content
     */
    protected function createFile($file, $content)
    {
        $this->writePermissions($file, $content);
        $this->info('Create: ' . $file);
    }


    /**
     * Update the exist locale file.
     *
     * @param string $file
     * @param array $origin
     * @param array $content
     *
     */
    protected function updateFile($file, $origin, $content)
    {
        try {

            foreach ($origin as $k => $v) {

                if (array_key_exists($k, $content)) {

                    if (is_array($v)) {

                        foreach ($origin[$k] as $code => $message) {

                            if (array_key_exists($code, $content[$k])) {

                                $content[$k][$code] = $message;

                            } else {

                                unset($content[$k][$code]);
                            }
                        }

                    } else {

                        unset($content[$k]);
                    }

                } else {

                    unset($content[$k]);
                }

            }

            $this->writePermissions($file, $content);
            $this->warn('Update: ' . $file);


        } catch (\Exception $e) {

            $this->error('Delete: ' . $file);
            $this->createFile($file, $content);
        }

    }

    /**
     * Write permissions list file.
     *
     * @param $file
     * @param $content
     */
    protected function writePermissionsList($file, $content)
    {
        if (file_exists($file)) {

            $this->warn('Update: ' . $file);

        } else {

            $this->info('Create: ' . $file);
        }

        file_put_contents($file, '[' . PHP_EOL);

        foreach ($content as $permission) {

            file_put_contents($file, '  \'' . $permission . '\',' . PHP_EOL, FILE_APPEND);
        }

        file_put_contents($file, ']' . PHP_EOL, FILE_APPEND);

    }

    /**
     * Write permissions to file.
     *
     * @param string $file
     * @param string $content
     */
    protected function writePermissions($file, $content)
    {
        file_put_contents($file, '<?php' . PHP_EOL . PHP_EOL . 'return [' . PHP_EOL);

        foreach ($content as $id => $data) {

            file_put_contents($file, "    '$id' => [ " . PHP_EOL, FILE_APPEND);

            foreach ($data as $field => $value) {

                if (is_string($value)) {

                    file_put_contents($file, "        '$field' => '$value'," . PHP_EOL, FILE_APPEND);

                } else if (is_array($value)) {

                    if (count($value) > 0) {

                        file_put_contents($file, "        '$field' => [" . PHP_EOL, FILE_APPEND);

                        foreach ($value as $v) {
                            file_put_contents($file, "            '$v'," . PHP_EOL, FILE_APPEND);
                        }

                        file_put_contents($file, "        ]," . PHP_EOL, FILE_APPEND);

                    } else {

                        file_put_contents($file, "        '$field' => []," . PHP_EOL, FILE_APPEND);
                    }

                } else if ($value == '') {

                    file_put_contents($file, "        '$field' => ''," . PHP_EOL, FILE_APPEND);

                } else {

                    file_put_contents($file, "        '$field' => $value," . PHP_EOL, FILE_APPEND);
                }
            }

            file_put_contents($file, "    ]," . PHP_EOL, FILE_APPEND);

        }

        file_put_contents($file, "];" . PHP_EOL, FILE_APPEND);
    }

    /**
     * Make module permissions file.
     */
    protected function makeFiles()
    {

        $deployFile = $this->getDeployPath() . DIRECTORY_SEPARATOR . $this->collectionFileName;

        if (file_exists($deployFile)) {

            $this->updateFile($deployFile, require $deployFile, $this->makeDeployContent());

        } else {

            $this->createFile($deployFile, $this->makeDeployContent());
        }

        $listFile = $this->getDeployPath() . DIRECTORY_SEPARATOR . $this->listFileName;

        $this->writePermissionsList($listFile, $this->makeListContent());

        $locales = list_dirs($this->getLangPath());

        foreach ($locales as $locale) {

            $localeFile = $locale . DIRECTORY_SEPARATOR . $this->collectionFileName;

            if (file_exists($localeFile)) {

                $this->updateFile($localeFile, require $localeFile, $this->makeLocaleContent());

            } else {

                $this->createFile($localeFile, $this->makeLocaleContent());
            }

        }

    }

}