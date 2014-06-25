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
    
### Check accounts for authentications

Check accounts for any authentications made against them

`GET /api/v1/authentications/{account_type}/{verify_type}/{uuid}`

#### Parameters

`{account_type}` - `minecraft` OR `teamspeak`, the type of account to check
`{verify_type}` - `verify` OR `online`, `online` will fill out the 'online' fields of the teamspeak account, `verify` will always show false
`{uuid}` - The UUID of the account to check

#### Returns

##### Minecraft account UUID search

    {
        "00000000-0000-0000-000000000000": {
            "minecraftAccount": {
                "createdAt": 1401960387,    # UNIX timestamp
                "updatedAt": 1401960387,    # UNIX timestamp
                "uuid": "00000000-0000-0000-000000000000",  # Account UUID, same as the requested UUID,
                "lastName": "ghowden"       # The last name the account was verified with
            },
            "authentications": [            # List of authentications made against the minecraft account
                {
                    "createdAt": 1401960387,    # UNIX timestamp
                    "udpatedAt": 1401960387,    # UNIX timestamp
                    "teamspeakAccount": {       # Assosciated teamspeak account details
                        "createdAt": 1401960387,    # UNIX timestamp
                        "updatedAt": 1401960387,    # UNIX timestamp
                        "lastName": "Eluinhost",    # Last teamspeak name on verification
                        "uuid": "teamspeakUUID"    # The teamspeak account UUID
                        "online": true          # Will always show false if using /api/verified
                    }
                },
                {
                    ... authentication number 2 ...
                },
                ... more authentications ...
            ]
        }
    }

##### Teamspeak account UUID search

    {
        "teamspeakUUID": { # teamspeakUUID will be the requested UUID
            "teamspeakAccount": {
                "createdAt": 1401960387,    # UNIX timestamp
                "updatedAt": 1401960387,    # UNIX timestamp
                "lastName": "Eluinhost",    # Last teamspeak name on verification
                "uuid": "teamspeakUUID"    # The teamspeak account UUID requested
                "online": true          # Will always show false if using verified type, true of false if using online
            },
            "authentications": [            # List of authentications made against the teamspeak account
                {
                    "createdAt": 1401960387,    # UNIX timestamp
                    "udpatedAt": 1401960387,    # UNIX timestamp
                    "minecraftAccount": {       # Assosciated minecraft account details
                        "createdAt": 1401960387,    # UNIX timestamp
                        "updatedAt": 1401960387,    # UNIX timestamp
                        "uuid": "00000000-0000-0000-000000000000",  # Minecraft account UUID
                        "lastName": "ghowden"       # The last name the account was verified with
                    }
                },
                {
                    ... authentication number 2 ...
                },
                ... more authentications ...
            ]
        }
    }