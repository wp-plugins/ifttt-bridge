=== IFTTT Bridge for WordPress ===
Contributors: bjoerne
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XS98Y5ASSH5S4
Tags: ifttt, ifthisthenthat
Requires at least: 3.9
Tested up to: 4.0
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

IFTTT Bridge for WordPress is a plugin that allows you to display IFTTT-processed data on your WordPress site in any way you like.

== Description ==

IFTTT Bridge for WordPress is a plugin that allows you to display IFTTT-processed data on your WordPress site in any way you like.

*One plugin, unlimited possibilities*

If you love IFTTT, but have always regretted that there are too many limits on what you can do with the standard IFTTT-WordPress channel, then this plugin is for you.

IFTTT Bridge for WordPress is a technical bridge between IFTTT und WordPress that allows flexible use of IFTTT-processed data in WordPress. There are no limits to what can be displayed and how.

One example is the [IFTTT Instagram Gallery](https://wordpress.org/plugins/ifttt-instagram-gallery/). Instead of using the standard WordPress channel offered by IFTTT, which only posts one photo at a time, IFTTT Instagram Gallery will allow you to show your latest Instagram photos in an awesome and highly customizable sidebar grid or within your text field, displaying any number of photos and columns you like.

*For blog owners and administrators*

IFTTT Bridge for WordPress will only prepare and process your IFTTT data, ensuring that you can use it on your WordPress blog in any way you like. To make it “come alive” on your blog, you will have to install a second plugin. Below you will find a list of currently available plugins that are compatible with IFTTT Bridge for WordPress:  

- [IFTTT Instagram Gallery](https://wordpress.org/plugins/ifttt-instagram-gallery/)

*For developers*

IFTTT Bridge for WordPress will process the data received and call the WordPress activity "ifttt-bridge". Any plugins that have registered for this activity will be notified and will receive the data.

If you have developed a plugin or plan to do so, feel free to contact me! I will gladly include your published plugin in this list.

*What is IFTTT?*

IFTTT or “If This Then That” is a service that enables users to connect different web applications (e.g., Facebook, Evernote, Weather, Dropbox, etc.) together through simple conditional statements known as “Recipes”. It sounds very technical but is actually really easy. Here are some typical examples of what IFTTT can do:

* If you post a new photo on Instagram, it will automatically be posted on your Facebook wall.
* When a new item on eBay comes up that matches your search criteria, the results will be sent to you via email.
* Every time you are tagged in a photo on Facebook, it will be sent to Dropbox.

*What do I have to do to use this plugin?*

1. Install this plugin (installation instructions can be found under the “Installations” tab)
2. Register at www.ifttt.com
3. Install the IFTTT Instagram Gallery or any other IFTTT plugin that fits your purpose. If you are a developer, you might even want to develop a plugin yourself.
4. Check out the logging and the test request form in the option panel. This shoud help you if you are using this plugin for the first time.

If you need help, don’t hesitate to contact me! In addition this [German blog article](http://www.bjoerne.com/instagram-bilder-mit-ifttt-den-eigenen-wordpress-blog-einbinden/) may help you.

If you like this plugin, please rate it.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'IFTTT Bridge for WordPress'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `ifttt-bridge.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `ifttt-bridge.zip`
2. Extract the `ifttt-bridge` directory to your computer
3. Upload the `ifttt-bridge` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

== Screenshots ==

1. Configure IFTTT to use this plugin. Don't forget the 'ifttt_bridge' tag
2. Send a test request when you are using this plugin the first time
3. Use the logging to track how the IFTTT request is processed or to find (configuration) errors

== Changelog ==

= 1.0.1 =
* Bugfix: Logging didn't work anymore after it had been emptied

= 1.0.2 =
* Bugfix: htmlspecialchars is not applied to raw request anymore but to all displayed log entries
