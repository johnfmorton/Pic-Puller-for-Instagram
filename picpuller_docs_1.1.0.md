# Change log

1.1.0 - ??APR2012
- Updated PP to be compatible with MSM

1.0.1 - 01MAR2012
- Fixed incorrect longitude reporting in some functions.

1.0.0 - 29FEB2012
- New fieldtype, Pic Puller for Instagram Browser, added to browse a user's Instagram feed. 
- Fieldtype compatible with Matrix.
- Module update to help manage cache files created. Pic Puller will now only keep the 25 most recently created cache files.
- CSS updates in module for visual consistency

0.9.2 - 04FEB2012
- Added support for authorizing logged in members from the front end of ExpressionEngine site.

0.9.1 - 28JAN2012
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
limit: an integer that determines how many images to return. Maximum of 32 allowed by Instagram.
use_stale_cache: either ‘yes’ or ‘no’ (defaults to ‘yes’ if undefined)

Tags returned in a successful Expression Engine loop:
status: a string of “true” is returned when data is returned
media_id: the Instagram unique media ID for the image
created_time: time stamp of image creation time, Unix timestamp formatted
link: URL of the images homepage on Instagram
caption: The caption provided by the author. Note, it may be left untitled which will return an empty string.
thumbnail: URL to image, sized 150x150
low_resolution: URL to image, sized 306x306
standard_resolution: URL to image, sized 612x612
latitude: latitude data, if available
longitude: longitude data, if available
username: the Instagram username of the user whose account the image is from
full_name: the full name provided by the user whose account the image is from
profile_picture: URL to the profile image of the user

Tags returned in an unsuccessful Expression Engine loop:
status: “false”
error: a string describing the error

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
status: “true” or “false” — “true” when valid results returned
username: the Instagram username
id: the Instagram user id
bio: biography information provided by the Instagram user
profile_picture: URL to the profile image of the user
website: the website URL provided by the user on Instagram
full_name: the full name provided by the user on Instagram
counts_media: the number of images in this user’s Instagram feed in total
counts_followed_by: the number of users who follow this user on Instagram
counts_follows: the number of users this user follows on Instagram

Tags returned in an unsuccessful Expression Engine loop:
status: “false”
error: a string describing the error

# User feed

## exp:ig_picpuller:user_feed

Description:
See the authenticated user’s feed. Includes user photos and photos of other users the select user follows in single feed.

Instragram docs page for this function:
http://instagram.com/developer/endpoints/users/#get_users_feed

Required parameters:
user_id: This is the ID number of an Expression Engine user. (It is not the Instagram user id number.)

Optional parameters:
limit: an integer that determines how many images to return. Maximum of 32 allowed by Instagram.
use_stale_cache: either ‘yes’ or ‘no’ (defaults to ‘yes’ if undefined)
max_id: an integer used to determine pagination of results. (See next_max_id in the ‘Tags returned’ below section for more information.)

Tags returned in a successful Expression Engine loop:
status: “true” or “false” — “true” when valid results returned
media_id: the Instagram unique media ID for the image
created_time: time stamp of image creation time, Unix timestamp formated
link: URL of the images homepage on Instagram
caption: The caption provided by the author. Note, it may be left untitled which will return an empty string.
thumbnail: URL to image, sized 150x150
low_resolution: URL to image, sized 306x306
standard_resolution: URL to image, sized 612x612
latitude: latitude data, if available
longitude: longitude data, if available
next_max_id: an integer, provided by Instagram, used to return the next set in the same series of images. Pass this value into the max_id parameter of the loop to get the next page of results.
user_id: the Instagram user ID of the user whose account the image is from
username: the Instagram username of the user whose account the image is from
profile_picture: URL to the profile image of the user
website: the website URL provided by the user whose account the image is from
full_name: the full name provided by the user whose account the image is from

Tags returned in an unsuccessful Expression Engine loop:
status: “false”
error: a string describing the error

# Recent media

## exp:ig_picpuller:media_recent

Description:
Get the most recent media published by a user.

Instragram docs page for this function:
http://instagram.com/developer/endpoints/users/#get_users_media_recent

Required parameters:
user_id: the Expression Engine user id (not an Instagram user id)

Optional parameters:
limit: an integer that determines how many images to return. Maximum of 32 allowed by Instagram.
use_stale_cache: either ‘yes’ or ‘no’ (defaults to ‘yes’ if undefined)
max_id: an integer used to determine pagination of results. (See next_max_id in the ‘Tags returned’ below section for more information.)

Tags returned in a successful Expression Engine loop:
status: a string of “true” is returned when data is returned
media_id: the Instagram unique media ID for the image
created_time: time stamp of image creation time, Unix timestamp formattede
link: URL of the images homepage on Instagram
caption: The caption provided by the author. Note, it may be left untitled which will return an empty string.
thumbnail: URL to image, sized 150x150
low_resolution: URL to image, sized 306x306
standard_resolution: URL to image, sized 612x612
latitude: latitude data, if available
longitude: longitude data, if available
next_max_id: an integer, provided by Instagram, used to return the next set in the same series of images. Pass this value into the max_id parameter of the loop to get the next page of results.

Tags returned in an unsuccessful Expression Engine loop:
status: “false”
error: a string describing the error

# Liked image feed

## exp:ig_picpuller:user_liked

Description:
See the authenticated user’s list of media they’ve liked. Note that this list is ordered by the order in which the user liked the media. Private media is returned as long as the authenticated user has permission to view that media. Liked media lists are only available for the currently authenticated user.

Instragram docs page for this function:
http://instagram.com/developer/endpoints/users/#get_users_liked_feed

Required parameters:
user_id: the Expression Engine user id (not an Instagram user id)

Optional parameters:
limit: an integer that determines how many images to return. Maximum of 32 allowed by Instagram.
use_stale_cache: either ‘yes’ or ‘no’ (defaults to ‘yes’ if undefined)
max_id: an integer used to determine pagination of results. (See next_max_id in the ‘Tags returned’ below section for more information.)

Tags returned in a successful Expression Engine loop:
status: a string of “true” is returned when data is returned
media_id: the Instagram unique media ID for the image
created_time: time stamp of image creation time, Unix timestamp formatted
link: URL of the images homepage on Instagram
caption: The caption provided by the author. Note, it may be left untitled which will return an empty string.
thumbnail: URL to image, sized 150x150
low_resolution: URL to image, sized 306x306
standard_resolution: URL to image, sized 612x612
latitude: latitude data, if available
longitude: longitude data, if available
next_max_id: an integer, provided by Instagram, used to return the next set in the same series of images. Pass this value into the max_id parameter of the loop to get the next page of results.

username: the Instagram username of the user whose account the image is from
full_name: the full name provided by the user whose account the image is from
profile_picture: URL to the profile image of the user
website: the website URL provided by the user whose account the image is from
user_id: the Instagram user ID of the user whose account the image is from

Tags returned in an unsuccessful Expression Engine loop:
status: “false”
error: a string describing the error

# Media by tag

## exp:ig_picpuller:tagged_media

Description:
Get a list of recently tagged media. Note that this media is ordered by when the media was tagged with this tag, rather than the order it was posted. 

For consistency amongst the tags used in Pic Puller, the ExpressionEngine tags use ‘next_max_id’ for pagination. If you refer to the Instagram documentation, you will see references ‘max_tag_id’ is used for pagination. That does not apply to Pic Puller. Those tags are rewritten by the module to be ‘next_max_id’.

Instragram docs page for this function:
http://instagram.com/developer/endpoints/tags/#get_tags_media_recent

Required parameters:
user_id: the Expression Engine user id (not an Instagram user id)

Optional parameters:
limit: an integer that determines how many images to return. Maximum of 32 allowed by Instagram.
use_stale_cache: either ‘yes’ or ‘no’ (defaults to ‘yes’ if undefined)
max_id: an integer used to determine pagination of results. (See next_max_id in the ‘Tags returned’ below section for more information.)

Tags returned in a successful Expression Engine loop:
status: “true” or “false” — “true” when valid results returned
media_id: the Instagram unique media ID for the image
created_time: time stamp of image creation time, Unix timestamp formated
link: URL of the images homepage on Instagram
caption: The caption provided by the author. Note, it may be left untitled which will return an empty string.
thumbnail: URL to image, sized 150x150
low_resolution: URL to image, sized 306x306
standard_resolution: URL to image, sized 612x612
latitude: latitude data, if available
longitude: longitude data, if available
next_max_id: an integer, provided by Instagram, used to return the next set in the same series of images. Pass this value into the max_id parameter of the loop to get the next page of results.
user_id: the Instagram user ID of the user whose account the image is from
username: the Instagram username of the user whose account the image is from
profile_picture: URL to the profile image of the user
website: the website URL provided by the user whose account the image is from
full_name: the full name provided by the user whose account the image is from

Tags returned in an unsuccessful Expression Engine loop:
status: “false”
error: a string describing the error

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
status: a string of “true” is returned when data is returned
created_time: time stamp of image creation time, Unix timestamp formatted
link: URL of the images homepage on Instagram
caption: The caption provided by the author. Note, it may be left untitled which will return an empty string.
thumbnail: URL to image, sized 150x150
low_resolution: URL to image, sized 306x306
standard_resolution: URL to image, sized 612x612
latitude: latitude data, if available
longitude: longitude data, if available
username: the Instagram username of the user whose account the image is from
user_id: the Instagram user id of the user whose account the image is from
full_name: the full name provided by the user whose account the image is from
profile_picture: URL to the profile image of the user
website: the website information whose account the image is from, if available

Tags returned in an unsuccessful Expression Engine loop:
status: “false”
error: a string describing the error

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


-----

# 'Pic Puller for Instagram Browser' Fieldtype

As of version 1.0.0, the 'Pic Puller for Instagram Browser' fieldtype is part of Pic Puller.

It's intended use is to be used in conjunction with the Pic Puller 'media' tag pair. The fieldtype will store a media_id from Instagram. The field type assists getting the desired media_id by using a photo browser interface that displays the logged in user's photos.

Since only users who have authorized Pic Puller can show the media browser, users who have not authorized are shown a message indicating they need to authorize before using the media browser. 

The fieldtype requires javascript. 

The fieldtype compatible with Matrix.

There is a single option for the fieldtype. You can use the default instructional language that the fieldtype will automatically include or you may turn it off. 