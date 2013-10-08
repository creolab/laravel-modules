# Modules in Laravel 4

Application specific modules in Laravel 4 can be enabled by adding the following to your **"composer.json"** file:

    "creolab/laravel-modules": "dev-master"

And by adding a new provider to your providers list in **"app/config/app.php"**:

    'Creolab\LaravelModules\ServiceProvider',

Also you need to add your modules directory to the composer autoloader:

    "autoload": {
        "classmap": [
            "app/modules"
        ]
    }

This also means you need to run **"composer dump"** every time you add a new class to your module.

By default you can add a **"modules"** directory in your **"app"** directory. So as an example this is a structure for one of my projects:

    app/
    |-- modules
        |-- auth
            |-- controllers
            |-- models
            |-- views
            |-- module.json
        |-- content
            |-- controllers
            |-- models
            |-- views
            |-- module.json
        |-- shop
            |-- module.json
        |-- system
            |-- module.json

Note that every module has a **"module.json"** file, in which you can enable/disable the module. I plan on adding more meta data to these module definitions, but I need feedback as to what to put in there.
The first thing will probably be some kind of a bootstrap class.

For now take a look at the example implementation, and please provide feedback ;)

[https://github.com/bstrahija/laravel-modules-example](https://github.com/bstrahija/laravel-modules-example)

# Commands

There are 2 commands available through this package:

    php artisan modules

Which simply diplays all current modules depending on the mode set in the configuration. And:

    php artisan modules:scan

Which is only required if you have your modules setup in the **"manifest"** mode (see below).
This command scans the modules exactly like in the **"auto"** mode, but caches the results into a manifest file.

# Optimization

By default the package scans the **"modules"** directory for **"modules.json"** files. This is not the best solution way to discover modules, and I do plan to implement some kind of a caching to the Finder class.
To optimize the modules Finder even more you can publish the package configuration, and add the modules and definitions directly inside the configuration file by running:

    php artisan config:publish creolab/laravel-modules

And the editing the file **"app/config/packages/creolab/laravel-modules/config.php"**.
You just need to change the **"mode"** parameter from **"auto"** to **"manual"**, and list your modules under the **"modules"** key. An example of that is already provided inside the configuration.

You can also add multiple module paths as an array, but do note that if a module has the same name, there will be problems.

## Modules Manifest

Another possible mode is **"manifest"** which basically writes a JSON manifest file in your Laravel storage directory that contains all the modules definitions.
This is only done the first time and to update the manifest file you need to either delete it, or rescan the modules via the following command:

    php artisan modules:scan

# Assets

Just recently the ability to publish public assets for each module has been added. Just run:

    php artisan modules:publish

And all modules that contain an **"assets"** directory will be published to the Laravel public directory.
You can also publish assets for individual modules by providing the module name:

    php artisan modules:publish content
