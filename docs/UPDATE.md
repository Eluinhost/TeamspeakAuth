How to Update
=============

To update your project run `git pull` to get the latest version of the branch you are on (for bugfixes if you are on a release branch).
 
To switch to another release branch run `git fetch` followed by `git checkout releases/1.1.0` replacing 'releases/1.1.0` with the release branch you want to use

If you are not using git, you will have to download the new version zip/tar.gz to another folder as overwriting may not be safe

First you should delete your config.yml to make sure you get any new changes to the file.

Then run the following: 

`grunt clean` - Clean all the third party libraries/dependencies as well as the entire cache

`grunt install` - Install the (possibly) newer libraries/dependencies and build the web/vendor folder

`grunt configure` - Build a new config.yml from the config.yml.dist

`php console/console.php schema:update` - update your database to the latest version

The commands can be shortened to `grunt clean install configure; php console/console.php schema:update`