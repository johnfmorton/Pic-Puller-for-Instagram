<!-- 
jQuery is here for debugging purposes only.
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
-->
<?php 

	/**
	 *  BEGIN: Helper Functions
	 */
	
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
		
	/**
	 *  END: Helper Functions
	 */

	$virdir = new VirtualDirectory();
	$third_party_theme_dir = $virdir->baseurl;

	// If multiple methods are supported, branch the method URL here
	//$method = $_GET['method'];
	$method = 'https://api.instagram.com/v1/users/self/media/recent/?';
	
	// The oAuth key to retreive photos for a user
	$oauthkey = $_GET["access_token"];
	
	// How many photos should be retrieved? If not set, set to 29 because that makes for a full set.
	$count = $_GET["count"];
	if (!isset($count)) {
		$count = '29';
	}

	// max id allows us to paginate results from Instagram
	$next_max_id = $_GET["max_id"];

	$jsonurl = $method."access_token=".$oauthkey."&count=".$count."&max_id=".$next_max_id;

	// the @ symbol will make this fail silently, so we'll need to check that $json actually is parsable and show alternate images instead
	
	$json = @file_get_contents($jsonurl,0,null,null);
	
	$json_output = json_decode($json);
	// does the $json_output->data array actually exist? ie, Was there an error getting the data from Instagram?		
	if (is_array($json_output->data)) {
		$theCount = 0;
		$new_next_max_id = $json_output->pagination->next_max_id;

		foreach ( $json_output->data as $images )
		{
			$theCount ++;
			$theImage = $images->images->thumbnail->url;
			$theId = $images->id;
			$theCaption = $images->caption->text;
			if (!isset($theCaption)) {
				$theCaption = '<em>untitled</em>';
			}
			echo "<div class='thumbnail' >
					<img src='$theImage' alt='Instagram image id: $theId' width='100' height='100' border=0 >
					<div class='headline' >$theCaption</div>
					<a href='#' class='selectbtn' data-id='$theId'>Select this image</a>
				</div>";
			if ($theCount >= $count && isset($new_next_max_id) ) {

				$nextURL = $third_party_theme_dir."pp_engine.php?"."access_token=".$oauthkey."&count=".$count."&max_id=".$new_next_max_id;
				echo "<div class='thumbnail getmore'>
					<div class='headline'>Need more to choose from?</div>
					<a href='$nextURL' class='pp_morebt'>Load more images</a>
				</div>";

				break;
			}
		}

	} else {
		echo "Error: Unable to communicate with Instagram to retreive images.";
	}

?>
