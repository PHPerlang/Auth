<?php

namespace Modules\Auth\Console;

use Modules\Auth\Models\Permission;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Jindowin\Commands\ModuleDeployCommand as JindowinModuleDeployCommand;

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
        $this->deploy();
    }

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

    protected function deploy()
    {
        $module = $this->argument('name');

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

    protected function deployResources()
    {

    }

    protected function deployPermissions($module, array $permissions)
    {
        $collection = collect($permissions);

        $registerPermissions = $collection->keys();

        Permission::where('module_id', $module)
            ->whereNotIn('permission_id', $registerPermissions)
            ->delete();

        $existPermissions = Permission::where('module_id', $module)
            ->pluck('permission_id');

        $collection->each(function ($item, $key) use ($existPermissions) {


            if (!in_array($key, $existPermissions->toArray())) {

                $item['permission_id'] = $key;
                Permission::create($item);
            }
        });

    }

    /**
     * Deploy the default roles without attached members.
     */
    protected function deployRoles()
    {

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