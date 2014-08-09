<?php namespace Creolab\LaravelModules\Providers;

use Illuminate\Support\ServiceProvider; 
use ModuleServiceProviderInterface;

abstract class ModuleServiceProvider extends ServiceProvider {

    /**
     * Bootstrap module service provider once registered with the app.
     *
     * @return void
     */
    abstract public function bootstrap();

}
