<?php
require_once EVENTROCKET_INC . '/data-requests/server-generic.php';
require_once EVENTROCKET_INC . '/data-requests/server-csv.php';
require_once EVENTROCKET_INC . '/data-requests/server-ical.php';
require_once EVENTROCKET_INC . '/data-requests/shortcodes.php';

new EventRocket_CSVRequests;
new EventRocket_iCalRequests;