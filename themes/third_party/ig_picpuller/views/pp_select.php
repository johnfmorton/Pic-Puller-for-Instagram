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
        $this->protocol = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) ? 'https' : 'http';
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
	<title>Pic Puller photo picker</title>
	<!--
	Looking for some Javascript? 
	It's loaded from within the system/third_party/ig_picpuller 
	directly into the control panel.
	-->
	<link rel="stylesheet" href="<?php echo $third_party_theme_dir;?>themes/base/jquery.ui.all.css">
	<style type="text/css">
	#ig_pp.scroll-area {
			position: relative;
			overflow: hidden;
			width: 815px;
			height: 470px;
			padding: 5px;
	}

	#ig_pp .scroll-content {
		position: absolute;
		top:  10px;
		min-height: 460px;
	}

	#ig_pp .thumbnail {
		border: 1px solid #8195a0;
		width: 250px;
		height: 100px;
		padding: 5px;
		margin: 2px;
		float: left;
		border-radius: 3px;
		background-color: #ffffff;
	}

	#ig_pp .thumbnail img {
		margin-right: 5px;
	}

	#ig_pp .thumbnail img{
		float: left;
	}

	#ig_pp .thumbnail .selectbtn {
		-webkit-border-radius: 3px;
		-moz-border-radius: 3px;
		border-radius: 3px;
		-moz-background-clip: padding;
		-webkit-background-clip: padding-box;
		padding: 5px 8px;
		color: #f6f6f6;
		background-color: #444444;
		background-image: -webkit-gradient(linear, left top, left bottom, from(#444444), to(#111111));
		background-image: -webkit-linear-gradient(top, #444444, #111111);
		background-image:    -moz-linear-gradient(top, #444444, #111111);
		background-image:     -ms-linear-gradient(top, #444444, #111111);
		background-image:      -o-linear-gradient(top, #444444, #111111);
		background-image:         linear-gradient(to bottom, #444444, #111111);
	}

	#ig_pp .thumbnail .selectbtn:hover {
		text-decoration: none;
		

		color: #ffffff;
		background-color: #f72a58;
		background-image: -webkit-gradient(linear, left top, left bottom, from(#fc2e5a), to(#d60d4c));
		background-image: -webkit-linear-gradient(top, #fc2e5a, #d60d4c);
		background-image:    -moz-linear-gradient(top, #fc2e5a, #d60d4c);
		background-image:     -ms-linear-gradient(top, #fc2e5a, #d60d4c);
		background-image:      -o-linear-gradient(top, #fc2e5a, #d60d4c);
		background-image:         linear-gradient(to bottom, #fc2e5a, #d60d4c);
	}

	#ig_pp div.headline {
		color: #1e2a32;
		font-size: 12px;
		margin-bottom: 10px;
		height: 70px;
		overflow: hidden;
	}

	#ig_pp .scroll-bar {
		position: absolute;
		top: 3.5%;
		right: 5px;
		height: 93%;
	}
	</style>

	<script>
	$(function() {

		// prevent IE errors when using console
		if (typeof console == "undefined") {
				window.console = {
					log: function () {}
				};
		}

		$('.scroll-bar').slider({
			orientation: 'vertical',
			animate: true,
			step: 1,
			max: '100',
			min: '0',
			value: '100'
		});

		$( ".scroll-bar" ).bind( "slide", function(event, ui) {
			// Why is there an extra +10 ? It's for padding top and botton on the scroll area
			var maxDepth = $('.scroll-content').height() - $('.scroll-area').height() + 10 ;
			var newTop = -((.01 * Math.abs(ui.value-100)) * maxDepth);
			newTop +=10;
			$('.scroll-content').css('top', newTop+'px');
			
		});
		
		$('.scroll-content').delegate('.pp_morebt', 'click', function(event) {
			getPics( $(this).attr('href') );
			return false;
		});

		$('.scroll-content').delegate('.selectbtn', 'click', function(event) {
			$('#activePPtarget').val($(this).attr('data-id'));
			$.ppcolorbox.close();
			return false;
		});


		function getPics(urlToCall) {
			// if getPics isn't being used for pagination it will not be given a URL for the next set of images so just use the default URL.
			urlToCall = typeof urlToCall !== 'undefined' ? urlToCall : "<?=$third_party_theme_dir;?>pp_engine.php?access_token=<?=$access_token;?>";
			$.ajax({
				url: urlToCall,
				success: function(data, textStatus, jqXHR) {
					$('.getmore').remove();
					var prevTotal = $('.scroll-content .thumbnail').length;
					$('.scroll-content').append(data).each(function() {
						var newTotal = $('.scroll-content .thumbnail').length;
						var sliderValue = Math.floor(Math.abs((prevTotal/newTotal * 100) -100 ) );

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
						console.log('404: Could not load "pp_engine" from themes/third_party/ig_picpuller/views directory.');
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


<div id='ig_pp' class="scroll-area">

	<div class="scroll-content">
	</div>
	<div class="scroll-bar-wrap">
		<div class="scroll-bar"></div>
	</div>

</div><!-- End .scroll-area -->

</body>
</html>
