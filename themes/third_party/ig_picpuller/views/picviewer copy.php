<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title></title>
	<style>
		background: #DDD;
		color: #333;
	</style>
</head>
<body>


<h1>Your photos from Instagram.</h1>
<p>The oAuth is <?php echo $_GET["oauth"]; ?></p>
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
</body>
</html>