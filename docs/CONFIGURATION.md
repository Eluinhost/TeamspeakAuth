Configuration
=============

You can create/edit your config.yml file by running the command:

    php console/console.php config:update
    
It will update/create your config file and ask you for any missing parameters

You can also open the config.yml file and edit it directly:

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
      minecraft.host: 0.0.0.0
      minecraft.port: 35879
      minecraft.description: "§4▁§e▂§4▃§e▄§4▅§e▆§4▇§e█ §4§l    Auth Server    §e█§4▇§e▆§4▅§e▄§4▃§e▂§4▁ §c▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔"

    #  Ignore everything below here unless you know what you are doing, things WILL break otherwise #
    
If you edit the file manually either run:

`php console/console.php clean:container`
 
or delete the folder `/cache/container` to clean the cache and get the new settings (you will need to restart the auth server if it is running)
Using config:update automatically cleans the container