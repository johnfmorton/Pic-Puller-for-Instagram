$(function() {
	// Handler for .ready() called.

	// The Field Type's photo viewer won't work properly without JS, so it's hidden until the JS has loaded.

	$('.igbrowserbt').show();

	$('.igbrowserbt').ppcolorbox({width:"830px", height:"525px", title: 'Choose a photo from your Instagram feed',
				onOpen: function() {
						$(this).parent().find('input').attr('id', 'activePPtarget');
					},
				onCleanup: function() {
						// check to see if the field actually had an ID input, if so
						// we turn on the search button
						checkForTextValue($('#activePPtarget'));
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
						// check to see if the field actually had an ID input, if so
						// we turn on the search button
						checkForTextValue($('#activePPtarget'));
						$('#activePPtarget').removeAttr('id');
					} });

	/*
	Make the Preview button work
	 */
	
	// preview button is hidden until there is something to look up
	// first check all PP fields to see if they contain something...
	$('.ig_media_id_field').each(function(e){
		checkForTextValue($(this));
	});
	// and watch for someone entering a media ID manually
	$('.ig_media_id_field').keyup(function(e){
		checkForTextValue($(this));
	});

	function checkForTextValue(theTarget) {
		var myValue= theTarget.val();
		var myLookupBt = theTarget.parent().find($('.ig_preview_bt'));
		if(myValue !== '') {
			myLookupBt.removeClass('hidden');
		} else {
			myLookupBt.addClass('hidden');
		}
	}

	$('.ig_preview_bt').on('click', function(e) {
		var myPreviewFrame = $(this).parent().find($('.ig_preview_frame'));
		myPreviewFrame.slideDown();
		var media_id = $(this).parent().find($('.ig_media_id_field')).val();
		var theURL = $(this).attr('href')+media_id;
		var theImage = $(this).parent().find($('.theImage'));
		var theHeadline = $(this).parent().find($('.theHeadline'));
		var ig_pp_loader_gr = $(this).parent().find($('.ig_pp_loader_gr'));

		$.ajax({
			url: theURL,
			dataType: 'json',
			success: function(data) {
				console.log('SUCCESS');
				console.log(data);
				ig_pp_loader_gr.addClass('hidden');
				theImage.attr("src",data.imageURL);
				theHeadline.text(data.imageTitle);

			},
			error: function(data) {
				console.log('ERROR');
				console.log(data);
			}
			});

		e.preventDefault();
	});


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

