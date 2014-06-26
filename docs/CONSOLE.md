Console Commands
----------------

To run a command run the console.php file with the command you want to run as the arguments. e.g.

    php bin\console authstart:server
    
To specify an environment to use (for example with clean:container) add `--env=<environment>` where <environment> is the environment to use (prod or dev)

### Nuke Group

`group:nuke <groupID>` 

Removes the description, icon and server group from all users in the given group.

Also removes any matching teamspeak UUID authentications.

If <groupID> is * then all users will have their icons and descriptions removed

### Start Server

`authstart:server`

Starts up the authentication server

### Update database schema

`doctrine:schema:update`

Updates the database to the latest schema, run this after any update

### Clean the cache

`cache:clean --env=<env>`

Removes the current environments container cache. This is required after any manual change to the parameters.yml file for the prod environment, dev is automatic. (requires a restart to the auth server if it is running)

TODO check/create command for skin caches