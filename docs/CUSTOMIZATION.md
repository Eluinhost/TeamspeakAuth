Customization
=============

Server List Icon
----------------

To change the server icon display in the server list inside Minecraft replace the file at authserver/servericon.png. 
Make sure it fits Minecraft's restrictions on server list images.

Web pages
---------

The templates for all the web pages can be found in the templates folder. They use the Twig framework.
All templates are cached so after any changes make sure to run `grunt clean:template_cache` to clear the cache or delete the folder /cache/templates.

The file layout.html.twig is the main layout file in which all the pages are placed.

The images/css used can be found at web/images and web/css
