<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Pic Puller IG Browser</title>
	<style>
	#photos > div.demo { padding: 10px !important; }
	.scroll-pane { overflow: auto; width: 99%; float:left; }
	.scroll-content { width: 2440px; float: left; }
	.scroll-content-item { width: 100px; height: 100px; float: left; margin: 10px; font-size: 3em; line-height: 96px; text-align: center; }
	* html .scroll-content-item { display: inline; } /* IE6 float double margin bug */
	.scroll-bar-wrap { clear: left; padding: 0 4px 0 2px; margin: 0 -1px -1px -1px; }
	.scroll-bar-wrap .ui-slider { background: none; border:0; height: 2em; margin: 0 auto;  }
	.scroll-bar-wrap .ui-handle-helper-parent { position: relative; width: 100%; height: 100%; margin: 0 auto; }
	.scroll-bar-wrap .ui-slider-handle { top:.2em; height: 1.5em; }
	.scroll-bar-wrap .ui-slider-handle .ui-icon { margin: -8px auto 0; position: relative; top: 50%; }
	</style>
	
	<link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.8.17.custom.css">
	<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.17.custom.min.js"></script>
</head>
<body>


<h1>Your photos from Instagram.</h1>
<p>The oAuth is <?php echo $_GET["oauth"]; ?></p>
<div class="scroll-pane ui-widget ui-widget-header ui-corner-all">
	<div class="scroll-content">
<?php 
$oauthkey = $_GET["oauth"];
	
	$jsonurl = "https://api.instagram.com/v1/users/self/media/recent/?access_token=".'1500897.56bd320.0a2f89a47626499ba9b6dfdf554a02c8&count=30';

	//$jsonurl = "https://api.instagram.com/v1/users/self/media/recent/?access_token=".$oauthkey."&count=30";

	// the @ symbol will make this fail silently, so we'll need to check that $json actually is parsable and show alternate images instead
	
	$json = @file_get_contents($jsonurl,0,null,null);
	
	$json_output = json_decode($json);
	// does the $json_output->data array actually exist? ie, Was there an error getting the data from Instagram?		
	if (is_array($json_output->data)) {
		$theCount = 0;
		$next_max_id = $json_output->pagination->next_max_id;
		echo "<div id='photos'>";

		foreach ( $json_output->data as $images )
		{
			$theCount ++;
			$theImage = $images->images->thumbnail->url;
			$theId = $images->id;
		    //echo "{$images->images}\n";
			//echo "<pre>";
			//echo $images->images->low_resolution->url;
			//print_r($images->images->low_resolution->url);
			//print_r($images);
			//echo "</pre>";
			echo "<img src='$theImage' alt='$theId' width='103' height='103' id='photo$theCount'>";
			if ($theCount >= 30) {
				echo "Next: $next_max_id";
				break;
			}
		}
		echo "</div>";
	} else {
		echo "Alternate content will go here.";
	}

?>
</div>
<div class="scroll-bar-wrap ui-widget-content ui-corner-bottom">
		<div class="scroll-bar"></div>
	</div>
</div>
<script>
	$(function() {
		//scrollpane parts
		var scrollPane = $( ".scroll-pane" ),
			scrollContent = $( ".scroll-content" );
		
		//build slider
		var scrollbar = $( ".scroll-bar" ).slider({
			slide: function( event, ui ) {
				if ( scrollContent.width() > scrollPane.width() ) {
					scrollContent.css( "margin-left", Math.round(
						ui.value / 100 * ( scrollPane.width() - scrollContent.width() )
					) + "px" );
				} else {
					scrollContent.css( "margin-left", 0 );
				}
			}
		});
		
		//append icon to handle
		var handleHelper = scrollbar.find( ".ui-slider-handle" )
		.mousedown(function() {
			scrollbar.width( handleHelper.width() );
		})
		.mouseup(function() {
			scrollbar.width( "100%" );
		})
		.append( "<span class='ui-icon ui-icon-grip-dotted-vertical'></span>" )
		.wrap( "<div class='ui-handle-helper-parent'></div>" ).parent();
		
		//change overflow to hidden now that slider handles the scrolling
		scrollPane.css( "overflow", "hidden" );
		
		//size scrollbar and handle proportionally to scroll distance
		function sizeScrollbar() {
			var remainder = scrollContent.width() - scrollPane.width();
			var proportion = remainder / scrollContent.width();
			var handleSize = scrollPane.width() - ( proportion * scrollPane.width() );
			scrollbar.find( ".ui-slider-handle" ).css({
				width: handleSize,
				"margin-left": -handleSize / 2
			});
			handleHelper.width( "" ).width( scrollbar.width() - handleSize );
		}
		
		//reset slider value based on scroll content position
		function resetValue() {
			var remainder = scrollPane.width() - scrollContent.width();
			var leftVal = scrollContent.css( "margin-left" ) === "auto" ? 0 :
				parseInt( scrollContent.css( "margin-left" ) );
			var percentage = Math.round( leftVal / remainder * 100 );
			scrollbar.slider( "value", percentage );
		}
		
		//if the slider is 100% and window gets larger, reveal content
		function reflowContent() {
				var showing = scrollContent.width() + parseInt( scrollContent.css( "margin-left" ), 10 );
				var gap = scrollPane.width() - showing;
				if ( gap > 0 ) {
					scrollContent.css( "margin-left", parseInt( scrollContent.css( "margin-left" ), 10 ) + gap );
				}
		}
		
		//change handle position on window resize
		$( window ).resize(function() {
			resetValue();
			sizeScrollbar();
			reflowContent();
		});
		//init scrollbar size
		setTimeout( sizeScrollbar, 10 );//safari wants a timeout
	});
	</script>
</body>
</html>