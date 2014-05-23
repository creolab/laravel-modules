<?php namespace Creolab\LaravelModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Composer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
* Modules console commands
* @author Boris Strahija <bstrahija@gmail.com>
*/
abstract class AbstractCommand extends Command {

	/**
	 * List of all available modules
	 *
	 * @var array
	 */
	protected $modules;

	/**
	 * IoC
	 *
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * DI
	 *
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		parent::__construct();
		$this->app = $app;
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

	/**
	 * Reformats the modules list for table display
	 *
	 * @return array
	 */
	public function getModules()
	{
		$results = array();

		foreach($this->app['modules']->modules() as $name => $module)
		{
			$path = str_replace(app()->make('path.base'), '', $module->path());

			$results[] = array(
				'name'    => $name,
				'path'    => $path,
				'order'   => $module->order,
				'enabled' => $module->enabled() ? 'true' : '',
			);
		}

		return array_filter($results);
	}


	/**
	 * Return a given module
	 *
	 * @param $module_name
	 * @return mixed
	 */
	public function getModule($module_name)
	{
		foreach ($this->getModules() as $module) {
			if ($module['name'] == $module_name) {
				return $module;
			}
		}
		return false;
	}

	/**
	 * Display a module info table in the console
	 *
	 * @param  array $modules
	 * @return void
	 */
	public function displayModules($modules)
	{
		// Get table helper
		$this->table = $this->getHelperSet()->get('table');

		$headers = array('Name', 'Path', 'Order', 'Enabled');

		$this->table->setHeaders($headers)->setRows($modules);

		$this->table->render($this->getOutput());
	}

	/**
	 * Dump autoload classes
	 *
	 * @return void
	 */
	public function dumpAutoload()
	{
		// Also run composer dump-autoload
		$composer = new Composer($this->app['files']);
		$this->info('Generating optimized class loader');
		$composer->dumpOptimized();
		$this->line('');
	}
}
