<?php namespace Creolab\LaravelModules;

use Illuminate\Foundation\Application;

/**
 * Monifest for scanned modules
 * @author Boris Strahija <bstrahija@gmail.com>
 */
class Manifest {

	/**
	 * Path to manifest file
	 * @var string
	 */
	protected $path;

	/**
	 * Manifest data
	 * @var array
	 */
	protected $data;

	/**
	 * IoC
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * Initialize the manifest
	 * @param Application $app [description]
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;

		// Path to manifest file
		$this->path = storage_path('meta/modules.json');

		// Try to read the file
		if ($this->app['files']->exists($this->path))
		{
			$this->data = @json_decode($this->app['files']->get($this->path), true);
		}
	}

	/**
	 * Save the manifest data
	 * @return void
	 */
	public function save($modules)
	{
		// Prepare manifest data
		foreach ($modules as $module)
		{
			$this->data[$module->name()] = $module->def();
		}

		// Cache it
		try
		{
			$this->app['files']->put($this->path, $this->app['modules']->prettyJsonEncode($this->data));
		}
		catch(\Exception $e)
		{
			$this->app['log']->error("[MODULES] Failed when saving manifest file: " . $e->getMessage());
		}

		return $this->data;
	}

	/**
	 * Get the manifest data as an array
	 * @return array
	 */
	public function toArray($module = null)
	{
		if ($module) return $this->data[$module];
		else         return $this->data;
	}

	/**
	 * Delete the manifest file
	 * @return void
	 */
	public function delete()
	{
		$this->data = null;

		$this->app['files']->delete($this->path);
	}

}
