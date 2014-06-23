API v1
======

All requests are prefixed with /api/v1/.

Succesful requests will return status code 200 with any relevant data.

Invalid requests will return a non-200 status codes and data in the format:

{"ERROR": "Error Message"}

### Request a code for a teamspeak account

Sends a new teamspeak code to the given Teamspeak username and returns the user's UUID

`GET /teamspeakCode/{username}`

#### Parameters

`{username}` - the username to send a code to

Sends a code to the given teamspeak username.

#### Returns:

    {"UUID": "the uuid of the teamspeak account"}
    
### Authentications List

Get a list of authentications, latest first

`GET /authentications`

Examples:

`/authentications?limit=15&offset=30` - lists 15 starting at index 30

`/authentications` - uses defaults 10 and 0

#### Parameters

`{limit}` - Maximum amount of authentications to return, default 10, maximum 50
`{offset}` - Offset within the data, default 0

#### Returns

    [
        {
            "updatedAt":1403187148,  # Unix timestamp
            "createdAt":1403187148,  # Unix timestamp
            "teamspeakAccount": {
                "createdAt":1403187038,  # Unix timestamp
                "updatedAt":1403187040,  # Unix timestamp
                "uuid":"ewo4M0KT59ifNUKEV\/FHEqoFCI1=",  # Teamspeak UUID
                "lastName":"2313"  # Teamspeak Username when authenticated
            },
            "minecraftAccount": {
                "createdAt":1403169784,  # Unix timestamp
                "updatedAt":1403169908,  # Unix timestamp
                "uuid":"22222222222222222222222222222222",  # Minecraft UUID
                "lastName":"ghowden",  # Minecraft username when authenticated
                "skin":"\/skins\/helm\/ghowden"  # local URL for helmet
            }
        },
        {
            ... another authentication ...
        }
    ]
    
### Authenticate

Create a new authentication for the given accounts, does the whole 'teamspeak verification' thing

`POST /authentications`

#### Parameters

`mc_uuid` - Minecraft UUID
`mc_code` - The code for the Minecraft Account
`ts_uuid` - The Teamspeak UUID
`ts_code` - The code for the Teamspeak Account

#### Returns

    {}