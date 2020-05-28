# URL Normalizer

Provides URL normalization (rewrite) & performance features to Joomla sites.

This system plugin for Joomla will rewrite all internal (and some common external) URLs to match your settings, which greatly helps when migrating a Joomla site from one domain to another and/or for switching from HTTP to HTTPS (or vice versa).

URLs from YouTube, Vimeo, Dailymotion, Facebook & Twitter (used in <iframe> or <script> embeds) will be re-written to use HTTPS. So no nagging "this site is not secure" browser messages caused by mixed internal or third-party content on your site.

Although URL rewriting/normalization was the initial goal for this plugin, it quickly became apparent that performance features could easily be integrated into it.

These additional features are currently available:

- JS based redirects from HTTP to HTTPS (and vice versa) - perfect for when a Joomla site is behind CloudFlare's CDN, using Flexible SSL and served via Varnish (which supports HTTP only).
- Enforce better client-side caching (with component exclusions) which can greatly assist in frontend performance, especially when Joomla is behind a caching proxy like Varnish or Nginx. If you use the Joomla Page Cache plugin as well, just remember to disable client-side caching there. Client-side caching can been configured differently between the home page and all inner pages. The benefit of enforcing client-side caching separately is that you don't have to explicitly enable Joomla (server-side) caching as well if you don't want to. Or you can choose to combine it with Joomla's general cache (fragment cache) but not the Page Cache plugin (full page cache).
- Custom HTTP header (X-Logged-In) transmission to assist in detecting user logins when using Joomla behind a caching proxy like Varnish or Nginx.
- Add the loading="lazy" attribute for lazy loading images in mid-2019 or later browsers.
- Assists in "adaptive" website development (separate desktop & mobile versions) by setting a PHP constant (SITE_VIEW) to use anywhere in Joomla to distinguish a desktop from a mobile request (uses the ?m and &m URL modifier).
- Tidy HTML markup (the rendered output) by using the PHP Tidy library, adapted for HTML5. This option requires that the relevant Tidy module for PHP is installed on your server.

...with more features to be gradually added in the plugin.

URL Normalizer works beautifully with the Joomla Page Cache plugin enabled. Just remember to order URL Normalizer right before (above) the Joomla Page Cache plugin in the Plugin Manager.

The plugin currently powers some of the largest Joomla sites in the world (in terms of absolute monthly visitors), so it's battle-tested.


## DOWNLOAD
You can get the latest (published) version here:

https://www.joomlaworks.net/downloads/?f=plg_urlnormalizer-v1.7_j1.5-3.x.zip (recommended)

Or you can get the latest export from this GitHub repo here: https://github.com/joomlaworks/url-normalizer/archive/master.zip

The plugin supports updating via the Joomla Updater, so any new releases will appear there.


## COMPATIBILITY
URL Normalizer is fully compatible with Joomla versions 1.5, 2.5, & 3.x on servers running PHP 5 or 7.


## LICENSE
URL Normalizer  is a Joomla plugin developed by [JoomlaWorks](https://www.joomlaworks.net), released under the GNU General Public License.
