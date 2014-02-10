<?php namespace Creolab\LaravelModules;

use Illuminate\Foundation\Application;

/**
 * Module finder
 * @author Boris Strahija <bstrahija@gmail.com>
 */
class Finder {

	/**
	 * Modules collection
	 * @var ModuleCollection
	 */
	protected $modules;

	/**
	 * The modules manifest
	 * @var Manifest
	 */
	protected $manifest;

	/**
	 * IoC
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * Initialize the finder
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->app      = $app;
		$this->modules  = new ModuleCollection($app);
		$this->manifest = new Manifest($app);
	}

	/**
	 * Start finder
	 * @return void
	 */
	public function start()
	{
		if ($this->app['config']->get('modules::mode') == 'auto')
		{
			$this->app['modules']->scan();
		}
		elseif ($this->app['config']->get('modules::mode') == 'manifest')
		{
			if ($manifest = $this->manifest->toArray())
			{
				$this->app['modules']->manual($this->manifest->toArray());
			}
			else
			{
				$this->app['modules']->scan();
			}
		}
		else
		{
			$this->app['modules']->manual();
		}
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
		// Get the modules directory paths
		$modulesPaths = $this->app['config']->get('modules::path');
		if ( ! is_array($modulesPaths)) $modulesPaths = array($modulesPaths);

		// Now prepare an array with all directories
		$paths = array();
		foreach ($modulesPaths as $modulesPath) $paths[$modulesPath] = $this->app['files']->directories(base_path($modulesPath));

		if ($paths)
		{
			foreach ($paths as $path => $directories)
			{
				if ($directories)
				{
					foreach ($directories as $directory)
					{
						// Check if dir contains a module definition file
						if ($this->app['files']->exists($directory . '/module.json'))
						{
							$name                 = pathinfo($directory, PATHINFO_BASENAME);
							$this->modules[$name] = new Module($name, $directory, null, $this->app, $path);
						}
					}

					// Save the manifest file
					$this->saveManifest();
				}
			}
		}

		return $this->modules;
	}

	/**
	 * Get modules from config array
	 * @return array
	 */
	public function manual($config = null)
	{
		if ( ! $config) $modules = $this->app['config']->get('modules::modules');
		else            $modules = $config;

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
	 * Return manifest object
	 * @return Manifest
	 */
	public function manifest($module = null)
	{
		return $this->manifest->toArray($module);
	}

	/**
	 * Save the manifest file
	 * @param  array $modules
	 * @return void
	 */
	public function saveManifest($modules = null)
	{
		$this->manifest->save($this->modules);
	}

	/**
	 * Delete the manifest file
	 * @return void
	 */
	public function deleteManifest()
	{
		$this->manifest->delete();
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
		$this->log($message);
	}

	/**
	 * Log an error message
	 * @param  string $message
	 * @return void
	 */
	public function logError($message)
	{
		$this->log($message, 'error');
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

			if ($type == 'error') $this->app['log']->error($message);
			else                  $this->app['log']->debug($message);
		}
	}

	/**
	 * Prettify a JSON Encode ( PHP 5.4+ )
	 * @param  mixed $values
	 * @return string
	 */
	public function prettyJsonEncode($values)
	{
		return version_compare(PHP_VERSION, '5.4.0', '>=') ? json_encode($values, JSON_PRETTY_PRINT) : json_encode($values);
	}

}
