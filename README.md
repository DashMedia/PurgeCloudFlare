PurgeCloudFlare
===============



##Setup Directions:

Goto System Settings and update the following:

`cf_api_key` = your CloudFlair api key, this is visible on the Account tab of CloudFlair https://www.cloudflare.com/my-account

`cf_email`   = The email address associated with the account (the one you use to log in to CloudFlair)

Please note that PurgeCloudFlare relies on your `http_host` setting to tell CloudFlare which domain/account to clear files from, if you're using multiple contexts they will each need this setting.

Ignoring a context: if you do not wish PurgeCloudFlare to attempt to clear the CloudFlare cache for a specific context, add a context setting named `cf_skip` and set its value to `1`

Once you have done this, MODX will clear your CloudFlare Cache every time the system-wide cache is cleared, and will clear individual pages when they are saved from the MODX manager

Created by Jason Carney - DashMedia.com.au