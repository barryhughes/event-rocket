if ( "undefined" !== typeof jQuery ) jQuery( document ).ready( function($) {
	var red_button = $( "#do_cleanup" );
	var safety_catch = $( "#cleanup_safety input[type='checkbox']" );

	// Hide the cleanup button by default
	red_button.hide();

	// Ensure the acknowledgement checkbox is checked before showing the button
	safety_catch.change( function() {
		if ( "checked" === safety_catch.attr( "checked" ) ) red_button.slideDown();
		else red_button.slideUp();
	} );

	// Support long cleanup operations
	if ( 1 === $( "input#keep_working" ).length )
		window.location = $( "input#keep_working" ).val();
} );