<?php namespace Creolab\LaravelModules;

use Illuminate\Console\Command;
use Illuminate\Foundation\Composer;
use Illuminate\Foundation\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
* Scan available modules
* @author Boris Strahija <bstrahija@gmail.com>
*/
class ModulesScanCommand extends Command {

	/**
	 * Name of the command
	 * @var string
	 */
	protected $name = 'modules:scan';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Scan modules and cache module meta data.';

	/**
	 * List of all available modules
	 * @var array
	 */
	protected $modules;

	/**
	 * Path to the modules monifest
	 * @var string
	 */
	protected $manifestPath;

	/**
	 * IoC
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * DI
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		parent::__construct();
		$this->app = $app;
	}

	/**
	 * Execute the console command.
	 * @return void
	 */
	public function fire()
	{
		$this->info('Scanning modules');

		// Get table helper
		$this->table = $this->getHelperSet()->get('table');

		// Delete the manifest
		$this->app['modules']->deleteManifest();

		// Run the scanner
		$this->modules = $this->app['modules']->scan();

		// Return error if no modules found
		if (count($this->modules) == 0)
		{
			return $this->error("Your application doesn't have any valid modules.");
		}

		// Also run composer dump-autoload
		$composer = new Composer($this->app['files']);
		$this->info('Generating optimized class loader');
		$composer->dumpOptimized();
		$this->line('');

		// Display number of found modules
		$this->info('Found ' . count($this->modules) . ' modules:');

		// Display the modules info
		$this->displayModules($this->getModules());
	}

	/**
	 * Reformats the modules list for table display
	 * @return array
	 */
	public function getModules()
	{
		$results = array();

		foreach($this->modules as $name => $module)
		{
			$path = str_replace(app()->make('path.base'), '', $module->path());

			$results[] = array(
				'name'    => $name,
				'path'    => $path,
				'enabled' => $module->enabled() ? 'true' : '',
			);
		}

		return array_filter($results);
	}

	/**
	 * Display a module info table in the console
	 * @param  array $modules
	 * @return void
	 */
	public function displayModules($modules)
	{
		$headers = array('Name', 'Path', 'Enabled');

		$this->table->setHeaders($headers)->setRows($modules);

		$this->table->render($this->getOutput());
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
