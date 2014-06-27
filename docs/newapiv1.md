### `GET` /api/v1/authentications.{_format} ###

_Fetch a list of all the authentications, latest first_

#### Requirements ####

**_format**

  - Requirement: json|xml
  - Type: String
  - Description: Format of response, if empty will be JSON

#### Filters ####

limit:

  * Requirement: \d+
  * Description: Amount to return
  * Default: 10

offset:

  * Requirement: \d+
  * Description: Offset to use
  * Default: 0


### `POST` /api/v1/authentications.{_format} ###

_Add a new authentication to the system between a Teamspeak Account and a Minecraft account_

#### Requirements ####

**_format**

  - Requirement: json|xml
  - Type: String
  - Description: Format of response, if empty will be JSON

#### Parameters ####

ts_uuid:

  * type: 
  * required: true
  * description: Teamspeak UUID

ts_code:

  * type: 
  * required: true
  * description: Teamspeak Code

mc_uuid:

  * type: 
  * required: true
  * description: Minecraft UUID

mc_code:

  * type: 
  * required: true
  * description: Minecraft Code
