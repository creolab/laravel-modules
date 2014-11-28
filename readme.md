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

This also means you need to run **"composer dump-autoload"** every time you add a new class to your module.

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

If you want to have your modules in more that 1 directories you need to change the packages config file as following:

    'path' => array(
        'app/modules',
        'public/site',
        'another/folder/containing/modules',
    ),

And don't forget to add those directories to your autoload list inside the composer.json file.

One of the available option is the order in which the modules are loaded. This can be done simply by adding the following to your module.json file:

    "order": 5

The order defaults to 0, so keep that in mind if you don't define it.

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

By default the package scans the **"modules"** directory for **"module.json"** files. This is not the best solution way to discover modules, and I do plan to implement some kind of a caching to the Finder class.
To optimize the modules Finder even more you can publish the package configuration, and add the modules and definitions directly inside the configuration file by running:

    php artisan config:publish creolab/laravel-modules

And the editing the file **"app/config/packages/creolab/laravel-modules/config.php"**.
You just need to change the **"mode"** parameter from **"auto"** to **"manual"**, and list your modules under the **"modules"** key. An example of that is already provided inside the configuration.

**Note for Manual mode with multiple paths** : Laravel-Modules could not determine witch path to use. So please specify the folder containing the module you want to load. Like this example :

    'modules' => [
	    'app/modules' => [
	        'system' => ['enabled' => true],
	    ],
	    'another/modules/path' => [
	        'pages' => ['enabled' => false],
	        'seo'   => ['enabled' => true],
	    ],
	],
	
You can also add multiple module paths as an array, but do note that if a module has the same name, there will be problems.

## Including files

You can also specify which files in the module will be automatically included. Simply add a list of files inside your **module.json** config:

    {
        "include": [
            "breadcrumbs.php"
        ]
    }

There are some defaults set on which files will be included if they exist. Take a look at the latest config file, and republish the configuration if needed. By default these files will be included:

    'include' => array(
        'helpers.php',
        'filters.php',
        'composers.php',
        'routes.php',
        'bindings.php',
        'observers.php',
    )

So you have the choice to either add your custom files to the global configuration, which will look for these files in every module, or you can set it up on a per module basis by adding it to the **module.json** file.

## Service providers

A new addition is registering service providers for each module. Just add a line to your **module.json** file that looks something like this:

    "provider": "App\\Modules\\Content\\ServiceProvider"

These service provider classes work exactly like any other service provider added to your **app/config/app.php** configuration, so setup these classes by extending the **\Illuminate\Support\ServiceProvider** class and adding the appropriate methods.

You can also register multiple providers for every module by simply providing an array:

    "provider": [
        "App\\Modules\\Content\\ServiceProvider",
        "App\\Modules\\Content\\AnotherServiceProvider"
    ]

Keep in mind that you may have to run **composer dump-autoload** so you want get error on missing classes.

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

# Migrations

Every module can have it's own migrations, and they need to be in the module/migrations directory. So for example if you want to create a migration that creates a user table for the auth module:

    php artisan migrate:make create_users_table --path=app/modules/auth/migrations --table=users --create

And to run all module migrations do this:

    php artisan modules:migrate

Or to run migrations for a specific module:

    php artisan modules:migrate auth

You can also seed the database form the module if your **module.json** contains a seeder setting. Just pass the **--seed** option to the command:

    php artisan modules:migrate --seed

# Seeding

The modules can also have seeders. Just create the class like you would create a normal seeder, place it somewhere inside your module, and be sure to run **composer dump-autoload**. Then add the following to your **module.json** file:

    "seeder": "App\\Modules\\Content\\Seeds\\DatabaseSeeder"

This setting should contain the namespace path to your seeder class. Now simply run:

    php artisan modules:seed

To seed all your modules. Or you can do it for a specific module:

    php artisan modules:seed content

# Commands

You can add module specific commands. This is a sample artisan command file creation :

    php artisan command:make <MyModuleCommandName> --path=app/modules/<MyModule>/commands --namespace=App\Modules\<MyModule>\Commands --command=modules.mymodule:mycommand

Then in the **module.json** add (you can also add an array if you have multiple commands) :

    "command": "App\\Modules\\<MyModule>\\Commands\\MyModuleCommandName"

After a dump-autoload you can now execute **modules.mymodule:mycommand** from command line :

    php artisan modules.mymodule:mycommand

# Aliases

If you declare Facades into your modules you will like to create Aliases for your module, you can simply reference your alias in the `module.json` :

    "alias": {
    	"<MyAlias>" "App\\Modules\\<MyModule>\\Facades\\<MyFacade>"
    }

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/creolab/laravel-modules/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
