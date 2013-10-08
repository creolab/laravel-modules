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

		// Register commands
		$this->bootCommands();

		// Auto scan if specified
		$this->app['modules']->start();

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
	 * Register all available commands
	 * @return void
	 */
	public function bootCommands()
	{
		// Add modules command to IoC
		$this->app['modules.list'] = $this->app->share(function($app)
		{
			return new ModulesCommand();
		});

		// Add scan command to IoC
		$this->app['modules.scan'] = $this->app->share(function($app)
		{
			return new ModulesScanCommand();
		});

		// Now register all the commands
		$this->commands('modules.list', 'modules.scan');
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
