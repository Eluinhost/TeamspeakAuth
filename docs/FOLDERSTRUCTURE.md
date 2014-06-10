Project Folder Structure
========================

The folder structure of the project with files of interest only is the following:

    |-authserver/                # The main folder of the authentication server
      |-AuthServer.js            # The main file to run the auth server, run it with `node AuthServer.js`
      |-servericon.png           # The server icon to display on the server list screen
    |-bower_components/          # Created when install bower assets, third party front end libraries
    |-cache/                     # Main folder for the entire cache, clear it with `grunt clean:cache`
      |-skins/                   # Folder for storing cached skins
      |-templates/               # Folder for storing cached templates
      |-container/               # Holds the cached container for the site built from /config/config.yml
      |-routing/                 # Holds the cached routes for the site built from /config/routes.yml
    |-config/                    # Stores conifguration options
      |-config.yml               # The config file, doesn't exist by default, needs to be created with `grunt configure`
      |-config.yml.dist          # The default config file, copy this to config.yml to manually edit
      |-routes.yml               # Stores the routes the website uses for URLs
    |-docs/                      # All these documentation files
    |-node_modules/              # Created when npm install is ran, third party node.js libraries
    |-src/                       # Stores the project's PHP code for the website to run from
    |-templates/                 # Stores the twig templates for the website. Any changes here need `grunt clean:cache` to take effect
    |-vendor/                    # Created when composer install is ran, third party PHP libraries for the website
    |-web/                       # The main folder to point the webserver at, stores assets/front PHP file
      |-css/
        |-style.css              # Simple styles used in the website
      |-images/
        |-fairy.png              # The example image on the homepage
        |-graphy.png             # The background image on the website
        |-main.png               # The background image on the centre part of the website
      |-vendor/                  # Stores third party front end libraries built from bower_components by grunt on install
      |-.htaccess                # Web server config file to rewrite requests through app.php
      |-app.php                  # The main file used for every request on the website