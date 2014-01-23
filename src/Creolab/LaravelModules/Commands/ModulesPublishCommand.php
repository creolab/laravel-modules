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
class ModulesPublishCommand extends AbstractCommand {

	/**
	 * Name of the command
	 * @var string
	 */
	protected $name = 'modules:publish';

	/**
	 * Command description
	 * @var string
	 */
	protected $description = 'Publish public assets for modules.';

	/**
	 * Execute the console command.
	 * @return void
	 */
	public function fire()
	{
		$this->info('Publishing module assets');

		// Get all modules or 1 specific
		if ($moduleName = $this->input->getArgument('module')) $modules = array(app('modules')->module($moduleName));
		else                                                   $modules = app('modules')->modules();

		foreach ($modules as $module)
		{
			if ($module)
			{
				if ($this->app['files']->exists($module->path('assets')))
				{
					// Group path
					$groupPath = $module->def('group') ? str_replace('/', '_', $module->def('group')) : null;

					// Prepare params
					$path = ltrim(str_replace(app()->make('path.base'), '', $module->path()), "/") . "/assets";

					// Get destination path
					if (is_array($this->app['config']->get('modules::path')))
					{
						$destination = app()->make('path.public') . '/packages/module/' . $groupPath . '/' . $module->name() . '/assets';
					}
					else
					{
						$destination = app()->make('path.public') . '/packages/module/' . $module->name() . '/assets';
					}

					// Try to copy
					$success = $this->app['files']->copyDirectory($path, $destination);

					// Result
					if ( ! $success) $this->line("Unable to publish assets for module '" . $module->name() . "'");
					else             $this->info("Published assets for module '" . $module->name() . "'");
				}
				else
				{
					$this->line("Module <info>'" . $module->name() . "'</info> has no assets available.");
				}
			}
			else
			{
				$this->error("Module '" . $moduleName . "' does not exist.");
			}
		}
	}

	/**
	 * Get the console command arguments.
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('module', InputArgument::OPTIONAL, 'The name of module being published.'),
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
