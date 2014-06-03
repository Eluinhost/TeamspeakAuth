How to Install
==============

Dependencies
------------

Your server must have the following:

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

Optional Dependencies:

cURL with the PHP extension - not required but recommended, will use it if available

Git - useful to update/download the project

Step 1: Download
----------------

### Using Git

Clone the project using the following command `git clone https://github.com/Eluinhost/TeamspeakAuth.git`

This will make a folder called 'TeamspeakAuth' in the current directory with the contents of the master branch

To switch to a release branch first change into the TeamspeakAuth directory and run the following:

`git fetch` - fetch all the remote branches

`git checkout releases/1.1.0` - checks out the particular release, replace 'releases/1.1.0' with the particular version you want

### Using Zip/tar.gz

Download the zip file and unzip it where you want it to be installed

Step 2: Install
---------------

Change directory into the TeamspeakAuth folder and run the following:

`npm install`

This will install the Node.js dependencies required for the entire project

You will then need to run:

`grunt install`

This will install all the other dependencies used by the project and build the front end css/js/fonts into the web folder

You can then create your config.yml by running:

`grunt configure`

The last option of this command is to run migrations on your database. 
Assuming you passed correct parameters during the configure prompts this will then create the database structure in the database supplied

Step 3: Startup the Auth Server
-------------------------------

You can then start the fake minecraft server by changing into the authserver directory and running:

`node AuthServer.js`

Step 4: Setup your webserver
----------------------------

You will need to point your webserver root to the web folder of the project. 

### Apache

Set the DocumentRoot to the web folder, the .htaccess will handle the request rooting

    DocumentRoot /path/to/the/project/folder/TeamspeakAuth/web
    
### nginx

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