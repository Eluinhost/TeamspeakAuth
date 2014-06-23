API v1
======

All requests are prefixed with /api/v1/.

Succesful requests will return status code 200 with any relevant data.

Invalid requests will return a non-200 status codes and data in the format:

{"ERROR": "Error Message"}

### Request a code for a teamspeak account

`GET /teamspeakCode/{username}`

#### Parameters

`{username}` - the username to send a code to

Sends a code to the given teamspeak username.

#### Returns:

    {"UUID": "the uuid of the teamspeak account"}