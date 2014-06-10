API
===

The api can be found at the URL /api/

Verification/Online checks
--------------------------

To check minecraft accounts by their UUID check the following:

/api/verified  - Check if the UUID provided are verified
/api/online    - Same as verified but also checks if the teamspeak account is online 

Expects a POST parameter `uuids` that is an array of UUID strings to check for.

### Return Format:

    {
        "uuid1": ... data for uuid1 ...,    
        "uuid2": ... data for uuid2 ...,    
        "uuid3": ... data for uuid3 ...,    
        ... more uuids and their data ...
    }
    
### Data Format

#### Unverified

    {
        "00000000-0000-0000-000000000000": false
    }
    
#### Verified

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