<?php namespace Creolab\LaravelModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Composer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Modules console commands
 * @author Boris Strahija <bstrahija@gmail.com>
 */
class ModulesMigrateCommand extends AbstractCommand
{

    /**
     * Name of the command
     * @var string
     */
    protected $name = 'modules:migrate';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Run migrations for modules.';

    protected $migrationList = array();

    /**
     * Execute the console command.
     * @return void
     */
    public function fire()
    {
        $this->info('Migrating modules');

        // Get all modules or 1 specific
        if ($moduleName = $this->input->getArgument('module')) {
            $modules = array(app('modules')->module($moduleName));
        } else {
            $modules = app('modules')->modules();
        }

        foreach ($modules as $module) {
            if ($module) {
                if ($this->app['files']->exists($module->path('migrations'))) {
                    // Prepare params
                    $path = ltrim(str_replace(app()->make('path.base'), '', $module->path()), "/") . "/migrations";
                    $_info = array('path' => $path);
                    //add to migration list
                    array_push($this->migrationList, $_info);
                    $this->info("[v] Added '" . $module->name() . "' to migration list.");

                } else {
                    $this->line("Module <info>'" . $module->name() . "'</info> has no migrations.");
                }
            } else {
                $this->error("Module '" . $moduleName . "' does not exist.");
            }
        }

        if (count($this->migrationList)) {
            $this->info("[i] Running Migrations...");
            //process migration list
            $this->runPathsMigration();
        }

        if ($this->input->getOption('seed')) {
            $this->info("[i] Running Seeding Command...");
            $this->call('modules:seed');
        }

    }

    protected function runPathsMigration()
    {
        $_fileService = new Filesystem();
        $_tmpPath     = app_path('storage') . DIRECTORY_SEPARATOR . 'migrations';

        if (!is_dir($_tmpPath) && !$_fileService->exists($_tmpPath)) {
            $_fileService->mkdir($_tmpPath);
        }

        $this->info("[i] Gathering migration files to {$_tmpPath}");

        //copy all files to storage/migrations
        foreach ($this->migrationList as $migration) {
            $_fileService->mirror($migration['path'], $_tmpPath);
        }

        //call migrate command on temporary path
        $this->info("[i] Migrating...");
        $this->call('migrate', array('--path' => ltrim(str_replace(base_path(), '', $_tmpPath), '/')));

        //delete all temp migration files
        $this->info("[i] Cleaning temporary files");
        $_fileService->remove($_tmpPath);
        //done
        $this->info("[i] DONE!");
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('module', InputArgument::OPTIONAL, 'The name of module being migrated.'),
        );
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('seed', null, InputOption::VALUE_NONE, 'Indicates if the module should seed the database.',),
        );
    }

}
