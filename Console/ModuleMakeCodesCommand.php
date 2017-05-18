<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Modules\Core\Foundation\Router;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMakeCodesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'module:make-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect specified module all codes from controllers and generate codes lang file';

    /**
     * The router instance.
     *
     * @var \Modules\Core\Foundation\Router
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
     * Execute the console command.
     */
    public function fire()
    {
        if ($this->getModuleName()) {

            $this->makeLocaleFiles();
        }
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
     * Make Locale file content.
     *
     * @param string $lang
     *
     * @return array
     */
    protected function makeLocaleContent($lang = 'zh-CN')
    {
        $content = [];

        foreach ($this->routes as $route) {

            if (preg_match('/^api\/.*/', $route->uri)) {

                $content[strtolower($route->methods()[0]) . '@' . $route->uri()] = $route->codes;
            }
        }

        return $content;
    }

    /**
     * Create locale file.
     *
     * @param string $file
     */
    protected function createLocaleFile($file)
    {
        $this->writeCodes($file, $this->makeLocaleContent());
        $this->info('Create: ' . $file);
    }

    /**
     * Update the exist locale file.
     * @param $file
     */
    protected function updateLocaleFile($file)
    {
        try {

            $locale = require $file;
            $content = $this->makeLocaleContent();

            foreach ($locale as $k => $v) {

                if (array_key_exists($k, $content)) {

                    if (is_array($v)) {

                        foreach ($locale[$k] as $code => $message) {

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

            $this->writeCodes($file, $content);
            $this->info('Update: ' . $file);


        } catch (\Exception $e) {

            $this->warn('Delete: ' . $file);
            $this->createLocaleFile($file);
        }

    }

    /**
     * Write codes to file.
     *
     * @param string $file
     * @param string $content
     */
    protected function writeCodes($file, $content)
    {
        file_put_contents($file, '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($content, true) . ';');
    }

    /**
     * Make module current using codes file.
     */
    protected function makeLocaleFiles()
    {

        $locales = list_dirs($this->getLangPath());

        foreach ($locales as $locale) {

            $file = $locale . DIRECTORY_SEPARATOR . $this->codeFileName;

            if (file_exists($file)) {

                $this->updateLocaleFile($file);


            } else {

                $this->createLocaleFile($file);
            }

        }

    }

}