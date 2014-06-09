<?php
defined( 'ABSPATH' ) or exit();


require_once EVENTROCKET_INC . '/shortcodes/widget-wrapper.php';
require_once EVENTROCKET_INC . '/shortcodes/widget-adjusters.php';
require_once EVENTROCKET_INC . '/shortcodes/embedded-templates.php';
require_once EVENTROCKET_INC . '/shortcodes/embed-events.php';

/**
 * Set up our widget-based shortcodes: if PRO is not enabled those shortcodes won't actually
 * be registered with WordPress. If PRO *is* enabled, the PRO list widget supercedes the
 * core equivalent.
 */
new EventRocketWidgetShortcodes( 'TribeEventsMiniCalendarWidget', 'event_rocket_calendar' );
new EventRocketWidgetShortcodes( 'TribeEventsListWidget', 'event_rocket_list' );
new EventRocketWidgetShortcodes( 'TribeEventsAdvancedListWidget', 'event_rocket_list' );
new EventRocketWidgetShortcodes( 'TribeCountdownWidget', 'event_rocket_countdown' );
new EventRocketWidgetShortcodes( 'TribeVenueWidget', 'event_rocket_venue' );
