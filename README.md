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

Documentation
-------------

[How to Install](docs/INSTALL.md)

[How to Update](docs/UPDATE.md)

[Grunt Tasks](docs/GRUNT.md)

[Customize](docs/CUSTOMIZATION.md)

[Project folder structure](docs/FOLDERSTRUCTURE.md)

[Configuration](docs/CONFIGURATION.md)