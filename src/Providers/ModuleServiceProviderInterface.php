<?php namespace Creolab\LaravelModules\Providers;

interface ModuleServiceProviderInterface {

    /**
     * Module service providers should have the ability to implement a bootstrap
     * once their provider is registered to the application.
     */
    public function bootstrap();

}
