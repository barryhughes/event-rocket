# Event Rocket
Contributors: barry.hughes, mikejanzen, mikevalstar
Donate link: http://www.britishlegion.org.uk/get-involved/how-to-give
Tags: events, shortcodes, The Events Calendar, duplicate, rsvp
Requires at least: 4.0
Tested up to: 4.3
Stable tag: 3.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Unsupported set of experimental extensions, hacks and gadgetry for The Events Calendar

## Description

> As of October 2015, this plugin is largely unsupported and is unlikely to receive further bug fixes or
> improvements. Please consider that before using it in new projects.
>
> [docs](https://eventrocket.wordpress.com/) | [wiki](https://github.com/barryhughes/event-rocket/wiki) | [changelog](https://wordpress.org/plugins/event-rocket/changelog/)

Event Rocket is a set of hacks and utilities that add some extra capabilities to
[The Events Calendar](http://wordpress.org/plugins/the-events-calendar/) and
[Events Calendar PRO](http://tri.be/shop/wordpress-events-calendar-pro/). *It is no longer supported* and is
unlikely to receive further fixes.

## Installation

It's just like any other regular WordPress plugin - but it does expect [The Events Calendar](http://wordpress.org/plugins/the-events-calendar/)
and, ideally, [Events Calendar PRO](http://tri.be/shop/wordpress-events-calendar-pro/) to be installed and activated.

### Manual installation

1. Download the latest version of the plugin and unzip it
2. Upload the resulting `event-rocket` plugin directory to your `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. You're done!

## Upgrade Notice

When the time comes to upgrade you need take no special precautions :-)

## Frequently Asked Questions

### Is this plugin actively supported/maintained?

No.

### How can I find out more?

[The wiki](https://github.com/barryhughes/event-rocket/wiki) contains more detail on various topics
and is often worth looking at if you need more information. You can also find useful information
at [eventrocket.wordpress.com](https://eventrocket.wordpress.com/).

### How do I put the main events page on the front page of my site/blog?

* With Event Rocket activated, visit the Settings → Reading admin screen
* Choose a static page for the front page
* Select _Main Events Page_ for the Front Page option
* Save!

### What are the shortcodes used to embed different widgets in pages/posts?

* `[event_rocket_list]` embeds the list widget _(should be considered deprecated if you are an Events Calendar *PRO* user)_

The remaining "widget wrappers" have now been deprecated and should not be used. If you are currently using them,
please transition to the official alternatives provided by Events Calendar PRO.

### Can I embed arbitrary events with a shortcode?

Yes - you can use the `[event_embed]` shortcode to do this (please [see here](https://github.com/barryhughes/event-rocket/wiki/Embed-Events)).
Examples:

* `[event_embed from="2014-07-01" to="2014-07-31"]` grab events in July
* `[event_embed from="2014-10-01" category="fruit"]` grab events starting in October that belong to the category _fruit_

### How can I specify the venue or event ID?

Both the countdown and venue widgets need to know which event or venue you are referring to. All of the following are
examples of acceptable ways to pass this information:

* `[event_rocket_venue id="123"]` which is nice and short
* `[event_rocket_venue venue_id="123"]` is also allowed
* `[event_rocket_countdown id="789"]` this time the ID relates to the event
* `[event_rocket_countdown event_id="789"]` again you can be more explicit if you wish

### How can I cleanup events data?

A new menu option will appear in the WordPress _tools_ menu labelled 'Cleanup events data' -  please note that
you are strongly cautioned to make a full and complete backup before using this tool (and, of course, should make
yourself aware of the steps needed to restore that backup).

### How do I enable RSVPs?

When Event Rocket is activated the event editor should contain a special new RSVP section. You must visit this
and enable RSVPs for each applicable event.

Once enabled an RSVP form will be displayed on single event pages: for best results you are encouraged to use the
_restrict to logged in users_ option, otherwise you run the potential of dealing with spam responses and other issues.

## Screenshots

1. Here you can see the new _Main Events Page_ entry in the Reading Settings screen.
2. Duplicating an event - simply click the duplicate link in the admin events list and this convenient modal window appears
3. Editing venue coordinates (only applicable to users without Events Calendar PRO)
4. Cleanup tools menu entry
5. The actual cleanup screen
6. RSVP admin user interface - allow RSVPs on a per-event basis, restrict to logged in users only and view confirmation totals

## Changelog

### 3.3
* Localization: further improve support and update textdomain to match plugin slug
* Embedding: add basic offset and pagination capabilities to the event_embed framework
* Embedding: logic for parsing IDs and slugs was fixed (thanks Christina for highlighting this!)
* Duplication: improve meta data copying (ie support multiple organizers)
* Duplication: add ability to specify a different time (not just a different date)
* RSVP: fixes minor issue in loading of attendance data before displaying frontend form

### 3.2
* Further fix in `[event_embed]`'s query building
* Limited attendance option added (props: mikevalstar)
* Email attendees option added (props: mikevalstar)
* Option to show attendees on the frontend added (props: mikejanzen)
* Added .pot catalogue for translations
* Resolved caching bug in `[event_embed]` system

### 3.1
* Protective measures added to prevent/limit abuse of data shortcodes
* Resolve bug in `[event_embed]`'s handling of post IDs
* Cleaned up some event duplicator issues ahead of further improvements

### 3.0
* Min requirements bump: PHP 5.3, TEC 3.10, WP 4.0 all now expected
* Enhance event duplicator tool - make it easy to define the duplicate's title, date etc and ensure taxonomy terms are also carried over
* Improve compatibility of event duplicator with other plugins using custom list tables etc
* Add a variety of new inline template placeholders, including author and venue related tags
* Fix bug in cleanup tool preventing proper operation (props to Richard Miller)
* Venue embedding now supports filtering by country, city, state and zip/postal code (props to JamesWemyss for the suggestion)
* Embedded event engine now supports more natural use of recurring event slugs (thanks to anderscarlen on the WP forums for the idea)
* New shortcodes to provide iCal and CSV download links (props to @louking for the idea)
* Improvements to the RSVP system - it is now easier (and more efficient) to retrieve user-by-user attendance information
* Shortcodes used to embed Events Calendar PRO widgets are now deprecated as this facility is now provided by ECP itself

### 2.5
* Adds simple RSVP facilities

### 2.4
* Adds new `[organizer_embed]` shortcode
* Resolves issue with the `"with_events"` parameter for venue and organizer embedding
* Add reverse chronological ordering for `[event_embed]`
* Special parameter added to make it easy to list current/ongoing events only, ie `[event_embed where="current"]`
* Duplicate events - with the exception of recurring events - with a single click
* Internal restructuring to make future enhancements easier

### 2.3
* Officially adds blog switching support: within multisite networks you can now display events from one blog on any other blog
* Adds a venue embed shortcode that parallels the event embed shortcode
* Removes the troublesome admin menu extension - should ease compatibility concerns in a variety of situations

### 2.2
* Restore venue positiong for single events (where only The Events Calendar and not Events Calendar PRO is activated)
* Add caching abilities to the event_embed shortcode
* Add support for "AND" taxonomy queries

### 2.1
* Clean-up reference to old jettison module (thanks to MJTDI3 for highlighting this)

### 2.0
* Codebase reorganized
* Superfluous components as of The Events Calendar 3.8 release removed (inc coordinate-based maps)
* Improvements to the "Event Embed" shortcode including nothing-found parameters and smarter querying
* Module loading overhauled for simplicity and, hopefully, better localhost Windows compatibility
* Admin menu logic revised to help avoid conflicts with other plugins

### 1.5.1
* Single event maps now respect default zoom setting (thanks to lord_dev for the idea)
* It is now easier to override the embedded map zoom setting (thanks to troull88 for highlighting this need)
* Project Jettison: cleanup tools now remove any additional event-specific user capabilites that were registered

### 1.5.0
* Project GPS enhancements: replace single event/venue embedded maps to use coordinate-based positioning (thanks
to mcreighton for the suggestion)
* Project GPS enhancements: flip order of lat/long fields in venue editor meta box (thanks to Casey Driscoll for the
suggestion)
* Widget shortcodes: new attributes to make specifying tags and categories easier - impacts on the calendar widget
and advanced (PRO) list widget

### 1.4.4
* Link to single events via the default `[event_embed]` template (thanks to ddggccaa for highlighting this!)

### 1.4.3
* Fixes to help smoother use of list view on the homepage (thanks to chwebdev for pointing out this issue)
* Addition of venue and organizer parameters to `[event_embed]` shortcode
* New placeholders for inline `[event_embed]` templates - url, link and description

### 1.4.2
* Fixes to `[event_embed]` to respect template and limit parameters (thanks to williamlevins for highlighting
this!)

### 1.4.1
* Shortcode enhancements: `[event-embed]` added
* Fix for potential issue with venue coordinate inputs in Chrome (thanks to Leah for noticing this!)

### 1.4.0
* Project Jettison: adds clean up tools - after deactivating core (The Events Calendar) clean up tools are added
(to the Tools admin menu) to enable removal of event data
* Fixes bug in Project Nosecone: list view wasn't rendering as expected when added to the front page (thanks to Leah
for highlighting this!)

### 1.3.4
* Fixes bug in Project HUD: conflict with Community Events (thanks to mimi.cummins for highlighting this one!)

### 1.3.3
* Fixes bug in Project HUD: attempting to build list of settings tabs fails during ajax requests

### 1.3.2
* Project HUD: extends the admin toolbar, initially with direct links to various settings tabs

### 1.3.1
* Bug fixes (thanks to GonzaloTGEB for highlighting some issues)

### 1.3
* Project 404 Laser: attempt to force a 200 OK status on empty day views

### 1.2
* Nosecone improvements (front page support): change all references to the main event page so they "point" to the front
page instead
* Project GPS: allow adjustments to stored longitude/latitude data for venues

### 1.1
* Project Nosecone: put the main events page on the blog front page

### 1.0
* Initial release
