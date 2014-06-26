Configuration
=============

All configuration is done via the file `app/config/parameters.yml`.

When running in debug (via app_dev.php) any changes to config files will apply on next page load.

In production (via the usual app.php) changes will require you to clear the cache first.

You can clear the production cache by running the command:

`php bin\console cache:clear --env=prod`

You can open the parameters.yml file and edit it directly: (if it doesn't exist, copy it from parameters.yml.dist

    parameters:
        database_driver:   pdo_mysql
        database_host:     127.0.0.1
        database_port:     ~
        database_name:     authentications
        database_user:     root
        database_password: ~
    
        locale:            en
        secret:            ThisTokenIsNotSoSecretChangeIt
    
        debug_toolbar:          true
        debug_redirects:        false
        use_assetic_controller: true
    
        database_driver: pdo_mysql
        database_host: localhost
        database_port: 3306
        database_name: test
        database_user: root
        database_password: ""
    
        minutesToLast: 15
        site_name: 'Teamspeak Auth'
        server_address: 'auth.example.com or example.com:35879'

If you edit the file manually either run the command above or delete the folder `/cache/<env>` to clean the cache and get the new settings (you will need to restart the auth server if it is running)