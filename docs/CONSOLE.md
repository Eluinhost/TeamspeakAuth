Console Commands
----------------

To run a command run the console.php file with the command you want to run as the arguments. e.g.

    php console.php start:server

### Nuke Group

`group:nuke <groupID>` 

Removes the description, icon and server group from all users in the given group.

Also removes any matching teamspeak UUID authentications.

### Start Server

`start:server`

Starts up the authentication server

### Update database schema

`schema:update`

Updates the database to the latest schema, run this after any update

### Update the config parmaeters

`config:update`

Updates/creates your config.yml asking you for any parameters that are missing. 
WARNING: If you have edited anything in the services node in your config.yml it will be overwritten, make a backup first

### Clean the container cache

`clean:container`

Removes the container cache. This is required after any manual change to the config.yml file. (requires a restart to the auth server if it is running)