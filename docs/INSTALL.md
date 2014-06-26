How to Install
==============

Dependencies
------------

Your server must have the following:

* PHP + PHP GD + mcrypt extension
* MySQL
* Web Server with PHP support

### Development dependencies (if fetching from git instead of pre-built version):

* NodeJS with NPM with Grunt CLI module installed globally (`npm install grunt-cli -g`)
* Composer

### Optional Dependencies (both):

* cURL with the PHP extension - not required but recommended, will use it if available to fetch skins
* Git - useful to update/download the project

Step 1: Download
----------------

### Using Git

Clone the project using the following command `git clone https://github.com/Eluinhost/TeamspeakAuth.git`

This will make a folder called 'TeamspeakAuth' in the current directory with the contents of the master branch (latest development)

To switch to a release branch first change into the TeamspeakAuth directory and run the following:

`git fetch` - fetch all the remote branches

`git checkout releases/1.1.0` - checks out the particular release, replace 'releases/1.1.0' with the particular version you want

### Using a prebuilt zip/tar.gz

Download the zip file and unzip it where you want it to be installed

Step 2: Install
---------------

### Using Git version

Change directory into the TeamspeakAuth folder and run the following:

`npm install`

This will install the Node.js dependencies required to build the project front-end

You will then need to run:

`grunt install`

This will install and build the front end css/js/fonts into the web folder

Finally `composer install` will install the PHP dependencies

### Using prebuilt version

The project already has all the dependencies built.

Step 3: config.yml
-----------------

You can edit your configuration by editing `app/config/parameters.yml` or copying `app/config/parameters.yml.dist` to `app/config/parameters.yml` if it doesn't exist.

You may also need to clear the cache folder for settings to take effect (only if you edit the file manually):

`php bin/console cache:clear --env=prod`

Step 4. Database
----------------

You can then set up your database by running:

`php bin/console schema:update`

Assuming you passed correct parameters during the configure prompts this will then create the database structure in the database supplied

Step 5: Startup the Auth Server
-------------------------------

You can then start the fake minecraft server by changing by running:

`php bin/console authserver:start`

Step 6: Setup your webserver
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