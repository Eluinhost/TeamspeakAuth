Customization
=============

Server List Icon
----------------

To change the server icon display in the server list inside Minecraft replace the file at web/images/servericon.png. 
Make sure it fits Minecraft's restrictions on server list images.

Web pages
---------

The templates for all the web pages can be found in the templates folder. They use Haml for the templates and work with the Twig framework.
Templates ending in .twig are parsed as twig and .haml are parsed into a twig template.
All templates are cached so after any changes make sure to run `php console/console.php clean:templates` to clear the cache or delete the folder /cache/templates.

The file layout.html.twig is the main layout file in which all the pages are placed.

The images/css used can be found at web/images and web/css
