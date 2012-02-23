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
//echo $virdir->thisurl;
/*echo "<pre>";
print_r($virdir);
echo "</pre>";
*/

$third_party_theme_dir = $virdir->baseurl;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>jQuery UI Slider - Slider scrollbar</title>
	<link rel="stylesheet" href="<?=$third_party_theme_dir;?>themes/base/jquery.ui.all.css">
	<!-- link rel="stylesheet" href="http://instashowee.com/themes/third_party/ig_picpuller/views/pp.css" -->
	<link rel="stylesheet" href="<?=$third_party_theme_dir;?>style.css">
	<!-- script src="jquery-1.7.1.min.js"></script -->
	<script src="<?=$third_party_theme_dir;?>ui/jquery.ui.core.js"></script>
	<script src="<?=$third_party_theme_dir;?>ui/jquery.ui.widget.js"></script>
	<script src="<?=$third_party_theme_dir;?>ui/jquery.ui.mouse.js"></script>
	<script src="<?=$third_party_theme_dir;?>ui/jquery.ui.slider.js"></script>
	
	<style>
	
	

	</style>
	<script>
	$(function() {
		var readout = $('#readout');
		$('.scroll-bar').slider({
			orientation: 'vertical',
			max: '100',
			min: '0',
			value: '100'
		});

		$( ".scroll-bar" ).bind( "slide", function(event, ui) {
			// Why is there an extra +5 and +10 ? It's for padding top and botton on the scroll area
			var maxDepth = $('.scroll-content').height() - $('.scroll-area').height() + 10 ;
			var newTop = -((.01 * Math.abs(ui.value-100)) * maxDepth);
			newTop +=10;
			readout.html('<span style="color:gray;">newTop: ' + -newTop + '</span>');
			//console.log(newTop);
			$('.scroll-content').css('top', newTop+'px');
		});

		$( ".scroll-bar" ).bind( "slidechange", function(event, ui) {
			console.log(Math.abs(ui.value-100))
			readout.html('<span style="color:green;">' + Math.abs(ui.value-100) + '</span>');
		});

		$('.pp_morebt').bind('click', function(event) {
			console.log('getting more');
			$.ajax({
				url: "more.php",
				success: function(data, textStatus, jqXHR) {
					console.log('success: ' + data);
				},
				statusCode: {
					404: function() {
						console.log('I couldnt find that.');
					}
				}
			})
		});


		function sizeScrollbar() {
			
		}
		
		//change handle position on window resize
		$( window ).resize(function() {
			//resetValue();
			//sizeScrollbar();
			//reflowContent();
		});
		//init scrollbar size
		setTimeout( sizeScrollbar, 10 );//safari wants a timeout
	});
	</script>
</head>
<body>


<div class="scroll-area">
	<div class="scroll-content">
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><img src="<?=$third_party_theme_dir;?>pizza.jpg" alt="pic" width="100" height="100" border="0" /><div class="headline">This is my headline</div><a href="#" class='selectbtn'>SELECT</a></div>
		<div class="thumbnail"><div class="headline">Wait, there are more...</div><a href="#" class='pp_morebt'>Load More Images &gt;</a></div>
		
	</div>
	<div class="scroll-bar-wrap">
		<div class="scroll-bar"></div>
	</div>
<div id='readout'></div>

</div><!-- End demo -->

</body>
</html>
