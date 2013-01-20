var PicPullerIG;

(function($) {
	// Handler for .ready() called.

	var callbacks = {
		afterThumbnailGeneration: {}
	};

	PicPullerIG = {
		init: function() {
			console.log('init fired for PicPullerIG');
			// The Field Type's photo viewer won't work properly without JS, so it's hidden until the JS has loaded. Attempt to show both the User Stream Browser and the Search Browser. If they aren't present based on the preferences, they won't be able to be shown.
			// $('.igbrowserbt').show();
			// $('.igsearchbt').show();
			$('.igbrowserbt').show();
			$('.igsearchbt').show();

			// Attache ColorBox listeners to both buttons
			$('.igbrowserbt').ppcolorbox({width:"830px", height:"525px", title: 'Choose a photo from your Instagram feed',
				onOpen: function() {
						$(this).parent().find('input').attr('id', 'activePPtarget');
					},
				onCleanup: function() {
						// check to see if the field actually had an ID input, if so
						// we turn on the search button
						if (checkForValueinPPfield($('#activePPtarget')) ){
							var myLookupBt = $(this).parent().find($('.ig_preview_bt'));
							myLookupBt.trigger('click');
						}
						$('#activePPtarget').removeAttr('id');
					}
			});

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
						if (checkForValueinPPfield($('#activePPtarget')) ){
							var myLookupBt = $(this).parent().find($('.ig_preview_bt'));
							myLookupBt.trigger('click');
						}
						$('#activePPtarget').removeAttr('id');
					}
			});

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
					var theSource = $(this).parent().find($('.ig_media_id_field'));
					console.log("theSource");
					console.log(theSource);
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
								// TO DO: make this work!
								PicPullerIG.callback('afterThumbnailGeneration', theSource);
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
				if (checkForValueinPPfield($(this)) ){
					console.log('There was a value in the checked PP field, so trigger an automated lookup.');
					var myLookupBt = $(this).parent().find($('.ig_preview_bt'));
					myLookupBt.trigger('click');
				} else {
					console.log('No need for an automated look up.');
				}
			});
			// and watch for someone entering a media ID manually
			$('.ig_media_id_field').keyup(function(e){
				checkForValueinPPfield($(this));
			});

			function checkForValueinPPfield(theTarget) {
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
					Bwf.bind('ig_picpuller', 'previewClose', function(){
						console.log("BWF is present & the preview window was just closed.");
						addClickEventToPPPreview();

						$('.ig_media_id_field').each(function(e){
							//console.log('checking to see if I need to turn on that magnifying glass');
							if (checkForValueinPPfield($(this)) ){
								console.log('There was a value in the checked PP field, so trigger an automated lookup.');
								var myLookupBt = $(this).parent().find($('.ig_preview_bt'));
								myLookupBt.trigger('click');
							} else {
								console.log('No need for an automated look up.');
							}
						});
					});
				}

				// Bind the PicPuller preparation events to the Matrix display event
				Matrix.bind('ig_picpuller', 'display', function(cell){
					// Upon the display of each new PP browser row within a Matrix field, this JS is fired
					// Show the matrix PP buttons
					$('.igbrowserbtmatrix').show();
					$('.igsearchbtmatrix').show();
					// attach the ColorBox to them
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
							if (checkForValueinPPfield($('#activePPtarget')) ){
								var myLookupBt = $(this).parent().find($('.ig_preview_bt'));
								myLookupBt.trigger('click');
							}
							// then remove the target ID from the text input box
							$('#activePPtarget').removeAttr('id');
						}
					});

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
									if($("#ig_search_field").val() !== '') {
										$("#ig_search_button").attr("disabled", false);
									} else {
										$("#ig_search_button").attr("disabled", true);
									}
								});
							},
							onCleanup: function() {
									// Look up whatever image might have chosen
									if (checkForValueinPPfield($('#activePPtarget')) ){
										var myLookupBt = $(this).parent().find($('.ig_preview_bt'));
										myLookupBt.trigger('click');
									}
									// then remove the target ID from the text input box
									$('#activePPtarget').removeAttr('id');
							}
						});
				}); // end Matrix.bind
			}
		}, // end init
		bind : function(myUniqueIdentifier, event, callback) {
			console.log('binding');
			if (typeof callbacks[event] == 'undefined') return;
			callbacks[event][myUniqueIdentifier] = callback;
			console.log('bound it!');
		},
		unbind : function(myUniqueIdentifier, event) {
			
			// is this a legit event?
			if (typeof callbacks[event] == 'undefined') return;

			// is the celltype even listening?
			if (typeof callbacks[event][myUniqueIdentifier] == 'undefined') return;

			delete callbacks[event][myUniqueIdentifier];
			console.log('unbinding ' + myUniqueIdentifier);
		},
		callback: function(callback, theSource){
			// theSource is the currentTarget field that generated the event, ie the field with the media_id
			for (var myIdentifier in callbacks[callback]) {
				if (typeof callbacks[callback][myIdentifier] == 'function') {
					console.log(myIdentifier + ' has a function.');
					callbacks[callback][myIdentifier].call(theSource, callbacks[callback]);
				}
			}
		}
	}.init();

	// Attach and event stored with the uniqueID of myUniqueIdentifier1234 which allows its removal if needed.
	PicPullerIG.bind('myUniqueIdentifier1234', 'afterThumbnailGeneration', function() { 
		// 'this' will hold a reference to the field that triggered the image generation
		// Just to show it works, let's turn the background color of the field to green
		this.css('backgroundColor', '#66ff33');
	});

	// The event stored at myUniqueIdentifier1234 is now removed
	PicPullerIG.unbind('myUniqueIdentifier1234', 'afterThumbnailGeneration');

})(jQuery);

