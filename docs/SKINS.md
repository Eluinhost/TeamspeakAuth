Serving Skins
=============

Included as a part of the project is a skin cache that caches skins from Minotar to serve them locally.

The cache can be found at cache/skins. To delete the cache run 'php console/console.php clean:skins' or delete cache/skins

The URLs for skins are the following:

### Head with helmet

/skins/helm/{username}/{size}

Size is optional, defaults to 16px

### Full skin

/skins/skin/{username}

### Head without helmet

/skins/head/{username}/{size}

Size is optional, defaults to 16px