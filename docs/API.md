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
                "createdAt": 1401956864,
                "updatedAt": 1401956864,
                "minecraftAccount":{
                    "createdAt": 1401956864,
                    "updatedAt": 1401956864,
                    "uuid":"6ac803fd132f4540a741cb18ffeed8ce"
                },
                "teamspeakAccount":{
                    "createdAt": 1401956864,
                    "updatedAt": 1401956864,
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