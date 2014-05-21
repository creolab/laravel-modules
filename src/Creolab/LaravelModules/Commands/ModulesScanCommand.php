<?php namespace Creolab\LaravelModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Composer;
use Illuminate\Foundation\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
* Scan available modules
* @author Boris Strahija <bstrahija@gmail.com>
*/
class ModulesScanCommand extends AbstractCommand {

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
		$this->dumpAutoload();

		// Display number of found modules
		$this->info('Found ' . count($this->modules) . ' modules:');

		// Display the modules info
		$this->displayModules($this->getModules());
	}

}
