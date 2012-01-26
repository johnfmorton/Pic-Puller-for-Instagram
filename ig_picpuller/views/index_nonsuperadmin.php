<?php if (!$preexisting_app) {?>
<p><?=$moduleShortTitle?> has not been fully installed. This requires Super Admin privileges.</p>
<p>When the set up is completed, you will be able to grant access from this page.</p>
<?php } else { ?>
	<p>This site can only display your Instagram photos if you grant it access to your photo stream.</p>
	<p>NOTE: This site will not see or store your password. It will only store an authorization key supplied by Instagram. You may revoke this access at any time.</p>
<p><a href="https://instagram.com/oauth/authorize/?client_id=<?=$clientID;?>&redirect_uri=<?=$redirect_url;?>&display=touch&response_type=code" class='submit authorize'>Authorize with Instagram</a></p>
<script>
$(document).ready(function()
    {
		$(".authorize").bind('click', processAuthorization);

		function processAuthorization(e)
		{
			e.preventDefault();
			var theURL = $(this).attr('href');
			window.open(theURL,'ingram_auth','width=400,height=300,left=0,top=100,screenX=0,screenY=100');
			$(window).focus(function() {
			   // user closed the popup window... refresh this page to see if their info was successfully saved
				window.location.reload();
			});
		}
		
		/*function getParameterByName(name, data) {
		    var match = RegExp('[?&]' + name + '=([^&]*)')
		                    .exec(data);
		    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
		}*/
});
</script>
<?php } ?>