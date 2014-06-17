Configuration
=============

You can create/edit your config.yml file by running the command:

`grunt configure`

You can also open the config.yml file and edit it directly:

    parameters:
      minutesToLast: 15             # Number of minutes codes are valid for
      skinCacheTime: 7200           # How long skins are cached from minotar for
      serverAddress: "auth.publicuhc.com or publicuhc.com:35879" # The address to show on the webpage for the auth server
      teamspeak:
        host: localhost             # The address of the teamspeak server
        port: 9988                  # The port of the server to use
        username: serveradmin       # The username to login with, must have valid permissions to edit descriptions/users e.t.c.
        password: ""                # The password to login with
        query_port: 10011           # The port the teamspeak serverquery is on
        group_id: 222               # The group ID to change people to when authenticated
      database:
        host: localhost             # The address of the database server
        port: 3306                  # The port of the database
        database: authentication            # The database to use
        username: ""                # The username to login to the database
        password: ""                # The password to use
      minecraft:
        host: localhost             # The address to listen on for the fake server
        port: 35879                 # The port to listen on for the fake server
        description: "§eAuth Server"    # The MOTD to display on the server list

    #  Ignore everything below here unless you know what you are doing, things WILL break otherwise #
    
If you edit the file manually either run `grunt clean:container` or delete the folder /cache/container to update the settings