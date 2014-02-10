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
		// First we need to sort the modules
		$this->sort(function($a, $b) {
			if ($a->order == $b->order) return 0;
			else                        return $a->order < $b->order ? -1 : 1;
		});

		// Then register each one
		foreach ($this->items as $module)
		{
			$module->register();
		}
	}

}
