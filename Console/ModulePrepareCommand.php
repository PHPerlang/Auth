<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ModulePrepareCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'module:prepare';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Develop a module completely and ready to deploy.';


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

    /**
     * Execute the console command.
     */
    public function fire()
    {
        $this->call('module:make-codes', ['name' => $this->argument('name')]);
        $this->call('module:make-permissions', ['name' => $this->argument('name')]);
    }


}