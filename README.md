# URL Normalizer

Provides URL normalization (rewrite) features for migrating a Joomla site from one domain to another and/or from HTTP to HTTPS (or vice versa).

This system plugin for Joomla will rewrite all internal (and some common external) URLs to match your settings. URLs from YouTube and Vimeo (used in <iframe> embeds) will be re-written to use HTTPS.

It also features:
- JS based redirects from HTTP to HTTPS (and vice versa) - perfect for when a Joomla site is behind CloudFlare's CDN, using Flexible SSL and served via Varnish (which supports HTTP only)
- better client-side caching header setup (with component exclusions) which can greatly assist in frontend performance, especially when Joomla is behind a caching proxy like Varnish or Nginx
- Custom HTTP header (X-Logged-In) transmission to assist in detecting user logins when using Joomla behind a caching proxy like Varnish or Nginx
- tidy HTML markup (the rendered output) by using the PHP Tidy library, adapted for HTML5
- assists in "adaptive" website development (separate desktop & mobile versions) by setting a PHP constant (SITE_VIEW) to use anywhere in Joomla to distinguish a desktop from a mobile request (uses the ?m or &m URL modifier)

...with more features to be gradually added.


## COMPATIBILITY
The plugin works with PHP5 and PHP7.

It is fully compatible with Joomla versions 1.5, 2.5 & 3.x.


## DOWNLOAD
You can get the latest (published) version here:

https://www.joomlaworks.net/downloads/?f=plg_urlnormalizer-v1.4_j1.5-3.x.zip (recommended)

...or you can get the latest export from this repo here: https://github.com/joomlaworks/url-normalizer/archive/master.zip

The plugin supports updating via the Joomla Updater, so any new releases will appear there.


## LICENSE
The plugin is distributed under the [GNU/GPL license](https://www.gnu.org/licenses/gpl.html).
