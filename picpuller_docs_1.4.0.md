# Change log

1.4.0 - 2012DEC25
-Pic Puller for Instagram Browser field type updated.
-Field type now supports tag search of all Instagram
-Cosmetic update to field type
-Updated preferences to allow or disallow browsing of user's Instram stream and the search functionality on a per instance basis
-Field type no longer requires PHP 'short_open_tag' be enabled.

1.3.1 - 2012OCT13
- Updated pagination for tagged_media to use 'next_max_id' instead of 'next_max_tag_id' due to 'next_max_tag_id' being deprecated in the Instagram API.

1.3.0 - 2012SEPT08
- All tags returned by Pic Puller are now prefixed with 'ig_' to avoid naming conflicts. Those updating, please read special note in your downloaded ZIP file on how to keep your current templates working properly.
- The default prefix 'ig_' can be reset to different string under the 'Active Site App Info' on a per application basis. Each Pic Puller app, in an MSM set up, can have its own prefix.
- New tag added to all return loops. 'ig_cacheddata' will respond 'yes' when cached data is being returned, and 'no' when live data from Instagram is being returned.
- The verbiage of some error messages were rewritten to indicate when cached data was not available.
- In "Pic Puller for Instagram Browser", updated pp_engine with missing curl_close.

1.2.0 - 17AUG2012
- exp:ig_picpuller:media tag now returns number of 'likes' for an image and the 'comment_count' for an image.
- exp:ig_picpuller:media_recent tag now returns number of 'likes' for an image and the 'comment_count' for an image.
- exp:ig_picpuller:user_liked tag now returns number of 'likes' for an image and the 'comment_count' for an image.
- exp:ig_picpuller:user_feed tag now returns number of 'likes' for an image and the 'comment_count' for an image.
- exp:ig_picpuller:media_tagged tag now returns number of 'likes' for an image and the 'comment_count' for an image.
- exp:ig_picpuller:popular tag now returns number of 'likes' for an image and the 'comment_count' for an image.
- Updated link to Instagram site for removal of user applications after user has removed authorization from within ExpressionEngine.
- Introduction of "Advanced" tab to module.
- First "Advanced" option: alternate method of validating with Instagram.
- In "Pic Puller for Instagram Browser", changed from "file_get_contents" method to "cURL" method for retrieving images in the  fieldtype display. 
- In "Pic Puller for Instagram Browser", Colorbox javascript updated to isolate it from other modules' use of the same library. (For example, the add-on Channel Images had CSS that would conflict with Pic Puller CSS files.)
- Removed extraneous CSS files from "Pic Puller for Instagram Browser" theme.

1.1.1 - (private beta release only) 7AUG2012
- CSS conflict test run.
- Field type pp_engine.php variation test run.

1.1.0 - 12MAY2012
- Updated PP to be compatible with MSM
- Various updates to admin panel to help users with the control panel know which site Pic Puller is dealing with when managing multiple Instagram applications.
- Minor language and visual tweaks in the control panel for consistency.
- Updates to Instagram management URLs that changed after Facebook's acquisition of Instagram.

1.0.1 - 01MAR2012
- Fixed incorrect longitude reporting in some functions.

1.0.0 - 29FEB2012
- New fieldtype, Pic Puller for Instagram Browser, added to browse a user's Instagram feed. 
- Fieldtype compatible with Matrix.
- Module update to help manage cache files created. Pic Puller will now only keep the 25 most recently created cache files.
- CSS updates in module for visual consistency

0.9.2 - 04FEB2012
- Added support for authorizing logged in members from the front end of ExpressionEngine site.

0.9.1 - (private beta release only) 28JAN2012
- Added 'media' tag to retrieve individual media by Instagram ID

0.9.0 - 24JAN2012
- Initial release

# Popular Photos on Instagram

## exp:ig_picpuller:popular

Description:
Get a list of what media is most popular at the moment.

NOTE: If you are using Pic Puller Lite, the ExpressionEngine tags are slightly different. exp:ig_picpuller_lite:popular is used instead of exp:ig_picpuller:popular.

Instragram docs page for this function:
http://instagram.com/developer/endpoints/media/#get_media_popular

Required parameters:
none

Optional parameters:
limit: an integer for how many images to request from Instagram. Instagram may return fewer under some circumstances. Maximum of 32 allowed by Instagram.
use_stale_cache: either ‘yes’ or ‘no’ (defaults to ‘yes’ if undefined)

Tags returned in a successful Expression Engine loop:
status: a string of “true” is returned when data is returned
ig_media_id: the Instagram unique media ID for the image
ig_created_time: time stamp of image creation time, Unix timestamp formatted
ig_link: URL of the images homepage on Instagram
ig_caption: The caption provided by the author. Note, it may be left untitled which will return an empty string.
ig_thumbnail: URL to image, sized 150x150
ig_low_resolution: URL to image, sized 306x306
ig_standard_resolution: URL to image, sized 612x612
ig_latitude: latitude data, if available
ig_longitude: longitude data, if available
ig_username: the Instagram username of the user whose account the image is from
ig_full_name: the full name provided by the user whose account the image is from
ig_profile_picture: URL to the profile image of the user

Tags returned in an unsuccessful Expression Engine loop:
ig_status: “false”
ig_error_type: a single code word indicating the type of error (NoInstagramApp, MissingReqParameter, UnauthorizedUser issued by Pic Puller. Other codes are passed through from Instagram.)
ig_error_message: a string describing the error

# User information

## exp:ig_picpuller:user

Description:
Get basic information about a user.

Instragram docs page for this function:
http://instagram.com/developer/endpoints/users/#get_users

Required parameters:
user_id: the Expression Engine user id (not an Instagram user id)

Optional parameters:
use_stale_cache: either ‘yes’ or ‘no’ (defaults to ‘yes’ if undefined)

Tags returned in a successful Expression Engine loop:
ig_status: “true” or “false” — “true” when valid results returned
ig_username: the Instagram username
ig_id: the Instagram user id
ig_bio: biography information provided by the Instagram user
ig_profile_picture: URL to the profile image of the user
ig_website: the website URL provided by the user on Instagram
ig_full_name: the full name provided by the user on Instagram
ig_counts_media: the number of images in this user’s Instagram feed in total
ig_counts_followed_by: the number of users who follow this user on Instagram
ig_counts_follows: the number of users this user follows on Instagram

Tags returned in an unsuccessful Expression Engine loop:
ig_status: “false”
ig_error_type: a single code word indicating the type of error (NoInstagramApp, MissingReqParameter, UnauthorizedUser issued by Pic Puller. Other codes are passed through from Instagram.)
ig_error_message: a string describing the error

# User feed

## exp:ig_picpuller:user_feed

Description:
See the authenticated user’s feed. Includes user photos and photos of other users the select user follows in single feed.

Instragram docs page for this function:
http://instagram.com/developer/endpoints/users/#get_users_feed

Required parameters:
user_id: This is the ID number of an Expression Engine user. (It is not the Instagram user id number.)

Optional parameters:
limit: an integer for how many images to request from Instagram. Instagram may return fewer under some circumstances. Maximum of 32 allowed by Instagram.
use_stale_cache: either ‘yes’ or ‘no’ (defaults to ‘yes’ if undefined)
max_id: an integer used to determine pagination of results. (See next_max_id in the ‘Tags returned’ below section for more information.)

Tags returned in a successful Expression Engine loop:
ig_status: “true” or “false” — “true” when valid results returned
ig_media_id: the Instagram unique media ID for the image
ig_created_time: time stamp of image creation time, Unix timestamp formatted
ig_link: URL of the images homepage on Instagram
ig_caption: The caption provided by the author. Note, it may be ig_left untitled which will return an empty string.
ig_thumbnail: URL to image, sized 150x150
ig_low_resolution: URL to image, sized 306x306
ig_standard_resolution: URL to image, sized 612x612
ig_latitude: latitude data, if available
ig_longitude: longitude data, if available
ig_next_max_id: an integer, provided by Instagram, used to return the next set in the same series of images. Pass this value into the max_id parameter of the loop to get the next page of results.
ig_user_id: the Instagram user ID of the user whose account the image is from
ig_username: the Instagram username of the user whose account the image is from
ig_profile_picture: URL to the profile image of the user
ig_website: the website URL provided by the user whose account the image is from
ig_full_name: the full name provided by the user whose account the image is from

Tags returned in an unsuccessful Expression Engine loop:
ig_status: “false”
ig_error_type: a single code word indicating the type of error (NoInstagramApp, MissingReqParameter, UnauthorizedUser issued by Pic Puller. Other codes are passed through from Instagram.)
ig_error_message: a string describing the error

# Recent media

## exp:ig_picpuller:media_recent

Description:
Get the most recent media published by a user.

Instragram docs page for this function:
http://instagram.com/developer/endpoints/users/#get_users_media_recent

Required parameters:
user_id: the Expression Engine user id (not an Instagram user id)

Optional parameters:
limit: an integer for how many images to request from Instagram. Instagram may return fewer under some circumstances. Maximum of 32 allowed by Instagram.
use_stale_cache: either ‘yes’ or ‘no’ (defaults to ‘yes’ if undefined)
max_id: an integer used to determine pagination of results. (See next_max_id in the ‘Tags returned’ below section for more information.)

Tags returned in a successful Expression Engine loop:
ig_status: a string of “true” is returned when data is returned
ig_media_id: the Instagram unique media ID for the image
ig_created_time: time stamp of image creation time, Unix timestamp formatted
ig_link: URL of the images homepage on Instagram
ig_caption: The caption provided by the author. Note, it may be left untitled which will return an empty string.
ig_thumbnail: URL to image, sized 150x150
ig_low_resolution: URL to image, sized 306x306
ig_standard_resolution: URL to image, sized 612x612
ig_latitude: latitude data, if available
ig_longitude: longitude data, if available
ig_next_max_id: an integer, provided by Instagram, used to return the next set in the same series of images. Pass this value into the max_id parameter of the loop to get the next page of results.

Tags returned in an unsuccessful Expression Engine loop:
ig_status: “false”
ig_error_type: a single code word indicating the type of error (NoInstagramApp, MissingReqParameter, UnauthorizedUser issued by Pic Puller. Other codes are passed through from Instagram.)
ig_error_message: a string describing the error

# Liked image feed

## exp:ig_picpuller:user_liked

Description:
See the authenticated user’s list of media they’ve liked. Note that this list is ordered by the order in which the user liked the media. Private media is returned as long as the authenticated user has permission to view that media. Liked media lists are only available for the currently authenticated user.

Instragram docs page for this function:
http://instagram.com/developer/endpoints/users/#get_users_liked_feed

Required parameters:
user_id: the Expression Engine user id (not an Instagram user id)

Optional parameters:
limit: an integer for how many images to request from Instagram. Instagram may return fewer under some circumstances. Maximum of 32 allowed by Instagram.
use_stale_cache: either ‘yes’ or ‘no’ (defaults to ‘yes’ if undefined)
max_id: an integer used to determine pagination of results. (See next_max_id in the ‘Tags returned’ below section for more information.)

Tags returned in a successful Expression Engine loop:
ig_status: a string of “true” is returned when data is returned
ig_media_id: the Instagram unique media ID for the image
ig_created_time: time stamp of image creation time, Unix timestamp formatted
ig_link: URL of the images homepage on Instagram
ig_caption: The caption provided by the author. Note, it may be left untitled which will return an empty string.
ig_thumbnail: URL to image, sized 150x150
ig_low_resolution: URL to image, sized 306x306
ig_standard_resolution: URL to image, sized 612x612
ig_latitude: latitude data, if available
ig_longitude: longitude data, if available
ig_next_max_id: an integer, provided by Instagram, used to return the next set in the same series of images. Pass this value into the max_id parameter of the loop to get the next page of results.

ig_username: the Instagram username of the user whose account the image is from
ig_full_name: the full name provided by the user whose account the image is from
ig_profile_picture: URL to the profile image of the user
ig_website: the website URL provided by the user whose account the image is from
ig_user_id: the Instagram user ID of the user whose account the image is from

Tags returned in an unsuccessful Expression Engine loop:
ig_status: “false”
ig_error_type: a single code word indicating the type of error (NoInstagramApp, MissingReqParameter, UnauthorizedUser issued by Pic Puller. Other codes are passed through from Instagram.)
ig_error_message: a string describing the error

# Media by tag

## exp:ig_picpuller:tagged_media

Description:
Get a list of recently tagged media. Note that this media is ordered by when the media was tagged with this tag, rather than the order it was posted. 

For consistency amongst the tags used in Pic Puller, the ExpressionEngine tags use ‘ig_next_max_id’ for pagination. If you refer to the Instagram documentation, you will see references ‘max_tag_id’ is used for pagination. That does not apply to Pic Puller. Those tags are rewritten by the module to be ‘ig_next_max_id’.

Instragram docs page for this function:
http://instagram.com/developer/endpoints/tags/#get_tags_media_recent

Required parameters:
user_id: the Expression Engine user id (not an Instagram user id)

Optional parameters:
limit: an integer for how many images to request from Instagram. Instagram may return fewer under some circumstances. Maximum of 32 allowed by Instagram.
use_stale_cache: either ‘yes’ or ‘no’ (defaults to ‘yes’ if undefined)
max_id: an integer used to determine pagination of results. (See next_max_id in the ‘Tags returned’ below section for more information.)

Tags returned in a successful Expression Engine loop:
ig_status: “true” or “false” — “true” when valid results returned
ig_media_id: the Instagram unique media ID for the image
created_time: time stamp of image creation time, Unix timestamp formatted
ig_link: URL of the images homepage on Instagram
ig_caption: The caption provided by the author. Note, it may be left untitled which will return an empty string.
ig_thumbnail: URL to image, sized 150x150
ig_low_resolution: URL to image, sized 306x306
ig_standard_resolution: URL to image, sized 612x612
ig_latitude: latitude data, if available
ig_longitude: longitude data, if available
ig_next_max_id: an integer, provided by Instagram, used to return the next set in the same series of images. Pass this value into the max_id parameter of the loop to get the next page of results.
ig_user_id: the Instagram user ID of the user whose account the image is from
ig_username: the Instagram username of the user whose account the image is from
ig_profile_picture: URL to the profile image of the user
ig_website: the website URL provided by the user whose account the image is from
ig_full_name: the full name provided by the user whose account the image is from

Tags returned in an unsuccessful Expression Engine loop:
ig_status: “false”
ig_error_type: a single code word indicating the type of error (NoInstagramApp, MissingReqParameter, UnauthorizedUser issued by Pic Puller. Other codes are passed through from Instagram.)
ig_error_message: a string describing the error

# Media by ID

## exp:ig_picpuller:media

Description:
Get information about a media object.

Can be used in ExpressionEngine with a custom field containing an Instagram media id but designed to work together with the 'Pic Puller for Instagram Browser' fieldtype included with Pic Puller. See fieldtype documentation at the end of this document.

Instragram docs page for this function:
http://instagram.com/developer/endpoints/media/#get_media

Required parameters:
user_id: the Expression Engine user id (not an Instagram user id)
media_id: this is the ID number that Instagram has assigned to an image

Optional parameters:
use_stale_cache: either ‘yes’ or ‘no’ (defaults to ‘yes’ if undefined)

Tags returned in a successful Expression Engine loop:
ig_status: a string of “true” is returned when data is returned
ig_created_time: time stamp of image creation time, Unix timestamp formatted
ig_link: URL of the images homepage on Instagram
ig_caption: The caption provided by the author. Note, it may be left untitled which will return an empty string.
ig_thumbnail: URL to image, sized 150x150
ig_low_resolution: URL to image, sized 306x306
ig_standard_resolution: URL to image, sized 612x612
ig_latitude: latitude data, if available
ig_longitude: longitude data, if available
ig_username: the Instagram username of the user whose account the image is from
ig_user_id: the Instagram user id of the user whose account the image is from
ig_full_name: the full name provided by the user whose account the image is from
ig_profile_picture: URL to the profile image of the user
ig_website: the website information whose account the image is from, if available
ig_likes: number of likes for piece of media

Tags returned in an unsuccessful Expression Engine loop:
ig_status: “false”
ig_error_type: a single code word indicating the type of error (NoInstagramApp, MissingReqParameter, UnauthorizedUser issued by Pic Puller. Other codes are passed through from Instagram.)
ig_error_message: a string describing the error

# Authorization Link for use on ExpressionEngine front end.

## exp:ig_picpuller:authorization_link

Description:
As of version 0.9.2, users no longer need access to the control panel to authorize a Pic Puller application with Instagram.

This is a single ExpressionEngine tag. Used without any parameters, it will generate a URL for logged in users that will toggle their authorization of your Instagram application. In other words, for users who have not authorized your Instagram application, the URL will authorize the app. For users who have authorized your Instagram application, the generated URL will de-authorize the app. When you only generate the URL string, you will need to manage the user experience. (Note: This tag should be used in a conditional that shows it only to logged in users. Each user must have an account with your ExpressionEngine installation in order to store that user’s Instagram validation credentials.)

The optional parameter 'fullhtml', when set to 'yes', will generate the correct URL and wrap it in an '<a>' tag pair. It also generates a small javascript snippet that requires jQuery. Using the 'fullhtml' option offers some customization options to override the default html that will be generated. You define the text for the authorization link and de-authorization link. You can also add classes to the <a> tag that it generates.

The generated html can be used as is, or it can serve as a starting point if you'd like to create a fully customized solution.

Required parameters:
none

Optional parameters:
fullhtml: Generate a link + javascript, requires jQuery. (Accepts ‘yes’ or ‘no’, default is ‘no.)
authtext: If the fullhtml parameter is set to ‘yes’, this parameter will override the default link text for an authorization link. Default text is “Authorize with Instagram”.
authclass: If the fullhtml parameter is set to ‘yes’, this parameter will add a class or classes to the generated <a> tag for an authorization link. Multiple classes are separated by a space.
deauthtext: If the fullhtml parameter is set to ‘yes’, this parameter will override the default link text for a de-authorization link. Default text is “De-authorize with Instagram”.
deauthclass: If the fullhtml parameter is set to ‘yes’, this parameter will add a class or classes to the generated <a> tag for a de-authorization link. Multiple classes are separated by a space.

Tags returned in a successful Expression Engine loop:
No tags are generated with this function.

Instead, a string is generated containing just a URL or a string that is full HTML code. The HTML code version requires that you have jQuery on your site.

Tags returned in an unsuccessful Expression Engine loop:
If there is no Instagram application stored in your site, this tag will report the error message:
ERROR: There is no Instagram application in the system to authorize.

If the user being served this tag is not logged in, this tag will report the error message:
ERROR: Only logged in users can authorize this application.

It is recommended that you use this tag within a conditional loop that displays it only to logged in members of your site.

There is an HTML file called "sample_frontend_auth_template.html" included with the module to get your front end authorization template started.

-----

# 'Pic Puller for Instagram Browser' Fieldtype

The 'Pic Puller for Instagram Browser' fieldtype is part of Pic Puller. It will allow a user who has authorized your Pic Puller application to browse their own photo stream for images. They can select one image and the image browser will populate it's field with the selected image's Instagram media_id.

The media_id is intended use is to be used in conjunction with the Pic Puller 'media' tag pair. The fieldtype will store a media_id from Instagram. The field type assists getting the desired media_id by using a photo browser interface that displays the logged in user's photos.

Since only users who have authorized Pic Puller can show the media browser, users who have not authorized are shown a message indicating they need to authorize before using the media browser. 

The fieldtype requires javascript. 

The fieldtype is compatible with Matrix.

There is a multiple options for the fieldtype. All options default to "on".

Option 1: You can use the default instructional language that the fieldtype will automatically include or you may turn it off. 
Option 2: You may choose to hide the Instagram browser that allows a user to choose from the logged in user's photo stream.
Option 3: You may choose to hide the Instagram browser that allows a user to search and choose photos from all of Instagram's public images.


-----

# Pic Puller compatibility with ExpressionEngine Multiple Site Manager

As of version 1.1.0, Pic Puller will allow you to manage multiple Instagram applications in Expression Engine when you have the Multiple Site Manage installed. Each site you manage can have an Instagram application associated with it. 

Each site will have it's own authorization credentials with Instagram. Each site's users will have separate credentials stored in EE for each site.

One potential issue you may have with authorizing apps comes up in the scenario where your control panel is at one domain, for example siteA.com/admin.php and the site you wish to authorize with Instagram is at siteB.com. You will need to authorize with Instagram from siteB.com and be logged into siteB.com with ExpressionEngine. 

The module addresses this issue by using the front-end authorization feature of Pic Puller. See the front-end authorization documentation for more information. Also see the included front-end authorization template, "sample_frontend_auth_template.html", included with the module to get your front-end authorization template started.

If you manage siteB.com from the same domain, for example siteB.com/admin.php, you will not run into this problem. 

There is an optional authorization method you may try in this situation.The alternate authorization method is intended to get around some stubborn servers that won't allow the normal authorization process, but it has proven useful in this situation as well. This option is available to SuperAdmin level users only under the "Advanced" tab.