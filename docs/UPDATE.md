How to Update
=============

### Git based version

To update your project run `git pull` to get the latest version of the branch you are on (for bugfixes if you are on a release branch).
 
To switch to another release branch run `git fetch` followed by `git checkout releases/1.1.0` replacing 'releases/1.1.0` with the release branch you want to use

`grunt clean` - Clean all the third party libraries/dependencies

Follow the install instructions again

### Prebuilt versions

You may have to download the new version zip/tar.gz to another folder and install again as just overwriting may not be safe to do.

### Both

You will need to at least run the following 2 commands: 

`php console/console.php clean` - Removes the entire application cache

`php console/console.php parameters:update` - Build a new config.yml from the config.yml.dist

`php console/console.php schema:update` - update your database to the latest version