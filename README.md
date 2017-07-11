# Protect before launch
Protected your website before launch with HTTP basic authentication. The module allows you to set a username and password and enable and disable them on the fly.

## Settings
The module allows you to set the following options for authentication.

* Realm
* Denied Content
* Username
* Password
* Exclude paths

### Exclude paths
The exclude paths option allows you to set urls or patterns (preg_match). When the url matches an exclude path for that path no passwords will be requested.

### Configuration screen
_Admin_ > _Configuration_ > _Development_ > _Protect before launch_.

## Console commands

**protect\_before\_launch:enabled [true|false]**<br />
Enable / Disable the password protection

**protect\_before\_launch:username [username]**<br />
Set the username to authenticate against.

**protect\_before\_launch:password [password]**<br />
Set the password to authenticate against.