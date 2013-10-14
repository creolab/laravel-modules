<?php namespace Creolab\LaravelModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
* List modules
* @author Boris Strahija <bstrahija@gmail.com>
*/
class ModulesCommand extends Command {

	/**
	 * Name of the command
	 * @var string
	 */
	protected $name = 'modules';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Just return all registered modules.';

	/**
	 * List of all available modules
	 * @var array
	 */
	protected $modules;

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
		// Get table helper
		$this->table = $this->getHelperSet()->get('table');

		// Return error if no modules found
		if (count($this->getModules()) == 0)
		{
			return $this->error("Your application doesn't have any registered modules.");
		}

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
	 * Display a module info table in the console
	 * @param  array $modules
	 * @return void
	 */
	public function displayModules($modules)
	{
		$headers = array('Name', 'Path', 'Order', 'Enabled');

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
