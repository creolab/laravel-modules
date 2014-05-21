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
class ModulesGenerateCommand extends AbstractCommand {

	/**
	 * Name of the command
	 * @var string
	 */
	protected $name = 'modules:generate';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Generate module resources.';

	/**
	 * Execute the console command.
	 * @return void
	 */
	public function fire()
	{
		// Name of new module
		$moduleName = $this->input->getArgument('module');
		$modulePath = $this->app['config']->get('modules::path');
		$type       = $this->input->getArgument('type');
		$resource   = $this->input->getArgument('resource');

		// Generate a controller
		if ($type == 'controller')
		{
			$dirPath = $modulePath . '/' . $moduleName . '/controllers';
			$this->call('generate:controller', array('controllerName' => $resource, '--path' => $dirPath));
		}

		// Generate a models
		if ($type == 'model')
		{
			$dirPath = $modulePath . '/' . $moduleName . '/models';
			$this->call('generate:model', array('modelName' => $resource, '--path' => $dirPath));
		}

		// Generate a migration
		if ($type == 'migration')
		{
			$dirPath = $modulePath . '/' . $moduleName . '/migrations';
			$this->call('generate:migration', array('migrationName' => $resource, '--path' => $dirPath));
		}

		// Generate a views
		if ($type == 'view')
		{
			$dirPath = $modulePath . '/' . $moduleName . '/views';
			$this->call('generate:view', array('viewName' => $resource, '--path' => $dirPath));
		}
	}

	/**
	 * Get the console command arguments.
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('module',   InputArgument::REQUIRED, 'The name of module.'),
			array('type',     InputArgument::REQUIRED, 'Type of resource you want to generate.'),
			array('resource', InputArgument::REQUIRED, 'Name of resource.'),
		);
	}

	/**
	 * Get the console command options.
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('path', null, InputOption::VALUE_OPTIONAL, 'Path to the directory.'),
			array('template', null, InputOption::VALUE_OPTIONAL, 'Path to template.')
		);
	}

}
