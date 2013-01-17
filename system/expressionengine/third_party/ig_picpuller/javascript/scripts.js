$(function() {
	// Handler for .ready() called.

	// The Field Type's photo viewer won't work properly without JS, so it's hidden until the JS has loaded.

	// If using the Instagram Search Browser turn it on (it won't be generated if it's not supposed to be here)
	$('.igbrowserbt').show();

	$('.igbrowserbt').ppcolorbox({width:"830px", height:"525px", title: 'Choose a photo from your Instagram feed',
				onOpen: function() {
						$(this).parent().find('input').attr('id', 'activePPtarget');
					},
				onCleanup: function() {
						// check to see if the field actually had an ID input, if so
						// we turn on the search button
						if (checkForTextValue($('#activePPtarget')) ){
							var myLookupBt = $(this).parent().find($('.ig_preview_bt'));
							myLookupBt.trigger('click');
						}
						$('#activePPtarget').removeAttr('id');
					} });

	// If using the Instagram Search Browser turn it on (it won't be generated if it's not supposed to be here).

	$('.igsearchbt').show();

	$('.igsearchbt').ppcolorbox({width:"830px", height:"525px", title: '<input type="text" id="ig_search_field" name="ig_tag" placeholder="Search for a single tag"><input type="submit" id="ig_search_button" value="Search">',
				onOpen: function() {
						$(this).parent().find('input').attr('id', 'activePPtarget');
					},
				onComplete: function() {
					$("#ig_search_button").attr("disabled", true);

					$("#ig_search_field").keyup(function(event) {
						if($("#ig_search_field").val() !== '') {
							$("#ig_search_button").attr("disabled", false);
						} else {
							$("#ig_search_button").attr("disabled", true);
						}
					});
				},

				onCleanup: function() {
						// check to see if the field actually had an ID input, if so
						// we turn on the search button
						if (checkForTextValue($('#activePPtarget')) ){
							var myLookupBt = $(this).parent().find($('.ig_preview_bt'));
							myLookupBt.trigger('click');
						}
						$('#activePPtarget').removeAttr('id');
					} });

	/*
	Make the Preview button work
	 */
	
	//
	// Since the click event might be triggered in the $('.ig_media_id_field').each loop,
	// I need to define the listener before doing that each loop
	//
	function addClickEventToPPPreview() {
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
					console.log('Data received from Instagram.');
					console.log('code: ' + data.code);
					ig_pp_loader_gr.addClass('hidden');
					if (data.code === 200 ){
						theImage.removeClass('hidden');
						theImage.attr("src",data.imageURL);
						//theHeadline.text(data.imageTitle);
						theHeadline.html(data.imageTitle + " <em>by " + data.theUsername + "</em>");
					} else {
						theImage.addClass('hidden');
						theHeadline.html("<strong>"+data.error_type+": </strong>" + data.error_message);
					}
				},
				error: function(data) {
					console.log('ERROR');
					console.log(data);
				}
				});

			e.preventDefault();
		});
	}

	addClickEventToPPPreview();

	// preview button is hidden until there is something to look up
	// first check all PP fields to see if they contain something...
	$('.ig_media_id_field').each(function(e){
		//console.log('checking to see if I need to turn on that magnifying glass');
		if (checkForTextValue($(this)) ){
			console.log('There was a value in the checked PP field, so trigger an automated lookup.');
			var myLookupBt = $(this).parent().find($('.ig_preview_bt'));
			myLookupBt.trigger('click');
		} else {
			console.log('No need for an automated look up.');
		};
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
			return true;
		} else {
			myLookupBt.addClass('hidden');
			return false;
		}
	}

	if (typeof Matrix == 'function'){
		console.log("PP detected a Matrix field.");

		// for compatibility with Better Workflow
		// check for the presence of Bwf, and if present
		// readd the click event to the PP preview button
		// since it disappears after closing a preview window
		// when Matix fields are used.
		if (typeof Bwf) {
			Bwf.bind('my_field', 'previewClose', function(){
				console.log("BWF is present & the preview window was just closed.");
				addClickEventToPPPreview();

			  $('.ig_media_id_field').each(function(e){
				//console.log('checking to see if I need to turn on that magnifying glass');
				if (checkForTextValue($(this)) ){
					console.log('There was a value in the checked PP field, so trigger an automated lookup.');
					var myLookupBt = $(this).parent().find($('.ig_preview_bt'));
					myLookupBt.trigger('click');
				} else {
					console.log('No need for an automated look up.');
				};
			});
			});
		}

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
						// Look up whatever image might have chosen
						if (checkForTextValue($('#activePPtarget')) ){
							var myLookupBt = $(this).parent().find($('.ig_preview_bt'));
							myLookupBt.trigger('click');
						}
						// then remove the target ID from the text input box
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
							// Look up whatever image might have chosen
							if (checkForTextValue($('#activePPtarget')) ){
								var myLookupBt = $(this).parent().find($('.ig_preview_bt'));
								myLookupBt.trigger('click');
							}
							// then remove the target ID from the text input box
							$('#activePPtarget').removeAttr('id');
						}
					});
		});
	}
});

