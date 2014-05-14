REFACTORING/UPDATING - THIS REPOSITORY WILL NOT WORK IN IT'S CURRENT STATE

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

Installation
------------

This project has the following requirements:

- PHP
- MySQL
- Web Server with PHP support
- NodeJS

If not using a pre-built package with dependencies included you will also need the following

- Bower (for installing bootstrap)
- Composer (for installing all PHP dependencies and autoloading)

### Set up dependencies

Skip this step if using  pre-built version.

Download all dependencies by running the following in the root of the project:

`composer install`

`bower install`

### Set up configuration

Copy the file /config/config.yml.dist to /config/config.yml and edit the parameters to fit your server

    parameters:
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
        minecraft_table: minecraft_codes    # The table name for the minecraft codes
        teamspeak_table: teamspeak_codes    # The table name for the teamspeak codes
        username: ""                # The username to login to the database
        password: ""                # The password to use
      minecraft:
        host: localhost             # The address to listen on for the fake server
        port: 35879                 # The port to listen on for the fake server
        motd: "§eAuth Server"       # The MOTD to display on the server list
    
    #  Ignore everything below here unless you know what you are doing #
    services:
      ts3interface:
        class: PublicUHC\TeamspeakAuth\TeamspeakHelper
        arguments: ["%teamspeak.host%", "%teamspeak.query_port%", "%teamspeak.port%", "%teamspeak.username%", "%teamspeak.password%"]
        
### Set up database

You can use the following snippet to create the default structure.

    DROP TABLE IF EXISTS minecraft_codes;
    DROP TABLE IF EXISTS teamspeak_codes;
    CREATE TABLE minecraft_codes
    (
      ID int NOT NULL AUTO_INCREMENT PRIMARY KEY,
      username varchar(16) UNIQUE NOT NULL,
      code varchar(10) NOT NULL,
      created_time datetime
    );
    CREATE TABLE teamspeak_codes
    (
      ID int NOT NULL AUTO_INCREMENT PRIMARY KEY,
      uuid varchar(128) UNIQUE NOT NULL,
      code varchar(10) NOT NULL,
      created_time datetime
    );
    
How to run the fake Minecraft server
------------------------------------

To run the authentication server all you need to do is run authserver/AuthServer.js with node:

`node AuthServer.js`