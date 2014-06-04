API
===

The api can be found at the URL /api/

Verified
--------

To check if a minecraft account with the supplied UUID is verified check the following:

/api/verified/mc_uuid

Replace mc_uuid with their minecraft UUID (without the - if there are any)

Return Format - non verified:

    {"verified":false}

Return Format - verified:

    {
        "verified":true,
        "authentications":[
            {
                "createdAt":"Tue, 03 Jun 2014 13:34:18 +0000",
                "updatedAt":"Tue, 03 Jun 2014 13:34:18 +0000",
                "minecraftAccount":{
                    "createdAt":"Tue, 03 Jun 2014 13:33:45 +0000",
                    "updatedAt":"Tue, 03 Jun 2014 13:33:45 +0000",
                    "uuid":"6ac803fd132f4540a741cb18ffeed8ce"
                },
                "teamspeakAccount":{
                    "createdAt":"Thu, 29 May 2014 18:08:48 +0000",
                    "updatedAt":"Tue, 03 Jun 2014 13:32:10 +0000",
                    "uuid":"ewo4M0KT59ifNUKEV\/FHEqoFCI4="
                }
            }
        ],
    }
    
Online
------

This is exactly the same as the verified one except also shows which Teamspeak UUIDs are online right now. If the online status isn't needed then use verified.

/api/online/mc_uuid

Adds a node 'online' with an array of Teamspeak 3 UUIDs to the root of the JSON. 

If none are online then the array will be empty. 

If not verified then the response is just:

    {"verified":false}