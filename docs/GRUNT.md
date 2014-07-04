Grunt Commands
==============

There are a few command you can use with grunt to make using the project easier.

`grunt` - shows all the available tasks to run (hides unimportant tasks from view)

### Clean

`grunt clean` - Cleans the entire project to build it again, runs all the below clean:* tasks

`grunt clean:bower` - Cleans the bower_components folder

`grunt clean:build` - Cleans the web/vendor folder - the compressed front end dependencies built from bower_components

### Install

`grunt install`

Runs the following tasks in order 

`grunt bower-install`, `grunt dist`

Essentially it downloads front-end dependencies and then builds the front end into /web/vendor

### Advanded

For other commands such as `dist` (build bower_components). Run `grunt --help`. It will show the full list of available tasks and their descriptions
