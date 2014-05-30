TS3 Authentication for Minecraft Accounts
=========================================

This provides a way to verify a Minecraft account belongs to a user within Teamspeak. On successful verification the
user will be provided a server group of choice, will have their description set to their Minecraft username and will
have an icon assigned to them that is the same as their ingame skin.

How it works
------------

There are 2 parts to the verification, a website and a fake minecraft server. First the user must visit the website and
type in their current Teamspeak username and click the button. It will then send them a code via private message on
Teamspeak that they have to input on the web page. The user must also connect to the fake Minecraft server with a valid
account where they will be kicked immediately with a message containing a code. The user must also input their Minecraft
username and the provided code into the website. If all the data matches the website will then contact the Teamspeak
server and set up the user.

What it does
------------

When verified the site will do the following for the client:

- Sets the client's description to their Minecraft username
- Adds the user to a chosen server group
- Uploads the head from the skin of the user to the server's icons and assigns the client the icon

Installation
------------

This project has the following direct requirements:

- PHP
- MySQL
- Web Server with PHP support
- NodeJS with NPM
- Composer
- Grunt CLI module installed globally (`npm install grunt-cli -g`)

The module 'ursa' used by the auth server requires the following also be installed:

- OpenSSL
- Python 2.7

Ursa has the following notes for Windows users:

    On Windows, you'll need to install some dependencies first:
     - [node-gyp](https://github.com/TooTallNate/node-gyp/) (`npm install -g node-gyp`)
       - [Python 2.7](http://www.python.org/download/releases/2.7.3#download) (not 3.3)
       - Vistual Studio 2010 or higher (including Express editions)
         - Windows XP/Vista/7:
            - Microsoft Visual Studio C++ 2010 ([Express](http://go.microsoft.com/?linkid=9709949) version works well)
            - For 64-bit builds of node and native modules you will _**also**_ need the [Windows 7 64-bit SDK](http://www.microsoft.com/en-us/download/details.aspx?id=8279)
            - If you get errors that the 64-bit compilers are not installed you may also need the [compiler update for the Windows SDK 7.1](http://www.microsoft.com/en-us/download/details.aspx?id=4422)
         - Windows 8:
            - Microsoft Visual Studio C++ 2012 for Windows Desktop ([Express](http://go.microsoft.com/?linkid=9816758) version works well)
     - [OpenSSL](http://slproweb.com/products/Win32OpenSSL.html) (normal, not light)
    in the same bitness as your Node.js installation.
      - The build script looks for OpenSSL in the default install directory  
      (`C:\OpenSSL-Win32` or `C:\OpenSSL-Win64`)
      - If you get `Error: The specified module could not be found.`, copy `libeay32.dll` from the OpenSSL bin directory to this module's bin directory, or to Windows\System3.

Personally installing [Visual Studio Express 2013 for Windows Desktop](http://www.visualstudio.com/downloads/download-visual-studio-vs) and replacing `npm install` with `npm install --msvs_version=2013` ran from the Visual Studio 'Developer Command Prompt' was enough (OpenSSL and Python still required)


### Set up dependencies

Download all dependencies by running the following in the root of the project:

`npm install`

`grunt install`

You can then create a config.yml with the command:

`grunt configure`

### Set up configuration

You can create/edit your config.yml file by running the command:

`grunt configure`

You can also open the config.yml file and edit it directly:

    parameters:
      minutesToLast: 15             # Number of minutes codes are valid for
      iconURL: "https://minotar.net/helm/;username;/16.png" # URL to get the faces from, replaces ;username; with the minecraft username
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
        motd: "§eAuth Server"       # The MOTD to display on the server list

    #  Ignore everything below here unless you know what you are doing #

### Set up database

First create your database and fill out the details in the config.yml manually or by using `grunt configure`.

If you use `grunt configure` answer yes to run the migrations and the database structure will be created. If you filled it in manually you can run `grunt run-migrations`

To update your database after updating the application running `grunt run-migrations` will update to the latest structure (assuming correct config.yml)

### Set up web server

Apache:

You want to point the webroot to the /web folder of this project. The .htaccess will handle routing everything through app.php correctly

nginx:

You want to set up something similar to this, routing through the app.php file. Make sure the root is the /web folder. (This is from my own configuration using FastCGI, your configuration may require something different)

    server {
        server_name auth.publicuhc.com www.auth.publicuhc.com;
        listen 37.59.47.201;
        root /home/publicuhc/domains/auth.publicuhc.com/public_html/web;
        index index.html index.htm index.php;
        access_log /var/log/virtualmin/auth.publicuhc.com_access_log;
        error_log /var/log/virtualmin/auth.publicuhc.com_error_log;
        fastcgi_param GATEWAY_INTERFACE CGI/1.1;
        fastcgi_param SERVER_SOFTWARE nginx;
        fastcgi_param QUERY_STRING $query_string;
        fastcgi_param REQUEST_METHOD $request_method;
        fastcgi_param CONTENT_TYPE $content_type;
        fastcgi_param CONTENT_LENGTH $content_length;
        fastcgi_param SCRIPT_FILENAME /home/publicuhc/domains/auth.publicuhc.com/public_html/web$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_param REQUEST_URI $request_uri;
        fastcgi_param DOCUMENT_URI $document_uri;
        fastcgi_param DOCUMENT_ROOT /home/publicuhc/domains/auth.publicuhc.com/public_html/web;
        fastcgi_param SERVER_PROTOCOL $server_protocol;
        fastcgi_param REMOTE_ADDR $remote_addr;
        fastcgi_param REMOTE_PORT $remote_port;
        fastcgi_param SERVER_ADDR $server_addr;
        fastcgi_param SERVER_PORT $server_port;
        fastcgi_param SERVER_NAME $server_name;
        fastcgi_param HTTPS $https;

        # strip app.php/ prefix if it is present
        rewrite ^/app\.php/?(.*)$ /$1 permanent;

        location / {
            index app.php;
            try_files $uri @rewriteapp;
        }

        location @rewriteapp {
            rewrite ^(.*)$ /app.php/$1 last;
        }

        # pass the PHP scripts to FastCGI server from upstream phpfcgi
        location ~ ^/(app)\.php(/|$) {
            fastcgi_pass unix:/var/php-nginx/139906447829275.sock/socket;
            fastcgi_split_path_info ^(.+\.php)(/.*)$;
            include fastcgi_params;
            fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_param  HTTPS off;
        }
    }
    
How to run the fake Minecraft server
------------------------------------

To run the authentication server all you need to do is run authserver/AuthServer.js with node:

`node AuthServer.js`

Editing templates
-----------------

You can edit the templates in the folder 'templates'. It uses the [Twig Framework](http://twig.sensiolabs.org/).

After making any changes to templates make sure to delete the cache folder or run `grunt clear-cache`