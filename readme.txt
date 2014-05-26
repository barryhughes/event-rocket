=== Event Rocket ===
Contributors: barry.hughes, shane.pearlman
Donate link: http://www.britishlegion.org.uk/get-involved/how-to-give
Tags: events, shortcodes
Requires at least: 3.8
Tested up to: 3.8.1
Stable tag: 1.4.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Experimental extension for The Events Calendar and Events Calendar PRO adding shortcodes, front page events and more.

== Description ==

This is an add-on that builds on top of the following wonderful plugins:

* [The Events Calendar](http://wordpress.org/plugins/the-events-calendar/) (required)
* [Events Calendar PRO](http://tri.be/shop/wordpress-events-calendar-pro/) (optional but recommended)
* Version 3.6 or greater for both of the above are suggested

So if you don't already have them installed it behooves you to do so now. This plugin then adds the following power-ups:

* It lets you position the main events page on the front page of your blog
* Precise editing of venue coordinates becomes possible for when street addresses just don't cut it
* A 404 Laser has been added to help blast away pesky 404 issues, especially with regards to empty day views
* Event widgets can be deployed as shortcodes and you can embed events inline *anywhere* using the `[event_embed]` shortcode
* You can access all event setting tabs directly from the admin menu
* Clean up and remove events data if you decide you don't need it any longer

Check out the FAQs and screenshots for more examples.

== Installation ==

It's just like any other regular WordPress plugin - but it does expect [The Events Calendar](http://wordpress.org/plugins/the-events-calendar/)
and, ideally, [Events Calendar PRO](http://tri.be/shop/wordpress-events-calendar-pro/) to be installed and activated.

= Manual installation =

1. Download the latest version of the plugin and unzip it
2. Upload the resulting `event-rocket` plugin directory to your `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. You're done!

== Upgrade Notice ==

When the time comes to upgrade you need take no special precautions :-)

== Frequently Asked Questions ==

= How can I find out more? =

[The wiki](https://github.com/barryhughes/event-rocket/wiki) contains more detail on various topics
and is often worth looking at if you need more information.

= How do I put the main events page on the front page of my site/blog? =

* With Event Rocket activated, visit the Settings → Reading admin screen
* Choose a static page for the front page
* Select _Main Events Page_ for the Front Page option
* Save!

= What are the shortcodes used to embed different widgets in pages/posts? =

* `[event_rocket_calendar]` embeds the calendar widget
* `[event_rocket_list]` embeds the list widget
* `[event_rocket_countdown]` embeds the event countdown widget
* `[event_rocket_venue]` embeds the featured venue widget

Please note however that if you are not using Events Calendar PRO then any widgets specific to that plugin (such as the
countdown and calendar widget) will *not* be available.

= Can I embed arbitrary events with a shortcode? =

Yes - you can use the `[event_embed]` shortcode to do this (please [see here](https://github.com/barryhughes/event-rocket/wiki/Embed-Events)).
Examples:

* `[event_embed from="2014-07-01" to="2014-07-31"]` grab events in July
* `[event_embed from="2014-10-01" category="fruit"]` grab events starting in October that belong to the category _fruit_

= How can I specify the venue or event ID? =

Both the countdown and venue widgets need to know which event or venue you are referring to. All of the following are
examples of acceptable ways to pass this information:

* `[event_rocket_venue id="123"]` which is nice and short
* `[event_rocket_venue venue_id="123"]` is also allowed
* `[event_rocket_countdown id="789"]` this time the ID relates to the event
* `[event_rocket_countdown event_id="789"]` again you can be more explicit if you wish

= How do I specify a category (when embedding the list widget)? =

The list widget allows you to specify a category of events. This is also possible via the corresponding shortcode,
simply do this:

`[event_rocket_list category="987"]`

Where 987 would be replaced with the actual category ID you wish to use.

= How can I make the countdown widget display seconds? =

You can let it know you want the seconds to be displayed by using the `show_seconds` attribute, something like this:

`[event_rocket_countdown id="789" show_seconds="true"]`

= How can I cleanup events data? =

A new menu option will appear in the WordPress _tools_ menu labelled 'Cleanup events data' - by default this *only*
appears when The Events Calendar is deactivated. You are strongly cautioned to make a full and complete backup before
using this tool (and, of course, should make yourself aware of the steps needed to restore that backup).

== Screenshots ==

1. Here you can see the new _Main Events Page_ entry in the Reading Settings screen.
2. Example of embedding a widget - in this case, the countdown widget - within a page or post.
3. The actual output with the countdown widget embedded in the page. A great example as it also shows the sort of flaws
in terms of styling that can occur theme to theme (ie, to make things super-seamless some CSS knowledge is going to be
required).
4. Editing venue coordinates
5. Enhanced admin toolbar options
6. Cleanup tools menu entry (will not normally appear unless The Events Calendar has been deactivated)
7. The actual cleanup screen

== Changelog ==

= 1.4.3 =
* Fixes to help smoother use of list view on the homepage (thanks to chwebdev for pointing out this issue)
* Addition of venue and organizer parameters to `[event_embed]` shortcode
* New placeholders for inline `[event_embed]` templates - url, link and description

= 1.4.2 =
* Fixes to `[event_embed]` to respect template and limit parameters (thanks to williamlevins for highlighting
this!)

= 1.4.1 =
* Shortcode enhancements: `[event-embed]` added
* Fix for potential issue with venue coordinate inputs in Chrome (thanks to Leah for noticing this!)

= 1.4.0 =
* Project Jettison: adds clean up tools - after deactivating core (The Events Calendar) clean up tools are added
(to the Tools admin menu) to enable removal of event data
* Fixes bug in Project Nosecone: list view wasn't rendering as expected when added to the front page (thanks to Leah
for highlighting this!)

= 1.3.4 =
* Fixes bug in Project HUD: conflict with Community Events (thanks to mimi.cummins for highlighting this one!)

= 1.3.3 =
* Fixes bug in Project HUD: attempting to build list of settings tabs fails during ajax requests

= 1.3.2 =
* Project HUD: extends the admin toolbar, initially with direct links to various settings tabs

= 1.3.1 =
* Bug fixes (thanks to GonzaloTGEB for highlighting some issues)

= 1.3 =
* Project 404 Laser: attempt to force a 200 OK status on empty day views

= 1.2 =
* Nosecone improvements (front page support): change all references to the main event page so they "point" to the front
page instead
* Project GPS: allow adjustments to stored longitude/latitude data for venues

= 1.1 =
* Project Nosecone: put the main events page on the blog front page

= 1.0 =
* Initial release