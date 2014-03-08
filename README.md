Event Rocket
============

Testbed for adding super powers to The Events Calendar/Events Calendar PRO (!)

Basic usage:

* It's a plugin!
* Activate TEC/ECP - this plugin depends on them
* Add `[event_rocket_calendar]` to a page or post
* The calendar widget should appear wherever you added that shortcode!
* You can even add params like `[event_rocket_calendar count="3"]` to limit the number of events listed beneath the calendar

Some further examples from the readme.txt FAQ section:

= What are the shortcodes used to embed different widgets in pages/posts? =

* `[event_rocket_calendar]` embeds the calendar widget
* `[event_rocket_list]` embeds the list widget
* `[event_rocket_countdown]` embeds the event countdown widget
* `[event_rocket_venue]` embeds the featured venue widget

= How can I specify the venue or event ID? =

Both the countdown and venue widgets need to know which event or venue you are referring to. All of the following are
examples of acceptable to pass this information:

* `[event_rocket_venue id="123"]` which is nice and short
* `[event_rocket_venue venue_id="123"]` is also allowed
* `[event_rocket_countdown id="789"]` this time the ID relates to the event
* `[event_rocket_countdown event_id="789"]` again you can be more explicit if you wish

= How can I make the countdown widget display seconds? *

You can let it know you want the seconds to be displayed by using the `show_seconds` attribute, something like this:

* `[event_rocket_countdown id="789" show_seconds="true"]`