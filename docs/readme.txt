Go to System Settings and update the following:

cloudflare.api_key = your CloudFlare api key, this is visible on the Account tab of CloudFlare https://www.cloudflare.com/my-account

cloudflare.email_address = The email address associated with the account (the one you use to log in to CloudFlare)

Optional context/system settings

cloudflare.skip = 1; ignore this context when clearing global cache

cloudflare.use_dev = 1; enable CloudFlare Development mode when clearing the global cache

Please note that PurgeCloudFlare relies on your http_host setting to tell CloudFlare which domain/account to clear files from, if you're using multiple contexts they will each need this setting.

Ignoring a context: if you do not wish PurgeCloudFlare to attempt to clear the CloudFlare cache for a specific context, add a context setting named 'cloudflare.skip' and set its value to 1

Once you have done this, MODX will clear your CloudFlare Cache every time the system-wide cache is cleared, and will clear individual pages when they are saved from the MODX manager

UPDATE: Saving a page will also clear the parents of the saved page (not doing this was confusing many of our clients, so I'd assume you were having similar issues with your own)

Installation Instructions

Install via Package Manager, or grab the static files from our GitHub Repo feel free to report bugs