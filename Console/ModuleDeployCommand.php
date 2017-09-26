<?php

namespace Modules\Auth\Console;

use Modules\Auth\Models\Permission;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Gindowin\Commands\ModuleDeployCommand as JindowinModuleDeployCommand;

class ModuleDeployCommand extends JindowinModuleDeployCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'module:deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy a module.';


    /**
     * The module deploy file relative path.
     *
     * @var string
     */
    protected $deploy = 'deploy.php';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

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

    protected function getOptions()
    {
        return [
            ['down', null, InputOption::VALUE_NONE, 'Drop down module deploy data.'],
        ];
    }

    /**
     * Execute the console command.
     */
    public function fire()
    {
        $module = $this->argument('name');

        if ($module) {
            $this->deploy($module);
        } else {
            foreach (app('modules')->getOrdered() as $name => $module) {
                $this->deploy($name);
            };
        }
    }

    /**
     * Get the deploy file path.
     *
     * @param string $module
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function getDeployFilePath($module)
    {
        $path = base_path(
            'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $this->deploy
        );

        if (!file_exists($path)) {

            throw new \Exception('Can\'t find file: ' . $path);
        }

        return $path;
    }

    /**
     * Exec deploy action.
     *
     * @param string $module
     *
     * @throws \Exception
     */
    protected function deploy($module)
    {

        $deployFile = $this->getDeployFilePath($module);

        $deployConfig = require $deployFile;

        if (!is_array($deployConfig)) {

            throw new \Exception('Deploy file should return a array at：' . $deployFile);
        }

        transaction(function () use ($module, $deployConfig) {

            if (isset($deployConfig['permissions'])) {

                $this->deployPermissions($module, $deployConfig['permissions']);
            }

            if (isset($deployConfig['installer']) && is_array($deployConfig['installer'])) {

                $this->deploySeeds($deployConfig['installer']);
            }
        });

    }


    /**
     * Deploy module permissions.
     *
     * @param string $module
     *
     * @param array $permissions
     */
    protected function deployPermissions($module, array $permissions)
    {
        $collection = collect($permissions);

        $registerPermissions = $collection->keys();

        Permission::where('module', $module)
            ->whereNotIn('permission_id', $registerPermissions)
            ->delete();

        $existPermissions = Permission::where('module', $module)
            ->pluck('permission_id');

        $collection->each(function ($item, $key) use ($existPermissions) {

            if (!in_array($key, $existPermissions->toArray())) {
                $permission = [];
                $permission['permission_id'] = $key;
                $permission['module'] = isset($item['module']) ? $item['module'] : null;
                $permission['permission_name'] = isset($item['name']) && trim($item['name']) != '' ? $item['name'] : $key;
                $permission['permission_desc'] = isset($item['description']) ? $item['description'] : null;
                Permission::create($permission);
            }
        });

    }

    /**
     * Seed the specified module.
     *
     * @param string $className
     *
     * @return array
     */
    protected function dbSeed($className)
    {
        $installer = new $className;

        if ($option = $this->option('down')) {

            if (method_exists($installer, 'down')) {

                $installer->down();
            }

        } else {

            if (method_exists($installer, 'up')) {

                $installer->up();
            }
        }
    }

    /**
     * Exec the database seeds.
     *
     * @param array $seeds
     */
    protected function deploySeeds(array $seeds)
    {
        foreach ($seeds as $seed) {

            $this->dbSeed($seed);
        }
    }
}