How to Update
=============

To update your project run `git pull`. 
If you are not using git, you will have to download the new version to another folder as overwriting may not be safe

First you should delete your config.yml to make sure you get any new changes to the file.

Then run the following: 

`grunt clean` - Clean all the third party libraries/dependencies

`grunt install` - Install the (possibly) newer libraries/dependencies and build the web/vendor folder

`grunt configure` - Build a new config.yml from the config.yml.dist, also gives a prompt to update your database to the latest version

The commands can be shortened to `grunt clean install configure`