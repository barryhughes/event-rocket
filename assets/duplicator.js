function eventrocket_duplicator( $ ) {
	var template;
	var datepicker;
	var datetime;
	var title;
	var dup_link;


	function on_click() {
		dup_link = $( this );
		prep_template();
		show_dialog();
		init_fields();
		return false;
	}

	function prep_template() {
		if ( "object" === typeof template ) return;
		$( "body" ).append( eventrocket_dup.dialog_template );

		template   = $( "#eventrocket_duplication_dialog" );
		datepicker = template.find( "#duplicate_datetimepicker" );
		datetime   = template.find( "#duplicate_datetime" );
		title      = template.find( "#duplicate_title" );

		// Capture cancel/close button clicks
		$( "button#cancel_duplication" ).click( on_close );
		$( "#eventrocket_duplication_dialog .close-btn" ).click( on_close );
	}

	function show_dialog() {
        template.show();
    }

    function init_fields() {
	    // Setup the datepicker
	    var date =  new Date( dup_link.data( "date" ) )

        datepicker.datetimepicker( {
	        "defaultDate":      date,
	        "dateFormat":       eventrocket_dup.date_format,
	        "timeFormat":       eventrocket_dup.time_format,
	        "altField":         "#duplicate_datetime",
	        "altFieldTimeOnly": false,
	        "altFormat":        "yy-mm-dd",
	        "altTimeFormat":    "HH:mm",
	        "altSeparator":     " "
        } );

	    datepicker.datetimepicker( "setDate", date );

	    // Set the default title
	    title.val( dup_link.data( "title" ) );

	    // Set the form action
        template.find( "form" ).attr( "action", dup_link.attr( "href" ) );
    }

	function on_close( event ) {
		event.stopImmediatePropagation();
		template.fadeOut( "fast" );
		return false;
	}

	// Capture clicks on "duplicate event" links
	$( "a.eventrocket_duplicate" ).click( on_click );
}

// Ensure our prereqs are met before rolling into action
( function() {
	if ( "function" !== typeof jQuery ||
		 "object"   !== typeof jQuery.ui ||
		 "object"   !== typeof jQuery.ui.datepicker ||
		 "object"   !== typeof eventrocket_dup ) return;

	jQuery( document ).ready( eventrocket_duplicator );
} )();
