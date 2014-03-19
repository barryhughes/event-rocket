=== Event Rocket ===
Contributors: barry.hughes
Donate link: http://www.britishlegion.org.uk/get-involved/how-to-give
Tags: events, shortcodes
Requires at least: 3.8
Tested up to: 3.8.1
Stable tag: 1.3.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Rocket themed extension adding experimental features to The Events Calendar like shortcodes and front-page events.

== Description ==

This is an add-on that builds on top of the following wonderful plugins:

* [The Events Calendar](http://wordpress.org/plugins/the-events-calendar/) (required)
* [Events Calendar PRO](http://tri.be/shop/wordpress-events-calendar-pro/) (optional but recommended)
* Version 3.4 or greater for both of the above are suggested

So if you don't already have them installed it behooves you to do so now. This plugin then adds the following power-ups:

* It lets you position the main events page on the front page of your blog
* Precise editing of venue coordinates becomes possible for when street addresses just don't cut it
* A 404 Laser has been added to help blast away pesky 404 issues, especially with regards to empty day views
* Event widgets can be deployed as shortcodes

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

= How can I specify the venue or event ID? =

Both the countdown and venue widgets need to know which event or venue you are referring to. All of the following are
examples of acceptable ways to pass this information:

* `[event_rocket_venue id="123"]` which is nice and short
* `[event_rocket_venue venue_id="123"]` is also allowed
* `[event_rocket_countdown id="789"]` this time the ID relates to the event
* `[event_rocket_countdown event_id="789"]` again you can be more explicit if you wish

= How can I make the countdown widget display seconds? =

You can let it know you want the seconds to be displayed by using the `show_seconds` attribute, something like this:

* `[event_rocket_countdown id="789" show_seconds="true"]`

== Screenshots ==

1. Here you can see the new _Main Events Page_ entry in the Reading Settings screen.
2. Example of embedding a widget - in this case, the countdown widget - within a page or post.
3. The actual output with the countdown widget embedded in the page. A great example as it also shows the sort of flaws
in terms of styling that can occur theme to theme (ie, to make things super-seamless some CSS knowledge is going to be
required).
4. Editing venue coordinates

== Changelog ==

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
* Initial release (version numbering broadly intended to coincide with The Events Calendar releases).
* Another change.