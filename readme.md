# Modules in Laravel 4

Application specific modules in Laravel 4 can be enabled by adding the following to your composer.json file:

    "creolab/laravel-modules": "dev-master"

And by adding a new provider to your providers list in "app/config/app.php":

    'Creolab\LaravelModules\ServiceProvider',

Also you need to add your modules directory to the composer autoloader:

    "autoload": {
        "classmap": [
            "app/modules"
        ]
    }

This also means you need to run "composer dump" everytime you add a new class to your module.

By default you can add a "modules" directory in your "app" directory. So as an example this is a structure for one of my projects:

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

Note that every module has a "module.json" file, in which you can enable/disable the module. I plan on adding more meta data to these module definitions, but I need feedback as to what to put in there.
The first thing will probably be some kind of a bootstrap class.

For now take a look at the example implementation, and please provide feedback ;)

[https://github.com/bstrahija/laravel-modules-example](https://github.com/bstrahija/laravel-modules-example)
