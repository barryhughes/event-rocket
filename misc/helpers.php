<?php
require_once EVENTROCKET_INC . '/misc/404-helper.php';
require_once EVENTROCKET_INC . '/misc/clean-up.php';
require_once EVENTROCKET_INC . '/misc/front-page-events.php';
require_once EVENTROCKET_INC . '/misc/venue-positioning.php';

/**
 * Compatibility layer to reduce friction while supporting TEC/ECP versions
 * old and new.
 *
 * This is born out of a large scale refactoring exercise which saw traditional
 * TEC classnames like TribeEvents deprecated; however continuing to reference
 * them - while allowed by newer TEC builds - results in unnecessary noise while
 * debugging due to deprecation notices.
 */
class EventRocket_TEC
{
	public static $event_type     = 'tribe_events';
	public static $venue_type     = 'tribe_venue';
	public static $organizer_type = 'tribe_organizer';
	public static $category       = 'tribe_events_cat';
}