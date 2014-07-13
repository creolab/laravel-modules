<?php namespace Creolab\LaravelModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Composer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Migrate command
 *
 * @author Boris Strahija <bstrahija@gmail.com>
 */
class ModulesMigrateCommand extends AbstractCommand {

	/**
	 * Name of the command
	 *
	 * @var string
	 */
	protected $name = 'modules:migrate';

	/**
	 * Command description
	 *
	 * @var string
	 */
	protected $description = 'Run migrations for modules.';

	/**
	 * List of migrations
	 *
	 * @var array
	 */
	protected $migrationList = array();

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->info('Migrating modules');

		// Get all modules or 1 specific
		if ($moduleName = $this->input->getArgument('module'))
		{
			$modules = array(app('modules')->module($moduleName));
		}
		else
		{
			$modules = app('modules')->modules();
		}

		foreach ($modules as $module)
		{
			if ($module)
			{
				if ($this->app['files']->exists($module->path('migrations')))
				{
					// Prepare params
					$path  = ltrim(str_replace(app()->make('path.base'), '', $module->path()), "/") . "/migrations";
					$_info = array('path' => $path);

					// Add to migration list
					array_push($this->migrationList, $_info);
					$this->info("Added '" . $module->name() . "' to migration list.");

				}
				else
				{
					$this->line("Module <info>'" . $module->name() . "'</info> has no migrations.");
				}
			}
			else
			{
				$this->error("Module '" . $moduleName . "' does not exist.");
			}
		}

		if (count($this->migrationList))
		{
			$this->info("
				Running Migrations...");

			// Process migration list
			$this->runPathsMigration();
		}

		if ($this->input->getOption('seed'))
		{
			$this->info("Running Seeding Command...");
			$this->call('modules:seed');
		}
	}

	/**
	 * Run paths migrations
	 *
	 * @return void
	 */
	protected function runPathsMigration()
	{
		$_fileService = new Filesystem();
		$_tmpPath     = app_path('storage') . DIRECTORY_SEPARATOR . 'migrations';

		if (!is_dir($_tmpPath) && !$_fileService->exists($_tmpPath))
		{
			$_fileService->mkdir($_tmpPath);
		}

		$this->info("Gathering migration files to {$_tmpPath}");

		// Copy all files to storage/migrations
		foreach ($this->migrationList as $migration)
		{
			$_fileService->mirror($migration['path'], $_tmpPath);
		}

		//call migrate command on temporary path
		$this->info("Migrating...");

		$opts = array('--path' => ltrim(str_replace(base_path(), '', $_tmpPath), '/'));

		if($this->input->getOption('force')) {
		 	$opts['--force'] = true;
 		}

 		if ($this->input->getOption('database')) {
 			$opts['--database'] = $this->input->getOption('database');
 		}

		$this->call('migrate', $opts);

		// Delete all temp migration files
		$this->info("Cleaning temporary files");
		$_fileService->remove($_tmpPath);

		// Done
		$this->info("DONE!");

	}

	/**
	 * Get the console command arguments.
	 *
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
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('seed', null, InputOption::VALUE_NONE, 'Indicates if the module should seed the database.'),
			array('force', '-f', InputOption::VALUE_NONE, 'Force the operation to run when in production.'),
			array('database', null, InputOption::VALUE_OPTIONAL, 'The database connection.', null)
		);
	}

}
