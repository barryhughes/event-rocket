=== Event Rocket ===
Contributors: barry.hughes
Donate link: http://www.britishlegion.org.uk/get-involved/how-to-give
Tags: events, shortcodes
Requires at least: 3.8
Tested up to: 3.8.1
Stable tag: 1.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Rocket themed extension for The Events Calendar. Pimps things up with some shortcodes and other stuff.

== Description ==

This is an add-on that builds on top of the following wonderful plugins:

* [The Events Calendar](http://wordpress.org/plugins/the-events-calendar/) (required)
* [Events Calendar PRO](http://tri.be/shop/wordpress-events-calendar-pro/) (optional but recommended)
* Version 3.4 or greater for both of the above are suggested

So if you don't already have them installed it behooves you to do so now ;-)

In short, it allows you to add widgets into pages or posts as shortcodes - here's an example showing how you can add
the calendar widget within your copy for a page or post:

`[event_rocket_calendar]`

Here is a slightly more complex example where we go the extra mile and specify that we want no more than 3 events to
be listed below it:

`[event_rocket_calendar count="3"]`

Other stuff may be added in time.

== Installation ==

It's just like any other regular WordPress plugin - but it does expect [The Events Calendar](http://wordpress.org/plugins/the-events-calendar/)
and, ideally, [Events Calendar PRO](http://tri.be/shop/wordpress-events-calendar-pro/) to be installed and activated.

= Manual installation =

1. Download the latest version of the plugin and unzip it
2. Upload the resulting `event-rocket` plugin directory to your `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. You're done!


== Frequently Asked Questions ==

= What are the shortcodes used to embed different widgets in pages/posts? =

* `[event_rocket_calendar]` embeds the calendar widget
* `[event_rocket_list]` embeds the list widget
* `[event_rocket_countdown]` embeds the event countdown widget
* `[event_rocket_venue]` embeds the featured venue widget

== Screenshots ==

There are no screenshots at this time!

== Changelog ==

= 1.0 =
* Initial release (version numbering broadly intended to coincide with The Events Calendar releases).
* Another change.