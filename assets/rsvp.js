if ( "function" === typeof jQuery ) jQuery( document ).ready( function( $ ) {
	var attendee_count     = $( ".eventrocket_rsvp .eventrocket_rsvp_attending" ),
		non_attendee_count = $( ".eventrocket_rsvp .eventrocket_rsvp_not_attending"),
		loaded             = false,
		attendee_link,
		non_attendee_link,
		dialog,
		attendee_tab,
		non_attendee_tab,
		email_form,
		current_list_type;

	function show( type ) {
		current_list_type = type;
		display_dialog();
		load();
		set_list();
	}

	/**
	 * Create the dialog container if it doesn't already exist and make it
	 * display.
	 */
	function display_dialog() {
		if ( "undefined" === typeof dialog ) {
			$( "body" ).append( '<div id="eventrocket_rsvp_dialog" title="' + eventrocket_rsvp.title + '">'
				+ eventrocket_rsvp.loading_msg
				+ '</div>' );

			dialog = $( "#eventrocket_rsvp_dialog" );
		}

		dialog.dialog( {
			modal:     true,
			minWidth:  600,
			minHeight: 500,
			show:      true
		} );
	}

	/**
	 * Display the specified type of attendees (attendees or non attendees) when
	 * the modal list shows.
	 */
	function set_list() {
		switch ( current_list_type ) {
			case "attendees":
				non_attendee_tab.hide();
				attendee_tab.show();
				break;
			case "non-attendees":
				attendee_tab.hide();
				non_attendee_tab.show();
				break;
		}
	}

	/**
	 * Handles attendee emailing form
	 *
	 * @param event
	 */
	function send_email( e ){
		e.preventDefault();

		var msg = {
			"action":   "rsvp_email",
			"check":    eventrocket_rsvp.check,
			"event_id": eventrocket_rsvp.event_id,
			"subject":  $("#eventrocket_subject").val(),
			"body":		$("#eventrocket_body").val()
		};

		$.post( ajaxurl, msg, function(){
			alert( "Email sent!" );
		}, "json" );
	}

	/**
	 * Renders the attendee list.
	 *
	 * Soon after the user requests to see the attendee list, this function should receive
	 * the data containing the attendee information, which it uses to form the actual list
	 * (and email form).
	 *
	 * @param data
	 */
	function render( data ) {
		if ( "success" !== data.msg ) return;
		loaded = true;

		dialog.html( '<div id="rsvp-attendee-tab">'
			+ "<h4>" + eventrocket_rsvp.attending_title + "</h4>"
			+ to_list( data.attendees )
			+ '<form id="email-attendees">'
			+ '<h4>' + eventrocket_rsvp.email_title + '</h4>'
			+ '<input type="text" style="width: 95%" placeholder="' + eventrocket_rsvp.email_subject + '" id="eventrocket_subject" />'
			+ '<textarea  style="width: 95%; height: 8em;" placeholder="' + eventrocket_rsvp.email_body + '" id="eventrocket_body"></textarea>'
			+ '<input type="submit" id="eventrocket_email_send" value="' + eventrocket_rsvp.email_send + '" class="button-secondary" />'
			+ '</form>'
			+ '</div>'
			+ '<div id="rsvp-non-attendee-tab">'
			+ "<h4>" + eventrocket_rsvp.not_attending_title + "</h4>"
			+ to_list( data.non_attendees )
			+ '</div>'
		);

		attendee_tab     = $( "#rsvp-attendee-tab" );
		non_attendee_tab = $( "#rsvp-non-attendee-tab" );
		email_form 		 = $( "#email-attendees" );

		email_form.submit( send_email );
		set_list();
	}

	function to_list( list ) {
		var str = "";

		for ( i = 0; i < list.length; i++ )
			str += "<li>" + list[i] + "</li>";

		if ( "" === str ) str = eventrocket_rsvp.none_found_text;
		return "<ul>" + str + "</ul>";
	}

	/**
	 * Tries to load a list of attendee or non-attendee data.
	 *
	 * @param type
	 */
	function load( type ) {
		if ( loaded ) return;

		var msg = {
			"action":   "rsvp_attendance",
			"check":    eventrocket_rsvp.check,
			"event_id": eventrocket_rsvp.event_id,
			"type":     type
		};

		$.post( ajaxurl, msg, render, "json" );
	}

	// Look for the attendee count and convert to a link
	if ( attendee_count.length ) {
		inner = attendee_count.html( '<a href="#rsvp-attendees">' + attendee_count.html() + '</a>' );
		attendee_link = attendee_count.find( "a" );
	}

	// Look for the non-attendee count and covert to a link
	if ( non_attendee_count.length ) {
		inner = non_attendee_count.html( '<a href="#rsvp-non-attendees">' + non_attendee_count.html() + '</a>' );
		non_attendee_link = non_attendee_count.find( "a" );
	}

	// Hook up a click action for the attendee link
	if ( "undefined" !== typeof attendee_link && attendee_link.length ) {
		attendee_link.click( function() { show( "attendees" ) } );
	}

	// Hook up a click action for the non-attendee link
	if ( "undefined" !== typeof non_attendee_link && non_attendee_link.length ) {
		non_attendee_link.click( function() { show( "non-attendees" ) } );
	}
} );
