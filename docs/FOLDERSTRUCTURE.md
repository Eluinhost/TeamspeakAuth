Project Folder Structure
========================

The folder structure of the project with files of interest only is the following:

    |-bower_components/          # Created when install bower assets, third party front end libraries
    |-cache/                     # Main folder for the entire cache, clear it with `grunt clean:cache`
      |-skins/                   # Folder for storing cached skins
      |-templates/               # Folder for storing cached templates
      |-routing/                 # Holds the cached routes for the site built from /config/routes.yml
      |-prod/                    # Holds the production enviroment container cache
      |-dev/                     # Holds the dev environment container cache
    |-config/                    # Stores conifguration options
      |-config.yml               # The base config file for all environments
      |-config_dev.yml           # Dev environment overrides
      |-config_prod.yml          # Production environment overrides
      |-parameters.yml           # Doesn't exist by default, running console command parameters:update will create one (required)
      |-routes.yml               # Stores the routes the website uses for URLs
    |-console/
      |-console.php              # Used to run console commands
    |-docs/                      # All these documentation files
    |-node_modules/              # Created when npm install is ran, third party node.js libraries
    |-src/                       # Stores the project's PHP code for the website to run from
      |-parameters.yml.dist      # The default parameters file, this file is the template the console uses to create your parameters.yml
    |-templates/                 # Stores the twig templates for the website. Any changes here need `grunt clean:templates` to take effect
    |-vendor/                    # Created when composer install is ran, third party PHP libraries for the website
    |-web/                       # The main folder to point the webserver at, stores assets/front PHP file
      |-css/
        |-style.css              # Simple styles used in the website
      |-images/
        |-fairy.png              # The example image on the homepage
        |-graphy.png             # The background image on the website
        |-main.png               # The background image on the centre part of the website
        |-servericon.png         # The server icon to display on the server list screen
      |-vendor/                  # Stores third party front end libraries built from bower_components by grunt on install
      |-.htaccess                # Web server config file to rewrite requests through app.php
      |-app.php                  # The main file used for every request on the website