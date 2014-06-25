Configuration
=============

All configuration is done via the file `config/parameters.yml`.

When running in debug (via app_dev.php) any changes to config files will apply on next page load.

In production (via the usual app.php) changes will require you to clear the cache first.

You can clear the production cache by running the command:

`php console\console.php clean:container --env=prod`

You can fill in any missing options in your parameters.yml file by running the command:

    php console/console.php parameters:update
    
It will update/create your parameters file and ask you for any missing

You can also open the parameters.yml file and edit it directly:

    parameters:
      minutesToLast: 15
      skinCacheTime: 7200
      serverAddress: "auth.example.com or example.com:35879"
      teamspeak.host: ts.example.com
      teamspeak.port: 9987
      teamspeak.username: serveradmin
      teamspeak.password: ""
      teamspeak.query_port: 10011
      teamspeak.group_id: 222
      database.host: localhost
      database.port: 3306
      database.database: authentication
      database.username: ""
      database.password: ""
      database.keepAlive: 300
      minecraft.host: 0.0.0.0
      minecraft.port: 35879
      minecraft.description: "§4▁§e▂§4▃§e▄§4▅§e▆§4▇§e█ §4§l    Auth Server    §e█§4▇§e▆§4▅§e▄§4▃§e▂§4▁ §c▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔"
    
If you edit the file manually either run the command above or delete the folder `/cache/<env>` to clean the cache and get the new settings (you will need to restart the auth server if it is running)

Using parameters:update automatically cleans the container (for the enviroment selected)