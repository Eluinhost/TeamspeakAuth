TS3 Authentication for Minecraft Accounts
=========================================

This provides a way to verify a Minecraft account belongs to a user within Teamspeak. On successful verification the
user will be provided a server group of choice, will have their description set to their Minecraft username and will
have an icon assigned to them that is the same as their ingame skin.

![Example Image](/web/images/fairy.png?raw=true "Example Image")

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

Changelog
---------

### 1.0.0

Initial release

### 1.1.0

- Added authentication history tracking and store accounts=>codes
- Replaced bootstrap for foundation in the webpages and restyled a bit
- Added a section to the website for viewing the last 10 authentications

### 2.0.0

- Replaced NodeJS auth server with one in PHP (no more NodeJS running dependency)
- Use the Minecraft UUID instead of username as an identifier for an account
- If someone auths with a minecraft username that is already in use by another minecraft UUID then the old account and it's authentications are removed and the teamspeak account changes are reset
- Includes a skin cache for caching/serving skins from minotar
- API for checking verification/online status of a minecraft accounts
- Caching of the website internal to speed up requests
- Added console commands for CLI
- New database structure shared by the auth server and website

Documentation
-------------

[How to Install](docs/INSTALL.md)

[How to Update](docs/UPDATE.md)

[Grunt Tasks](docs/GRUNT.md)

[Customize](docs/CUSTOMIZATION.md)

[Project folder structure](docs/FOLDERSTRUCTURE.md)

[Configuration](docs/CONFIGURATION.md)

[Serving skins locally](docs/SKINS.md)

[Console Commands](docs/CONSOLE.md)

[API for checking Minecraft account verification/online status](docs/API.md)

Git Branches
------------

`master` - holds the latest development version, branch into releases/* branches when ready for next release

`releases/1.0.0` `releases/1.1.0` e.t.c. - holds the particular release branch

`features/*` - branches for developing features, merges back into the master branch

`hotfixes/*` - hotfix branches that merge into the affected release branch/es and/or master

`bugfixes/*` - bugfix branches that merge into master for the next release