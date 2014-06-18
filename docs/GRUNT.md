Grunt Commands
==============

There are a few command you can use with grunt to make using the project easier.

`grunt` - shows all the available tasks to run (hides unimportant tasks from view)

### Clean

`grunt clean` - Cleans the entire project to build it again, runs all the below clean:* tasks

`grunt clean:bower` - Cleans the bower_components folder

`grunt clean:build` - Cleans the web/vendor folder - the compressed front end dependencies built from bower_components

`grunt clean:composer` - Cleans the vendor directory of all PHP dependencies

`grunt clean:cache` - Cleans the cache folder of all of the cached skins/templates/container/routing

`grunt clean:template_cache` - Cleans the template cache, use this if you make changes to the templates

`grunt clean:skins_cache` - Cleans the cache of all skins

`grunt clean:container_cache` - Cleans the project container. Use this if you make any changes to the config.yml file

`grunt clean:routing_cache` - Cleans the project routes. Use this if you make any changes to the routes.yml file

### Install

`grunt install`

Runs the following tasks in order 

`grunt composer:install`, `grunt bower-install`, `grunt dist`, `grunt clean:cache`

Essentially it downloads PHP and front-end dependencies and then builds the front end into /web/vendor

### Advanded

For other commands such as `dist` (build bower_components). Run `grunt --help`. It will show the full list of available tasks and their descriptions
