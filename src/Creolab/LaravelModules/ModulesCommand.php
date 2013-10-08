<?php namespace Creolab\LaravelModules;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
* Modules console commands
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
	 * Path to the modules monifest
	 * @var string
	 */
	protected $manifestPath;

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

		foreach(app('modules')->modules() as $name => $module)
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
