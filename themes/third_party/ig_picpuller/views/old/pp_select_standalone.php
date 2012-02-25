<?php 
class VirtualDirectory
{
    var $protocol;
    var $site;
    var $thisfile;
    var $real_directories;
    var $num_of_real_directories;
    var $virtual_directories = array();
    var $num_of_virtual_directories = array();
    var $baseurl;
    var $thisurl;
    function VirtualDirectory()
    {
        $this->protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
        $this->site = $this->protocol . '://' . $_SERVER['HTTP_HOST'];
        $this->thisfile = basename($_SERVER['SCRIPT_FILENAME']);
        $this->real_directories = $this->cleanUp(explode("/", str_replace($this->thisfile, "", $_SERVER['PHP_SELF'])));
        $this->num_of_real_directories = count($this->real_directories);
        $this->virtual_directories = array_diff($this->cleanUp(explode("/", str_replace($this->thisfile, "", $_SERVER['REQUEST_URI']))),$this->real_directories);
        $this->num_of_virtual_directories = count($this->virtual_directories);
        $this->baseurl = $this->site . "/" . implode("/", $this->real_directories) . "/";
        $this->thisurl = $this->baseurl . implode("/", $this->virtual_directories) . "/";
    }
    function cleanUp($array)
    {
        $cleaned_array = array();
        foreach($array as $key => $value)
        {
            $qpos = strpos($value, "?");
            if($qpos !== false)
            {
                break;
            }
            if($key != "" && $value != "")
            {
                $cleaned_array[] = $value;
            }
        }
        return $cleaned_array;
    }
}

$virdir = new VirtualDirectory();

/*echo "<pre>";
print_r($virdir);
echo "</pre>";
*/

$third_party_theme_dir = $virdir->baseurl;
$access_token = $_GET["access_token"];
if(!isset($access_token)){
	exit('No direct script access allowed');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>jQuery UI Slider - Slider scrollbar</title>
	<link rel="stylesheet" href="<?=$third_party_theme_dir;?>themes/base/jquery.ui.all.css">
	<link rel="stylesheet" href="<?=$third_party_theme_dir;?>style.css">

<!-- 
jQuery is here for debugging purposes only.
-->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="<?=$third_party_theme_dir;?>ui/jquery.ui.core.js"></script>
<script src="<?=$third_party_theme_dir;?>ui/jquery.ui.widget.js"></script>
<script src="<?=$third_party_theme_dir;?>ui/jquery.ui.mouse.js"></script>
<script src="<?=$third_party_theme_dir;?>ui/jquery.ui.slider.js"></script>
<!-- 
jQuery is here for debugging purposes only.
-->

	<script>
	$(function() {

		// prevent IE errors when using console
		if (typeof console == "undefined") {
				window.console = {
					log: function () {}
				};
		}
		// readout is to be removed after debugging
		var readout = $('#readout');

		$('.scroll-bar').slider({
			orientation: 'vertical',
			animate: true,
			step: 1,
			max: '100',
			min: '0',
			value: '100'
		});

		$( ".scroll-bar" ).bind( "slide", function(event, ui) {
			// Why is there an extra +5 and +10 ? It's for padding top and botton on the scroll area
			var maxDepth = $('.scroll-content').height() - $('.scroll-area').height() + 10 ;
			var newTop = -((.01 * Math.abs(ui.value-100)) * maxDepth);
			newTop +=10;
			$('.scroll-content').css('top', newTop+'px');
			readout.html('<span style="color:gray;">newTop: ' + -newTop + '</span>');
			
		});
		
		/*
		$( ".scroll-bar" ).bind( "slidechange", function(event, ui) {
			readout.html('<span style="color:green;">' + Math.abs(ui.value-100) + '</span>');
		});
		*/

		$('.scroll-content').delegate('.pp_morebt', 'click', function(event) {
		//$('.pp_morebt').bind('click', function(event) {
			console.log('getting more');
			return false;
			//getPics();
		});

		function getPics(urlToCall) {
			// if getPics isn't being used for pagination it will not be given a URL for the next set of images so just use the default URL.
			urlToCall = typeof urlToCall !== 'undefined' ? urlToCall : "<?=$third_party_theme_dir;?>pp_engine.php?access_token=<?=$access_token;?>";
			//console.log("<?=$third_party_theme_dir;?>pp_engine.php?access_token=<?=$access_token;?>");

			$.ajax({
				url: urlToCall,
				success: function(data, textStatus, jqXHR) {
					$('.getmore').remove();
					//var getMoreHolder = '<div class="thumbnail getmore"><div class="headline">Wait, there are more...</div><a href="#" class="pp_morebt">Load More Images &gt;</a></div>';
					var prevTotal = $('.scroll-content .thumbnail').length;
					//console.log('success: ' + data);
					$('.scroll-content').append(data).each(function() {
						var newTotal = $('.scroll-content .thumbnail').length;
						var sliderValue = Math.ceil(Math.abs((prevTotal/newTotal * 100) -100 ) );
						console.log('there were ' + prevTotal + ' and now there are ' + newTotal + ' items. So that is ' +  sliderValue);

						// using 'each' to allow a callback to reset slider value
						$( ".scroll-bar" ).slider({ value: sliderValue });

						var maxDepth = $('.scroll-content').height() - $('.scroll-area').height() + 10 ;
						var newTop = -((.01 * Math.abs(sliderValue-100)) * maxDepth);
						newTop +=10;
						$('.scroll-content').css('top', newTop+'px');
					});
					
				},
				statusCode: {
					404: function() {
						console.log('404: Could not load "pp_select" from themes/third_party/ig_picpuller/views directory.');
					}
				}
			})
		}

		//////////////////////////////////////////
		// Let's get this party started. Right? //
		//////////////////////////////////////////

		getPics();
	});
	</script>
</head>
<body>


<div class="scroll-area">
	<div class="scroll-content">
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
	</div>
	<div class="scroll-bar-wrap">
		<div class="scroll-bar"></div>
	</div>
<div id='readout'></div>

</div><!-- End demo -->

</body>
</html>
