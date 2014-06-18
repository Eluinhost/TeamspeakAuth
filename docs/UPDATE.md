How to Update
=============

### Git based version

To update your project run `git pull` to get the latest version of the branch you are on (for bugfixes if you are on a release branch).
 
To switch to another release branch run `git fetch` followed by `git checkout releases/1.1.0` replacing 'releases/1.1.0` with the release branch you want to use

### Prebuilt versions

You will have to download the new version zip/tar.gz to another folder as overwriting may not be safe to do.

### Both

Run the following: 

TODO replace grunt commands with php scripts to remove nodejs dependency

`grunt clean` - Clean all the third party libraries/dependencies as well as the entire cache

TODO grunt install runs the config:update script on composer install
`grunt install` - Install the (possibly) newer libraries/dependencies and build the web/vendor folder

`php console/console.php config:update` - Build a new config.yml from the config.yml.dist

`php console/console.php schema:update` - update your database to the latest version