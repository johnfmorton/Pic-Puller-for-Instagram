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
	$method = isset($_GET['method']) ? $_GET['method'] : false;
	//print_r($method);
	



	switch ($method) {
		// The media case is used by the fieldtype to display image previews.
		// It returns JSON instead of the full HTML that the other functions display
		// in future releases, I will probably switch to all these methods returning
		// JSON and have the HTML creation does in whereever the data ends up.
		// You learn by doing, right?
		case 'media':
		$media_id = $_GET["media_id"];
		$theURL = "https://api.instagram.com/v1/media/$media_id?";
		$jsonurl = $theURL."access_token=".$oauthkey;
		
		// the @ symbol will make this fail silently, so we'll need to check that $json actually is parsable and show alternate images instead
				
			// $json = @file_get_contents($jsonurl,0,null,null);
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $jsonurl);
				// 1 to prevent the response from being outputted
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				// don't verify the SSL cert
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				$json = curl_exec($ch);
				curl_close($ch);

				$json_output = json_decode($json);
				// Need to debug? Uncomment out the following.
				// echo "<pre>";
				// print_r($json);
				// echo "</pre>";
				
				if ($json_output->meta->code === 200 ) {
					header('Content-Type: application/json');
					$success = '1';
					$imageTitle =  $json_output->data->caption->text;
					$imageURL = $json_output->data->images->low_resolution->url;
					$theUsername = $json_output->data->user->username;
					$theLink = $json_output->data->link;
					echo json_encode( array(
						'success' => $success,
						'code' => $json_output->meta->code,
						'imageTitle' => $imageTitle,
						'imageID' => $json_output->data->id,
						'imageURL' => $imageURL,
						'theUsername' => $theUsername,
						'theLink' => $theLink
					) );
				} else {
					header('Content-Type: application/json');
					$success = '0';
					$error_message =  $json_output->meta->error_message;
					$imageURL = '';
					echo json_encode( array(
						'success' => $success,
						'code' => $json_output->meta->code,
						'error_type' => $json_output->meta->error_type,
						'error_message' => $error_message
					) );
				}	

		break;

		case 'tagsearch':
			$next_max_tag_id = isset($_GET["next_max_tag_id"]) ? $_GET["next_max_tag_id"] : null;
			$searchTerm = $_GET['tag'];
			$theURL = "https://api.instagram.com/v1/tags/$searchTerm/media/recent?";
			$jsonurl = $theURL."access_token=".$oauthkey."&count=".$count."&max_tag_id=".$next_max_tag_id;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $jsonurl);
			// to prevent the response from being outputted
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			// don't verify the SSL cert
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$json = curl_exec($ch);
			curl_close($ch);

			$json_output = json_decode($json);

			// Need to debug? Uncomment out the following.
			// echo "<pre>";
			// print_r($json_output);
			// echo "</pre>";

			if ($json_output->meta->code === 200 ) {
				header('Content-Type: application/json');
				echo $json;
			} else {
				header('Content-Type: application/json');
				$success = '0';
				$error_message =  $json_output->meta->error_message;
				$imageURL = '';
				echo json_encode( array(
					'success' => $success,
					'code' => $json_output->meta->code,
					'error_type' => $json_output->meta->error_type,
					'error_message' => $error_message
				) );
			}

				// does the $json_output->data array actually exist? ie, Was there an error getting the data from Instagram?
				// if (is_array($json_output->data)) {
				// 	$theCount = 0;
				// 	$new_next_max_id = isset($json_output->pagination->next_max_tag_id) ? $json_output->pagination->next_max_tag_id : null;
				// 	$fullCount = count($json_output->data);
				// 	if($fullCount >0) {
				// 	foreach ( $json_output->data as $images )
				// 	{
				// 		$theCount ++;
				// 		$theImage = $images->images->thumbnail->url;
				// 		$theId = $images->id;
				// 		$theLink = $images->link;
				// 		$theUsername = $images->user->username;
				// 		$theCaption = isset($images->caption) ? $images->caption->text : '<em>untitled</em>';
				// 		echo "<div class='thumbnail' >
				// 				<img src='$theImage' alt='Instagram image id: $theId' width='100' height='100' border=0 data-link='$theLink'>
				// 				<div class='headline' >$theCaption <em>by $theUsername</em></div>
				// 				<a href='#' class='selectbtn' data-id='$theId'>Select this image</a>
				// 			</div>";
				// 		if ($theCount == $fullCount && isset($new_next_max_id) ) {

				// 			$nextURL = $third_party_theme_dir."pp_engine.php?"."access_token=".$oauthkey."&method=tagsearch&tag=".$searchTerm."&count=".$count."&next_max_tag_id=".$new_next_max_id;
				// 			echo "<div class='thumbnail getmore'>
				// 				<div class='headline'>Need more to choose from?</div>
				// 				<a href='$nextURL' class='pp_morebt'>Load more images</a></div>";
							
				// 			break;
				// 		}
				// 	}
				// } else {
				// 	echo "<div class='thumbnail'>
				// 				<div class='headline'>No results for <em>$searchTerm</em>.</div>
				// 				</div>";
							
				// 			break;
				// }


				// } else {
				// 	echo "<div class='thumbnail'>
				// 				<div class='headline'>No results for <em>$searchTerm</em>.</div>
				// 				</div>";
							
				// 			break;
				// 	//echo "Error: Unable to communicate with Instagram to retreive images.";
				// }



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
			
			if ($json_output->meta->code === 200 ) {
				header('Content-Type: application/json');
				echo $json;
			} else {
				echo "Error: Unable to communicate with Instagram to retreive images.";
			}

			// does the $json_output->data array actually exist? ie, Was there an error getting the data from Instagram?
			// if (is_array($json_output->data)) {
			// 	$theCount = 0;
			// 	$new_next_max_id = isset($json_output->pagination->next_max_id) ? $json_output->pagination->next_max_id : null;
			// 	$fullCount = count($json_output->data);
			// 	foreach ( $json_output->data as $images )
			// 	{
			// 		$theCount ++;
			// 		$theImage = $images->images->thumbnail->url;
			// 		$theId = $images->id;
			// 		$theLink = $images->link;
			// 		$theUsername = $images->user->username;
			// 		$theCaption = isset($images->caption) ? $images->caption->text : '<em>untitled</em>';
			// 		echo "<div class='thumbnail' >
			// 				<img src='$theImage' alt='Instagram image id: $theId' width='100' height='100' border=0 >
			// 				<div class='headline' >$theCaption</div>
			// 				<a href='#' class='selectbtn' data-id='$theId'>Select this image</a>
			// 			</div>";
			// 		if ($theCount == $fullCount && isset($new_next_max_id) ) {

			// 			$nextURL = $third_party_theme_dir."pp_engine.php?"."access_token=".$oauthkey."&count=".$count."&max_id=".$new_next_max_id;
			// 			echo "<div class='thumbnail getmore'>
			// 				<div class='headline'>Need more to choose from?</div>
			// 				<a href='$nextURL' class='pp_morebt'>Load more images</a></div>";

			// 			break;
			// 		}
			// 	}

			// } else {
			// 	echo "Error: Unable to communicate with Instagram to retreive images.";
			// }


			break;
	}
?>