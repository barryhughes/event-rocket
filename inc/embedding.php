<?php
defined( 'ABSPATH' ) or exit();


require_once EVENTROCKET_INC . '/embedding/widget-wrapper.php';
require_once EVENTROCKET_INC . '/embedding/widget-adjusters.php';

require_once EVENTROCKET_INC . '/embedding/inline-templates-parser.php';
require_once EVENTROCKET_INC . '/embedding/inline-templates-events.php';
require_once EVENTROCKET_INC . '/embedding/inline-templates-venues.php';
require_once EVENTROCKET_INC . '/embedding/inline-templates-organizers.php';

require_once EVENTROCKET_INC . '/embedding/lister-objects.php';
require_once EVENTROCKET_INC . '/embedding/lister-events.php';
require_once EVENTROCKET_INC . '/embedding/lister-venues.php';
require_once EVENTROCKET_INC . '/embedding/lister-organizers.php';

require_once EVENTROCKET_INC . '/embedding/embed-events.php';
require_once EVENTROCKET_INC . '/embedding/embed-venues.php';
require_once EVENTROCKET_INC . '/embedding/embed-organizers.php';

/**
 * Set up our widget-based shortcodes: if PRO is not enabled those shortcodes won't actually
 * be registered with WordPress. If PRO *is* enabled, the PRO list widget supercedes the
 * core equivalent.
 */
new EventRocket_WidgetShortcodes( 'TribeEventsMiniCalendarWidget', 'event_rocket_calendar' );
new EventRocket_WidgetShortcodes( 'TribeEventsListWidget', 'event_rocket_list' );
new EventRocket_WidgetShortcodes( 'TribeEventsAdvancedListWidget', 'event_rocket_list' );
new EventRocket_WidgetShortcodes( 'TribeCountdownWidget', 'event_rocket_countdown' );
new EventRocket_WidgetShortcodes( 'TribeVenueWidget', 'event_rocket_venue' );
