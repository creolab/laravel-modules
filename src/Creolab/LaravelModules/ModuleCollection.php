<?php namespace Creolab\LaravelModules;

use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

/**
 * Single module definition
 * @author Boris Strahija <bstrahija@gmail.com>
 */
class ModuleCollection extends Collection {

	/**
	 * List of all modules
	 * @var array
	 */
	public $items = array();

	/**
	 * IoC
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * Initialize a module collection
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * Initialize all modules
	 * @return void
	 */
	public function registerModules()
	{
		foreach ($this->items as $module)
		{
			$module->register();
		}
	}

}
