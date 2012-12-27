$(function() {
	// Handler for .ready() called.

	// The Field Type's photo viewer won't work properly without JS, so it's hidden until the JS has loaded.

	$('.igbrowserbt').show();
	$('.igbrowserbt').ppcolorbox({width:"830px", height:"525px", title: 'Choose a photo from your Instagram feed',
				onOpen: function() {
						$(this).parent().find('input').attr('id', 'activePPtarget');
					},
				onCleanup: function() {
						$('#activePPtarget').removeAttr('id');
					} });

	// If using the Instagram Search Browser
	
	$('.igsearchbt').show();
	$('.igsearchbt').ppcolorbox({width:"830px", height:"525px", title: '<input type="text" id="ig_search_field" name="ig_tag" placeholder="Search for a single tag"><input type="submit" id="ig_search_button" value="Search">',
				onOpen: function() {
						$(this).parent().find('input').attr('id', 'activePPtarget');
					},
				onComplete: function() {
					$("#ig_search_button").attr("disabled", true);

					$("#ig_search_field").keyup(function(event) {
						//console.log('testing search field : ' + $("#ig_search_field").val());
						if($("#ig_search_field").val() != '') {
							$("#ig_search_button").attr("disabled", false);
						} else {
							$("#ig_search_button").attr("disabled", true);
						}
					});
				},

				onCleanup: function() {
						$('#activePPtarget').removeAttr('id');
					} });


	if (typeof Matrix == 'function'){
		Matrix.bind('ig_picpuller', 'display', function(cell){
			// Upon the display of each new PP browser row within a Matrix field, this JS is fired
			$('.igbrowserbtmatrix').show();
			//igsearchbtmatrix
			$('.igbrowserbtmatrix').ppcolorbox({
					width:"830px",
					height:"525px",
					title: 'Choose a photo from your Instagram feed',
					onOpen: function() {
							//$(this).prev().attr('id', 'activePPtarget');
							$(this).parent().find('input').attr('id', 'activePPtarget');
						},
					onCleanup: function() {
							$('#activePPtarget').removeAttr('id');
						}
					});

			$('.igsearchbtmatrix').show();

			$('.igsearchbtmatrix').ppcolorbox({
					width:"830px",
					height:"525px",
					title: '<input type="text" id="ig_search_field" name="ig_tag" placeholder="Search for a single tag"><input type="submit" id="ig_search_button" value="Search">',
					onOpen: function() {
							//$(this).prev().attr('id', 'activePPtarget');
							$(this).parent().find('input').attr('id', 'activePPtarget');
						},
					onComplete: function() {
					$("#ig_search_button").attr("disabled", true);

					$("#ig_search_field").keyup(function(event) {
						console.log('testing search field : ' + $("#ig_search_field").val());
						if($("#ig_search_field").val() != '') {
							$("#ig_search_button").attr("disabled", false);
						} else {
							$("#ig_search_button").attr("disabled", true);
						}
					});
				},
					onCleanup: function() {
							$('#activePPtarget').removeAttr('id');
						}
					});
		});
	}
});

