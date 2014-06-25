Console Commands
----------------

To run a command run the console.php file with the command you want to run as the arguments. e.g.

    php console.php start:server
    
To specify an environment to use (for example with clean:container) add `--env=<environment>` where <environment> is the environment to use (prod or dev)

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

### Update the parameters

`parameters:update`

Updates/creates your parameters.yml asking you for any parameters that are missing and then cleans the current environment container so they take effect.
You will need to restart the auth server if it is running to get the changes.

### Clean the container cache

`clean:container`

Removes the current environments container cache. This is required after any manual change to the config.yml file for the prod environment, dev is automatic. (requires a restart to the auth server if it is running)

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

Removes all caches