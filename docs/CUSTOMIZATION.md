Customization
=============

Server List Icon
----------------

To change the server icon display in the server list inside Minecraft replace the file at /src/PublicUHC/Bundle/TeamspeakAuthBundle/Resources/public/images/servericon.png.

Default: ![Server Icon](/src/PublicUHC/Bundle/TeamspeakAuthBundle/Resources/public/images/servericon.png)
Make sure it fits Minecraft's restrictions on server list images.

Web pages
---------

The templates for all the web pages can be found in the `src/PublicUHC/Bundle/TeamspeakAuthBundle/Resources/views` folder. They use Haml for the templates and work with the Twig framework.
Templates ending in .twig are parsed as twig and .haml are parsed as HAML into a twig template.
All templates are cached so after any changes make sure to clear the cache.

The images/css used can be found at web/images and web/css
