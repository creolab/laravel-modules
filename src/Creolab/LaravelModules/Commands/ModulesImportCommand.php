<?php namespace Creolab\LaravelModules\Commands;

use Illuminate\Console\Command,
	Illuminate\Foundation\Application,
	Illuminate\Foundation\Composer,
	Symfony\Component\Console\Input\InputOption,
	Symfony\Component\Console\Input\InputArgument;

/**
* Command for importing a remote module
* @author Xander Smalbil <xander@netbulae.eu>
*/
class ModulesImportCommand extends AbstractCommand {

	/**
	 * Name of the command
	 * @var string
	 */
	protected $name = 'modules:import';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Import a module.';

	/**
	 * Execute the console command.
	 * @return void
	 */
	public function fire()
	{

		$modulePath = $this->input->getArgument('module');

		$this->info('Importing module "'.$modulePath.'"');

		$moduleContent = $this->app['files']->getRemote($modulePath);

		$moduleStorage = $this->app['config']->get('modules::path') . '/module.zip';

		$this->info('Saving module "'.$modulePath.'"');

		$this->app['files']->put($moduleStorage, $moduleContent);

		$this->info('Extracting module "'.$modulePath.'"');

		system('unzip -d '. app_path() . ' ' . $moduleStorage);

		$this->app['files']->delete(array($moduleStorage));

		$this->dumpAutoload();

	}

	/**
	 * Get the console command arguments.
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('module', InputArgument::REQUIRED, 'The remote location of the module'),
		);
	}

	/**
	 * Get the console command options.
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
