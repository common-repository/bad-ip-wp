=== bad_ip WP ===
Contributors: iridiumintel
Tags: bad_ip, firewall, login protection, security, tor, block, malicious, bad, ip, ban, auto, list
Requires at least: 3.0.1
<<<<<<< HEAD
Tested up to: 5.3.2
Stable tag: 1.0.8
=======
Tested up to: 5.5.1
Stable tag: 1.1.2
>>>>>>> 046a612abd639c6f6ee3362d6bc3ac093b9a7420
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

This is Wordpress version of bad_ip plugin, used to protect and report malicious IP addresses visiting and trying to exploit your website with addition to block Tor endpoints.

Beside monitoring and logging malicious actions on your website, plugin consumes bad_ip API endpoints to check every IP accessing your site against bad_ip public database.

== Installation ==

1. Upload `bad_ip WP` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Libraries ==

To make development and maintaining of our plugin as easy as possible, current version uses next public libraries and plugins:

* [Bootsrtap](https://getbootstrap.com/)
* [Chart.js](https://www.chartjs.org/)
* [jQuery](https://jquery.com/)
* [jQueryUI](https://jqueryui.com/easing/)
* [Timber](https://github.com/timber/timber)

== Frequently Asked Questions ==

= What if I lock my self out of my web site? =

Delisting from bad_ip database is pretty straight forward, after you have been redirected as IP with restriction you will be offered a form to unlist your IP, just fill your email address, send request and you will receive link to remove your IP from database.

= Can Web Crawlers still index my web site? =

Plugin comes with options in settings to allow crawler bots to bypass reporting and blocking mechanisms, and is enabled by default.

= Can I manually whitelist IP? =

Yes, in plugin's settings page you can manually add and remove IP's to white and black lists.

= What if broken link is being detected as bad query? =

You can add exception for any reported bad query by clicking on icon next to query inside Bad Queries list on main plugin's page

== Screenshots ==

1. Main plugin page where user can observe latest reports segmented in several sections covering login attempts, bad queries logged and access to site from Tor browsers with additional details
2. Settings page of the bad_ip plugin where user can enable or disable specific functionalities of the plugin

== Changelog ==

= 1.1.2 =
* Regular refactoring and optimisation

= 1.1.1 =
* Regular refactoring and optimisation

= 1.1.0 =
* Regular refactoring and optimisation

= 1.0.20 =
* Regular refactoring and optimisation

= 1.0.13 =
* Regular refactoring and optimisation

= 1.0.12 =
* Regular refactoring and optimisation

= 1.0.11 =
* Regular refactoring and optimisation

= 1.0.10 =
* Server migration
* Regular refactoring and optimisation

= 1.0.9 =
* Patch
* Regular refactoring and optimisation

= 1.0.8 =
* Regular refactoring and optimisation

= 1.0.7 =
* Storing of IP and query whitelists and blacklists moved to database
* Implemented ipalyzer as external IP info service for reported IP's
* Regular refactoring and optimisation

= 1.0.6 =
* Patch with unlist request
* Bad queries check upgrade
* Regular refactoring and optimisation

= 1.0.5 =
* Patch with missing view

= 1.0.4 =
* Upgrade process refactor
* Query whitelisting
* Regular refactoring and optimisation

= 1.0.3 =
* Introducing data visualisation in reports
* Regular refactoring and optimisation

= 1.0.2 =
* Upgrade process refactor

= 1.0.1 =
* Manually add IP to white or black list
* IP info link on blocked IP's
* Unlist request for blocked IP
* Option to allow web crawlers
* Refactoring

= 1.0.0 =
* Initial release of the plugin.
