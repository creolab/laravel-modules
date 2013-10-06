<?php namespace Creolab\LaravelModules;

use Illuminate\Foundation\Application;

/**
 * Single module definition
 * @author Boris Strahija <bstrahija@gmail.com>
 */
class Module extends \Illuminate\Support\ServiceProvider {

	/**
	 * Name of the module
	 * @var string
	 */
	protected $name;

	/**
	 * Path to module directory
	 * @var string
	 */
	protected $path;

	/**
	 * Path to module definition JSON file
	 * @var string
	 */
	protected $definitionPath;

	/**
	 * Module definition
	 * @var array
	 */
	protected $definition;

	/**
	 * Is the module enabled
	 * @var boolean
	 */
	protected $enabled = true;

	/**
	 * IoC
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * Initialize a module
	 * @param Application $app
	 */
	public function __construct($name, $path = null, $definition = null, Application $app)
	{
		$this->name = $name;
		$this->app  = $app;
		$this->path = $path;

		// Get definition
		if ($path and ! $definition)
		{
			$this->definitionPath = $path . '/module.json';
		}
		elseif (is_array($definition))
		{
			$this->definition = $definition;
		}

		// Try to get the definition
		$this->readDefinition();
	}

	/**
	 * Read the module definition
	 * @return array
	 */
	public function readDefinition()
	{
		if ($this->app['config']->get('modules::mode') == 'auto')
		{
			if ($this->definitionPath)
			{
				$this->definition = @json_decode($this->app['files']->get($this->definitionPath), true);

				if ( ! $this->definition or (isset($this->definition['enabled']) and $this->definition['enabled'] === false))
				{
					$this->enabled = false;
				}
			}
			else
			{
				$this->enabled = false;
			}
		}
		else
		{
			if ((isset($this->definition['enabled']) and $this->definition['enabled'] === false))
			{
				$this->enabled = false;
			}
		}

		return $this->definition;
	}

	/**
	 * Register the module if enabled
	 * @return boolean
	 */
	public function register()
	{
		if ($this->enabled)
		{
			// Register module as a package
			$this->package('app/' . $this->name, $this->name, $this->path());

			// Require module helpers
			$helpers = $this->path('helpers.php');
			if ($this->app['files']->exists($helpers)) require $helpers;

			// Require module filters
			$filters = $this->path('filters.php');
			if ($this->app['files']->exists($filters)) require $filters;

			// Require module routes
			$routes = $this->path('routes.php');
			if ($this->app['files']->exists($routes)) require $routes;

			// Log it
			$this->app['modules']->logDebug('Module "' . $this->name . '" has been registered.');
		}
	}

	/**
	 * Module path
	 * @param  string $path
	 * @return string
	 */
	function path($path = null)
	{
		if ($path) return $this->path . '/' . ltrim($path, '/');
		else       return $this->path;
	}

}
