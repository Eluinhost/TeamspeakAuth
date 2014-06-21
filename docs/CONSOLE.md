Console Commands
----------------

To run a command run the console.php file with the command you want to run as the arguments. e.g.

    php console.php start:server

### Nuke Group

`group:nuke <groupID>` 

Removes the description, icon and server group from all users in the given group.

Also removes any matching teamspeak UUID authentications.

If <groupID> is * then all users will have their icons and descriptions removed

### Start Server

`start:server`

Starts up the authentication server

### Update database schema

`schema:update`

Updates the database to the latest schema, run this after any update

### Update the config parmaeters

`config:update`

Updates/creates your config.yml asking you for any parameters that are missing and then cleans the container so they take effect.
You will need to restart the auth server if it is running to get the changes.

WARNING: If you have edited anything in the services node within your config.yml it will be overwritten, make a backup first

### Clean the container cache

`clean:container`

Removes the container cache. This is required after any manual change to the config.yml file. (requires a restart to the auth server if it is running)

### Clean the routing cache

`clean:router`

Removes the routing cache. This is required after any change to the routing.yml

### Clean the templating cache

`clean:templates`

Removes the template cache. This is required after any change to files in the templates folder

### Clean the skin cache

`clean:skins`

Removes the skin/transparents skin cache

### Clean all caches

`clean`

Removes all of the above caches