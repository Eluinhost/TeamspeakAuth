Grunt Commands
==============

There are a few command you can use with grunt to make using the project easier.

`grunt` - shows all the available tasks to run (hides unimportant tasks from view)

### Clean

`grunt clean` - Cleans the entire project to build it again, runs all the below clean:* tasks

`grunt clean:bower` - Cleans the bower_components folder

`grunt clean:build` - Cleans the web/vendor folder - the compressed front end dependencies built from bower_components

`grunt clean:composer` - Cleans the vendor directory of all PHP dependencies

`grunt clean:cache` - Cleans the cache folder of all of the cached skins/templates

### Configure

`grunt configure` - Runs the configuration script to create/edit your config.yml with prompts

### Install

`grunt install`

Runs the following tasks in order 

`grunt composer:install`, `grunt bower-install`, `grunt dist`

Essentially it downloads PHP and front-end dependencies and then builds the front end into /web/vendor

### Run Migrations

If you have updated to a newer version and want to update your database, you can either start the authserver or run:

`grunt run-migrations`

It will update your database using your config.yml settings to the latest version.

### Advanded

For other commands such as `dist` (build bower_components). Run `grunt --help`. It will show the full list of available tasks and their descriptions