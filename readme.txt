=== Plugin Name ===
Contributors: hikingnerd
License: GPLv2 or later
Donate link: http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/#Contribution
Tags: owncloud, Bookmarks, api, integration, shortcode
Requires at least: 3.9.2
Tested up to: 4.1.0
Stable tag: 1.0.0

Embed your Bookmarks that are managed by ownCloud in your WordPress posts and pages as table.

== Description ==
This plugin allows you to make use of your ownCloud bookmarks in WordPress posts and pages. You can:

* make use of the ownCloud Bookmarks App (<a href="https://github.com/owncloud/Bookmarks" target="_blank">Link to the newest version</a>).
* access the ownCloud database after configuring it like described <a href="http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/#MySQL mode" target="_blank">in this tutorial section</a>.
* Use the shortcode [ oc2wpbm] to generate tables that contain ownCloud Bookmarks that ared tagged with 'public'.
* Use the shortcode [ oc2wpbm tag="example"] to generate tables that contain ownCloud Bookmarks that ared tagged with 'example'.
* Use the shortcode [ oc2wpbm tag="example, public"] to generate tables that contain ownCloud Bookmarks that ared tagged with 'example' or 'public'.
* Use the shortcode [ oc2wpbm tag="example, public" connector="AND"] to generate tables that contain ownCloud Bookmarks that ared tagged with both: 'example' AND 'public'.
* Configure the <a href ="http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/#configure the table layout" target="_blank">layout of the table</a>.

find more <a href ="http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/" target="_blank">in this tutorial</a>


== Installation ==
1. Decide for one <a href="http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/#Preconditions & Installation">operation mode</a>
1. If you wish to make use of the ownCloud App operation mode ensure that on your ownCloud server php5-curl is running and that the ownCloud Bookmarks App <a href="http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/#Replace the ownCloud Bookmarks App" target="_blank">is supporting the REST API</a>
1. Download and copy the plugin into the folder `/wp-content/plugins/`
1. Activate the plugin by making use of the /Plugin area in the WordPress backend.
1. go to /Settings/OC2WP Bookmarks and configure the operation mode of the plugin
1. put the shortcode [ oc2wp] into the page or post that should contain a table of bookmarks that are tagged with 'public'

== Frequently Asked Questions ==

= Are there preconditions that my ownCloud instance has to satisfy? =

The server running your owncloud instance needs to run php5-curl. Furthermore you have to replace the existing ownCloud Bookmarks App (<a href="https://github.com/owncloud/Bookmarks" target="_blank">this new version</a> will be included with ownCloud 8.0) or to provide access to the SQL-Database.

= Which operation mode is appropriate? =

In general it is recommended to use the ownCloud App mode. The MySQL mode only is for those appropriate who whish to access the Bookmarks of all users of an ownCloud instance or those that cannot change the ownCloud Bookmarks App.

= How to configure the OC mode =

Enter the credentials of the ownCloud account that owns the Bookmarks that should be published. 

= Language =
This plugin is currently only available in English but you can set the title of the generated tables to your own needs in your own language.

= What are the shortcodes to embed a table containing the ownCloud Bookmarks into posts or pages? =
* embed those Bookmarks that are tagged with 'public': [ oc2wpbm]
* embed those Bookmarks that contain one out of a set of tags (in this case 'public' or 'example'): [ oc2wpbm tags=”public, example”] 
* embed those Bookmarks that contain a specific set of Bookmars (in this case 'public' AND 'example'): [ oc2wpbm tags=”public, example” connector=”AND”] 

= What are the next steps for this plugin =
Currently I am working to enhance the sorting capabilites. After this my plan is to add a widget for sidebars so that ownCloud bookmarks also can be used within the widget areas.
Further suggestions are welcome!


== Screenshots ==
1. oc2wp Settings
2. ownCloud Plugin
3. Resulting Table enhanded by TablePress

== Changelog ==
= 1.0.0 =
* Very first version enabling to connect via SQL or the ownCloud Plugin REST API to the ownCloud instance using the tags.

