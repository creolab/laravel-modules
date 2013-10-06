<?php namespace Creolab\LaravelModules;

class ServiceProvider extends \Illuminate\Support\ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 * @return void
	 */
	public function boot()
	{
		$this->package('creolab/laravel-modules', 'modules', __DIR__ . '/../../');

		// Auto scan if specified
		if ($this->app['config']->get('modules::mode') == 'auto')
		{
			$this->app['modules']->scan();
		}
		else
		{
			$this->app['modules']->manual();
		}

		// And finally register all modules
		$this->app['modules']->register();
	}

	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register()
	{
		// Register IoC bindings
		$this->app['modules'] = $this->app->share(function($app)
		{
			return new Finder($app, $app['files'], $app['config']);
		});
	}

	/**
	 * Provided service
	 * @return array
	 */
	public function provides()
	{
		return array('Modules');
	}

}
