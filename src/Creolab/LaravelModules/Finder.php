<?php namespace Creolab\LaravelModules;

use Illuminate\Foundation\Application;

/**
 * Module finder
 * @author Boris Strahija <bstrahija@gmail.com>
 */
class Finder{

	/**
	 * IoC
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * Modules collection
	 * @var ModuleCollection
	 */
	protected $modules;

	/**
	 * Initialize the finder
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->app     = $app;
		$this->modules = new ModuleCollection($app);
	}

	/**
	 * Return module collection
	 * @return ModuleCollection
	 */
	public function modules()
	{
		return $this->modules;
	}

	/**
	 * Return single module
	 * @param  string $id
	 * @return Module
	 */
	public function module($id)
	{
		if (isset($this->modules[$id])) return $this->modules[$id];
	}

	/**
	 * Scan module folder and add valid modules to collection
	 * @return array
	 */
	public function scan()
	{
		// Get all directories in modules path
		$directories = $this->app['files']->directories(base_path($this->app['config']->get('modules::path')));

		if ($directories)
		{
			foreach ($directories as $directory)
			{
				// Check if dir contains a module definition file
				if ($this->app['files']->exists($directory . '/module.json'))
				{
					$name                 = pathinfo($directory, PATHINFO_BASENAME);
					$this->modules[$name] = new Module($name, $directory, null, $this->app);
				}
			}
		}

		return $this->modules;
	}

	/**
	 * Get modules from config array
	 * @return array
	 */
	public function manual()
	{
		$modules = $this->app['config']->get('modules::modules');

		if ($modules)
		{
			foreach ($modules as $key => $module)
			{
				// Get name first
				if     (is_string($module)) $name = $module;
				elseif (is_array($module))  $name = $key;

				// The path
				$path = base_path($this->app['config']->get('modules::path') . '/' . $name);

				// Then the definition
				$definition = (is_array($module)) ? $module : array();

				// Create instance
				$this->modules[$name] = new Module($name, $path, $definition, $this->app);
			}
		}

		return $this->modules;
	}

	/**
	 * Register all modules in collection
	 * @return void
	 */
	public function register()
	{
		return $this->modules->registerModules();
	}

	/**
	 * Log a debug message
	 * @param  string $message
	 * @return void
	 */
	public function logDebug($message)
	{
		return $this->log($message);
	}

	/**
	 * Log an error message
	 * @param  string $message
	 * @return void
	 */
	public function logError($message)
	{
		return $this->log($message, 'error');
	}

	/**
	 * Log a message
	 * @param  string $type
	 * @param  string $message
	 * @return void
	 */
	public function log($message, $type = 'debug')
	{
		if ($this->app['config']->get('modules::debug'))
		{
			$namespace = 'MODULES';
			$message   = "[$namespace] $message";

			if ($type == 'error') return $this->app['log']->error($message);
			else                  return $this->app['log']->debug($message);
		}
	}

}
