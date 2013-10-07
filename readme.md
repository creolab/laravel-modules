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

# Optimization

By default the package scans the **"modules"** directory for **"modules.json"** files. This is not the best solution way to discover modules, and I do plan to implement some kind of a caching to the Finder class.
To optimize the modules Finder even more you can publish the package configuration, and add the modules and definitions directly inside the configuration file by running:

    php artisan config:publish creolab/laravel-modules

And the editing the file **"app/config/packages/creolab/laravel-modules/config.php"**.
You just need to change the **"mode"** parameter from **"auto"** to **"manual"**, and list your modules under the **"modules"** key. An example of that is already provided inside the configuration.
