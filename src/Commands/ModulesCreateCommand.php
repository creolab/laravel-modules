<?php namespace Creolab\LaravelModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Composer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
* Command for creating a new module
* @author Boris Strahija <bstrahija@gmail.com>
*/
class ModulesCreateCommand extends AbstractCommand {

	/**
	 * Name of the command
	 * @var string
	 */
	protected $name = 'modules:create';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Create a new module.';

	/**
	 * Execute the console command.
	 * @return void
	 */
	public function fire()
	{
		// Name of new module
		$moduleName = $this->input->getArgument('module');
		$this->info('Creating module "'.$moduleName.'"');

		// Chech if module exists
		$exists = app('modules')->module($moduleName);

		if ( ! app('modules')->module($moduleName))
		{
			// Get path to modules
			$modulePath = $this->app['config']->get('modules::path');
			if (is_array($modulePath)) $modulePath = $modulePath[0];
			$modulePath .= '/' . $moduleName;

			// Create the directory
			if ( ! $this->app['files']->exists($modulePath))
			{
				$this->app['files']->makeDirectory($modulePath, 0755);
			}

			// Create definition and write to file
			$definition = $this->app['modules']->prettyJsonEncode(array('enabled' => true));
			$this->app['files']->put($modulePath . '/module.json', $definition);

			// Create routes and write to file
			$routes = '<?php' . PHP_EOL;
			$this->app['files']->put($modulePath . '/routes.php', $routes);

			// Create some resource directories
			$this->app['files']->makeDirectory($modulePath . '/assets', 0755);
			$this->app['files']->makeDirectory($modulePath . '/config', 0755);
			$this->app['files']->makeDirectory($modulePath . '/controllers', 0755);
			$this->app['files']->makeDirectory($modulePath . '/lang', 0755);
			$this->app['files']->makeDirectory($modulePath . '/models', 0755);
			$this->app['files']->makeDirectory($modulePath . '/migrations', 0755);
			$this->app['files']->makeDirectory($modulePath . '/views', 0755);

			// Autoload classes
			$this->dumpAutoload();
		}
		else
		{
			$this->error('Module with name "'.$moduleName.'" already exists.');
		}
	}

	/**
	 * Get the console command arguments.
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('module', InputArgument::REQUIRED, 'The name of module being created.'),
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
