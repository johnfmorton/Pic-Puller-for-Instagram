$(function() {
	 // Handler for .ready() called.

	 // The Field Type's photo viewer won't work properly without JS, so it's hidden until the JS has loaded.

	$('.igbrowserbt').show();
 	$('.igbrowserbt').colorbox({width:"830px", height:"515px", title: 'Choose a photo from your Instagram feed'});
});

