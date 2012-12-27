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
		var $protocol = 'http';
		var $site;
		var $thisfile;
		var $real_directories;
		var $num_of_real_directories;
		var $virtual_directories = array();
		var $num_of_virtual_directories = array();
		var $baseurl;
		var $thisurl;
		var $count;
		var $next_max_id;
		var $new_next_max_id;
		function VirtualDirectory()
		{
			//$this->protocol = 'http';
	       	if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) {
	       		if($_SERVER['HTTPS'] == 'on'){
	       			$this->protocol .= "s";
	       		}
	       	}
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

	// The oAuth key to retreive photos for a user
	$oauthkey = isset($_GET["access_token"]) ? $_GET["access_token"] : null;
	
	// How many photos should be retrieved? If not set, set to 29 because that makes for a full set.
	$count = isset($_GET["count"]) ? $_GET["count"] : null;
	if (!isset($count)) {
		$count = '29';
	}

	// If multiple methods are supported, branch the method URL here
	$method = $_GET['method'];
	//print_r($method);
	



	switch ($method) {
		case 'tagsearch':
			$next_max_tag_id = isset($_GET["next_max_tag_id"]) ? $_GET["next_max_tag_id"] : null;
			$searchTerm = $_GET['tag'];
			$theURL = "https://api.instagram.com/v1/tags/$searchTerm/media/recent?";
			$jsonurl = $theURL."access_token=".$oauthkey."&count=".$count."&max_tag_id=".$next_max_tag_id;

			// the @ symbol will make this fail silently, so we'll need to check that $json actually is parsable and show alternate images instead
				
			// $json = @file_get_contents($jsonurl,0,null,null);
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $jsonurl);
				// to prevent the response from being outputted
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				// don't verify the SSL cert
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				$json = curl_exec($ch);
				curl_close($ch);

				// Need to debug? Uncomment out the following.
				// echo "<pre>";
				// print_r($json);
				// echo "</pre>";



				$json_output = json_decode($json);
				

				// does the $json_output->data array actually exist? ie, Was there an error getting the data from Instagram?
				if (is_array($json_output->data)) {
					$theCount = 0;
					$new_next_max_id = isset($json_output->pagination->next_max_tag_id) ? $json_output->pagination->next_max_tag_id : null;
					$fullCount = count($json_output->data);
					if($fullCount >0) {
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
						if ($theCount == $fullCount && isset($new_next_max_id) ) {

							$nextURL = $third_party_theme_dir."pp_engine.php?"."access_token=".$oauthkey."&method=tagsearch&tag=".$searchTerm."&count=".$count."&next_max_tag_id=".$new_next_max_id;
							echo "<div class='thumbnail getmore'>
								<div class='headline'>Need more to choose from?</div>
								<a href='$nextURL' class='pp_morebt'>Load more images</a></div>";
							
							break;
						}
					}
				} else {
					echo "<div class='thumbnail'>
								<div class='headline'>No results for <em>$searchTerm</em>.</div>
								</div>";
							
							break;
				}


				} else {
					echo "<div class='thumbnail'>
								<div class='headline'>No results for <em>$searchTerm</em>.</div>
								</div>";
							
							break;
					//echo "Error: Unable to communicate with Instagram to retreive images.";
				}



			break;
		
		default:
			$theURL = 'https://api.instagram.com/v1/users/self/media/recent/?';
			$next_max_id = isset($_GET["max_id"]) ? $_GET["max_id"] : null;
			$jsonurl = $theURL."access_token=".$oauthkey."&count=".$count."&max_id=".$next_max_id;

			// the @ symbol will make this fail silently, so we'll need to check that $json actually is parsable and show alternate images instead
	
			// $json = @file_get_contents($jsonurl,0,null,null);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $jsonurl);
			// to prevent the response from being outputted
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			// don't verify the SSL cert
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$json = curl_exec($ch);
			curl_close($ch);

			// Need to debug? Uncomment out the following.
			// echo "<pre>";
			// print_r($json);
			// echo "</pre>";


			$json_output = json_decode($json);
			

			// does the $json_output->data array actually exist? ie, Was there an error getting the data from Instagram?
			if (is_array($json_output->data)) {
				$theCount = 0;
				$new_next_max_id = isset($json_output->pagination->next_max_id) ? $json_output->pagination->next_max_id : null;
				$fullCount = count($json_output->data);
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
					if ($theCount == $fullCount && isset($new_next_max_id) ) {

						$nextURL = $third_party_theme_dir."pp_engine.php?"."access_token=".$oauthkey."&count=".$count."&max_id=".$new_next_max_id;
						echo "<div class='thumbnail getmore'>
							<div class='headline'>Need more to choose from?</div>
							<a href='$nextURL' class='pp_morebt'>Load more images</a></div>";

						break;
					}
				}

			} else {
				echo "Error: Unable to communicate with Instagram to retreive images.";
			}


			break;
	}


	

?>