<?php
if (ini_get('allow_url_fopen')) {
	echo "The setting for <em>allow_url_fopen</em> is TRUE";
} else {
	echo "The setting for <em>allow_url_fopen</em> is FALSE";
}
?>