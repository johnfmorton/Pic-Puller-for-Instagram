<h3>Success!</h3>
<p>Your Instagram application information has now been updated.</p>

<?php

if ($frontend_auth_url === '' ){
	$frontend_auth_url = '<em>No value set</em>';
}
?>

<p>Your Client ID:</p>
<p><pre>
	<?=$client_id;?>
</pre></p>
<p>Your Client Secret:</p>
<p><pre>
	<?=$client_secret;?>
</pre></p>
<p>Your Front-end Authorization URL:</p>
<p><pre>
	<?=$frontend_auth_url;?>
</pre></p>
<br>
<p><strong><a href="<?=$cancel_url;?>">Return to the App Info page</a></strong>.</p>