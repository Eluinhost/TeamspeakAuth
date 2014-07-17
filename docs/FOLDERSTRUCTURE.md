Project Folder Structure
========================

The folder structure of the project with files of interest only is the following:

    |-app/
      |-config/                    # Stores conifguration options
        |-config.yml               # The base config file for all environments
        |-config_dev.yml           # Dev environment overrides
        |-config_prod.yml          # Production environment overrides
        |-parameters.yml           # User configuration options
        |-parameters.yml.dist      # Default configuration options, copy this to parameters.yml
        |-routing.yml              # Stores the routes the website uses for URLs
    |-bower_components/          # Created when install bower assets, third party front end libraries
    |-cache/                     # Main folder for the entire cache, clear it with `grunt clean:cache`
      |-skins/                   # Folder for storing cached skins
      |-prod/                    # Production environment cache
      |-dev/                     # Dev environment cache    
    |-bin/
      |-console                  # Used to run console commands
    |-docs/                      # All these documentation files
    |-node_modules/              # Created when npm install is ran, third party node.js libraries
    |-src/                       # Stores the project's PHP code for the website to run from
    |-vendor/                    # Created when composer install is ran, third party PHP libraries for the website
    |-web/                       # The main folder to point the webserver at, stores assets/front PHP file
      |-vendor/                  # Stores third party front end libraries built from bower_components by grunt on install
      |-.htaccess                # Web server config file to rewrite requests through app.php
      |-app.php                  # The main file used for every request on the website