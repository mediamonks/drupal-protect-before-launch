# Protect before launch
Protected your website before launch with HTTP basic authentication.
The module allows you to set a username and password and enable and
disable them on the fly.

## Settings
The module allows you to set the following options for authentication.

* Realm
* Denied Content
* Username
* Password
* Exclude paths
* System variable
* System Variable value
* Identity provider

### Auto enable with System variable
Set the enabled mode to Environmental key/value.
When this mode is set the module will look for the variable and
when this is set it will enable the password protection.<br />
<br />
When also the System Variable value is set the the module will check
if the value in the variable is set correctly before enabling.<br />
<br />
### Acquia hosting
Default value is set to "AH\_NON\_PRODUCTION" to work correctly with
Acquia Hosting out of the box. Just select set an username and 
password and set protection mode to "env_enabled". All none 
production servers will be password protected.

### Identity provider
The module allows you to use the drupal user database to authenticate
against or the simple username and password set directly into the
module. This username and password is independent from the Drupal
user database.

### Exclude paths
The exclude paths option allows you to set urls or patterns (preg_match).
When the url matches an exclude path for that path no passwords will be
requested.

### Default username/passsword
**username:** username<br />
**password:** password

### Configuration screen
_Admin_ > _Configuration_ > _Development_ > _Protect Before Launch_.

## Console commands

**protect\_before\_launch:status [disabled | enabled | env]**<br />
Enable / Disable the password protection

**protect\_before\_launch:username [\<username\>]**<br />
Set the username to authenticate against.

**protect\_before\_launch:password [\<password\>]**<br />
Set the password to authenticate against.

**protect\_before\_launch:environment [\<key\>] [\<value\>]**<br />
Set the environment environment key and environment value

**protect\_before\_launch:environment [\<key\>] --no-value**<br />
Set the environment environment key without environment value
