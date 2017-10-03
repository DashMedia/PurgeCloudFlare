PurgeCloudFlare
===============

## Setup Directions:

Goto System Settings and update the following:

`cf_api_key` = your CloudFlare api key, this is visible on the Account tab of CloudFlare https://www.cloudflare.com/my-account

`cf_email`   = The email address associated with the account (the one you use to log in to CloudFlare)

Please note that PurgeCloudFlare relies on your `http_host` setting to tell CloudFlare which domain/account to clear files from, if you're using multiple contexts they will each need this setting.

Once you have done this, MODX will clear your CloudFlare Cache every time the system-wide cache is cleared, and will clear individual pages (and the parents of the saved page) when they are saved from the MODX manager

## Ignoring a context

If you do not wish PurgeCloudFlare to attempt to clear the CloudFlare cache for a specific context, add a context setting named `cf_skip` and set its value to `1`

## Development mode

You can make MODX automatically enable development mode when clearing the global cache, by creating a context setting of `cloudflare.use_dev` and setting it to 1 on any contexts you wish. (or the system setting, to make it do this by default)

Created by Jason Carney - DashMedia.com.au
