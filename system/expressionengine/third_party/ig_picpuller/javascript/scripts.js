$(function() {
	// Handler for .ready() called.

	// The Field Type's photo viewer won't work properly without JS, so it's hidden until the JS has loaded.

	$('.igbrowserbt').show();
	$('.igbrowserbt').ppcolorbox({width:"830px", height:"515px", title: 'Choose a photo from your Instagram feed',
				onOpen: function() {
						//$(this).parent().$('input').attr('id', 'activePPtarget');
						$(this).parent().find('input').attr('id', 'activePPtarget');
						//console.log($(this).parent().find('input'));
					},
				onCleanup: function() {
						$('#activePPtarget').removeAttr('id');
					} });

	if (typeof Matrix == 'function'){
		Matrix.bind('ig_picpuller', 'display', function(cell){
			// Upon the display of each new PP browser row within a Matrix field, this JS is fired
			$('.igbrowserbtmatrix').show();
			$('.igbrowserbtmatrix').ppcolorbox({
					width:"830px",
					height:"515px",
					title: 'Choose a photo from your Instagram feed',
					onOpen: function() {
							//$(this).prev().attr('id', 'activePPtarget');
							$(this).parent().find('input').attr('id', 'activePPtarget');
						},
					onCleanup: function() {
							$('#activePPtarget').removeAttr('id');
						}
					});
		});
	}
});

