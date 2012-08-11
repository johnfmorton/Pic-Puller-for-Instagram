<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * ig_picpuller Module Front End File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		John Morton
 * @link		http://picpuller.com
 */

class Ig_picpuller {
	
	public $return_data;
	public $cache_name = 'ig_picpuller';
	public $cache_expired = FALSE;
	public $refresh_time = 15; // in minutes
	public $use_stale;

	// $_currentSite will identify whatever is the current site for use in cases where MSM is being used.
	private $_currentSite;
	private $_appID;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->_currentSite = $this->EE->config->config['site_id'];
		$this->_appID = $this->getAppID();
		$this->_memberID = $this->get_logged_in_user_id();
	}
	
	// ----------------------------------------------------------------

	/**
	 * Beep
	 *
	 * Testing function only, to see that module is working. 
	 *
	 * @access	private
	 * @param	none
	 * @return	string - beeps
	 */

	 public function beep() 
	 {
		$tagdata = $this->EE->TMPL->tagdata;

		if (!$this->applicationExists() ) {
			$variables[] = array(
				'error_type' => 'NoInstagramApp',
				'error_message' => 'There is no application stored in the Expression Engine data base. It appear set up is not complete.',
				'status' => 'false'
			);

			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		};
		
		$this->EE->TMPL->log_item('Pic Puller for Instagram: is installed an returning data. Beep.');
		return "Beep. Beep beep.";
	 }
	
	/**
	 * Front End Authorization
	 *
	 * This function permits users who do not have access to the control panel to authorize Pic Puller
	 *
	 * @access	private
	 * @param	none
	 * @return	string - beeps
	 */

	 public function authorization_link() 
	 {
	 	if (!$this->applicationExists() ) {
			return 'ERROR: There is no Instagram application in the system to authorize.';
		};

	 	if (! $this->EE->session->userdata('member_id')){
	 		return 'ERROR: Only logged in users can authorize this application.';
	 	}
	 	
	 	$loggedInUser = $this->getInstagramId ($this->get_logged_in_user_id());
		
		$fullhtml = $this->EE->TMPL->fetch_param('fullhtml');
		
		if (!$loggedInUser) 
		{
			$this->EE->TMPL->log_item('Pic Puller for Instagram: Generating authorization for front end.');
			
			$redirect_uri = $this->get_auth_url();
			$clientID = $this->getClientID();

			$authorization_link = "https://instagram.com/oauth/authorize/?client_id=$clientID&redirect_uri=$redirect_uri&display=touch&response_type=code";
	
			if ($fullhtml == 'yes')
			{
				$authtext = $this->EE->TMPL->fetch_param('authtext');
				$authclass = $this->EE->TMPL->fetch_param('authclass');
				// if linktext is set in the EE tags, use that instead of the default text
				$authtext  = $authtext ? $authtext : 'Authorize with Instagram';
				$authorization_link = '<a href="'.$authorization_link.'" id="ig_picpuller_authorize" class="'.$authclass.'">'.$authtext.'</a>'.'
				<script>
				$(document).ready(function()
					{
						$("#ig_picpuller_authorize").bind("click", processAuthorization);
						function processAuthorization(e)
						{
							e.preventDefault();
							var theURL = $(this).attr("href");
							window.open(theURL,"ingram_auth","width=400,height=450,left=0,top=100,screenX=0,screenY=100");
							$(window).focus(function() {
							   // user closed the popup window... refresh this page to see if their info was successfully saved
								window.location.reload();
							});
						}
				});
				</script>';
			}
			return $authorization_link;
		}
		else 
		{
			$deauthorization_link= $this->deauthorization_url();

			if ($fullhtml == 'yes')
			{
				$deauthtext = $this->EE->TMPL->fetch_param('deauthtext');
				$deauthclass = $this->EE->TMPL->fetch_param('deauthclass');
				// if linktext is set in the EE tags, use that instead of the default text
				$deauthtext  = $deauthtext ? $deauthtext : 'De-authorize with Instagram';
				$deauthorization_link = '<a href="'.$deauthorization_link.'" id="ig_picpuller_deauthorize" class="'.$deauthclass.'">'.$deauthtext.'</a>'.'
				<script>
				$(document).ready(function()
					{
						$("#ig_picpuller_deauthorize").bind("click", processDeauthorization);
						function processDeauthorization(e)
						{
							e.preventDefault();
							var theURL = $(this).attr("href");
							window.open(theURL,"ingram_auth","width=400,height=300,left=0,top=100,screenX=0,screenY=100");
							$(window).focus(function() {
							   // user closed the popup window... refresh this page to see if their info was successfully saved
								window.location.reload();
							});
						}
				});
				</script>';
			}

			return $deauthorization_link;
		}
/*
		$tagdata = $this->EE->TMPL->tagdata;
		return $this->EE->TMPL->parse_variables($tagdata, $variables);
*/
	 }


	/**
	 * Popular
	 *
	 * Get a list of what media is most popular at the moment on Instagram. 32 image max.
	 * http://instagram.com/developer/endpoints/media/#get_media_popular
	 *
	 * @access	public
	 * @param 	tag param: 'limit', an integer that determines how many images to return (32 is the max number the API will return)
	 * @param 	use_stale_cache:
	 * @return	tag data: username, full_name, profile_picture, created_time, link, caption, low_resolution, thumbnail, standard_resolution, status
	 */
	
	public function popular()
	{
		$tagdata = $this->EE->TMPL->tagdata;
		
		if (!$this->applicationExists() ) {
			$variables[] = array(
				'error_type' => 'NoInstagramApp',
				'error_message' => 'There is no application stored in the Expression Engine data base. It appear set up is not complete.',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		};
		
		$this->use_stale = $this->EE->TMPL->fetch_param('use_stale_cache', 'yes');
	
		$variables = array();
				
		$client_id = $this->getClientID();
		
		$limit = $this->EE->TMPL->fetch_param('limit');
		
		
		if(isset($limit))
		{
			$limit = "&count=$limit";
		}
		
		$query_string ="https://api.instagram.com/v1/media/popular?client_id=$client_id". $limit;
		
		$data = $this->_fetch_data($query_string);
		
		if ($data['status'] === FALSE ) { // && $this->use_stale != 'yes') {
			$variables[] = array(
				'error_type' => $data['error_type'],
				'error_message' => $data['error_message'],
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}

		foreach($data['data'] as $node)
		{
			$variables[] = array(
				'username' => $node['user']['username'],
				'full_name' => $node['user']['full_name'],
				'profile_picture' => $node['user']['profile_picture']['url'],
				'created_time' => $node['created_time'],
				'link' => $node['link'],
				'caption' => $node['caption']['text'],
				'low_resolution' => $node['images']['low_resolution']['url'],
				'thumbnail' => $node['images']['thumbnail']['url'],
				'standard_resolution' => $node['images']['standard_resolution']['url'],
				'latitude' => isset($node['location']['latitude']) ? $node['location']['latitude'] : '',
				'longitude' => isset($node['location']['longitude']) ? $node['location']['longitude'] : '',
				'media_id' => $node['id'],
				'status' => 'true'
			);
		}
		return $this->EE->TMPL->parse_variables($tagdata, $variables);
		
	}

	/**
	 * User
	 *
	 * Get the user information from a specified EE user that has authorized the Instagram application 
	 * http://instagram.com/developer/endpoints/users/#get_users
	 *
	 * @access	private
	 * @param	tag param, 'user_id', the EE member ID of a user that has authorized the Instagram application
	 * @param 	use_stale_cache:
	 * @return	tag data, username, bio, profile_picture, website, full_name, counts_media, counts_followed_by, counts_follows, id, status
	 */
		
	public function user()
	{
		$tagdata = $this->EE->TMPL->tagdata;
		
		if (!$this->applicationExists() ) {
			$variables[] = array(
				'error_type' => 'NoInstagramApp',
				'error_message' => 'There is no application stored in the Expression Engine data base. It appear set up is not complete.',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		};
		$this->EE->TMPL->log_item('Pic Puller: user');
		$this->use_stale = $this->EE->TMPL->fetch_param('use_stale_cache', 'yes');
		$variables = array();
	
		$user_id = $this->EE->TMPL->fetch_param('user_id');
		
		if($user_id == '') 
		{
			//return "ERROR: No user ID set for this function";
			$variables[] = array(
				'error_type' => 'MissingReqParameter',
				'error_message' => 'No user ID set for this function',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}
		
		$oauth = $this->getAuthCredsForUser($user_id);
		
		if(!$oauth) 
		{
			//return "ERROR: No user ID set for this function";
			$variables[] = array(
				'error_type' => 'UnauthorizedUser',
				'error_message' => 'User has not authorized Pic Puller for access to Instagram.',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}
		
		$query_string = "https://api.instagram.com/v1/users/self?access_token={$oauth}";
		
		$data = $this->_fetch_data($query_string);
		
		if ($data['status'] === FALSE ) { // && $this->use_stale != 'yes') {
			$variables[] = array(
				'error_type' => $data['error_type'],
				'error_message' => $data['error_message'],
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}
		
		$node = $data['data'];
		$variables[] = array(
			'username' => $node['username'],
			'bio' => $node['bio'],
			'profile_picture' => $node['profile_picture'],
			'website' => $node['website'],
			'full_name' => $node['full_name'],
			'counts_media' => strval($node['counts']['media']),
			'counts_followed_by' => strval($node['counts']['followed_by']),
			'counts_follows' => strval($node['counts']['follows']),
			'id' => $node['id'],
			'status' => 'true'
		);
		return $this->EE->TMPL->parse_variables($tagdata, $variables);

	}

	/**
	 * Media 
	 *
	 * Get information about a single media object. 
	 * http://instagram.com/developer/endpoints/media/#get_media
	 *
	 * @access	public
	 * @param	tag param: 'user_id', the EE member ID of a user that has authorized the Instagram application
	 * @param 	tag param: 'media_id', the Instagram media ID of the image to be returned
	 * @param 	use_stale_cache:
	 * @return	tag data: status, username, user_id, full_name, profile_picture, website, created_time, link, caption, low_resolution, thumbnail, standard_resolution, latitude, longitude, likes
	 */
	 public function media()
	 {
	 	$tagdata = $this->EE->TMPL->tagdata;
		
		if (!$this->applicationExists() ) {
			$variables[] = array(
				'error_type' => 'NoInstagramApp',
				'error_message' => 'There is no application stored in the Expression Engine data base. It appear set up is not complete.',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		};
		
		$this->use_stale = $this->EE->TMPL->fetch_param('use_stale_cache', 'yes');
		
		$variables = array();
		$user_id = $this->EE->TMPL->fetch_param('user_id');
		$media_id = $this->EE->TMPL->fetch_param('media_id');
		
		if($user_id == '') 
		{
			//return "ERROR: No user ID set for this function";
			$variables[] = array(
				'error_type' => 'MissingReqParameter',
				'error_message' => 'No user ID set for this function',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}

		if($media_id == '') 
		{
			//return "ERROR: No user ID set for this function";
			$variables[] = array(
				'error_type' => 'MissingReqParameter',
				'error_message' => 'No media_id set for this function',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}

		$oauth = $this->getAuthCredsForUser($user_id);

		$query_string = "https://api.instagram.com/v1/media/{$media_id}?access_token={$oauth}";
		
		$data = $this->_fetch_data($query_string);

		// echo '404 : 
		// <pre>';
		// var_dump($data);
		// echo '</pre>';


		if ($data['status'] === FALSE) {
			$variables[] = array(
				'error_type' => $data['error_type'],
				'error_message' => $data['error_message'],
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}

		$node = $data['data'];

		$variables[] = array(
			'username' => $node['user']['username'],
			'user_id' => $node['user']['id'],
			'full_name' => $node['user']['full_name'],
			'profile_picture' => $node['user']['profile_picture'],
			'website' => $node['user']['website'],
			'created_time' => $node['created_time'],
			'link' => $node['link'],
			'caption' => $node['caption']['text'],
			'low_resolution' => $node['images']['low_resolution']['url'],
			'thumbnail' => $node['images']['thumbnail']['url'],
			'standard_resolution' => $node['images']['standard_resolution']['url'],
			'latitude' => isset($node['location']['latitude']) ? $node['location']['latitude'] : '',
			'longitude' => isset($node['location']['longitude']) ? $node['location']['longitude'] : '',
			'likes' => $node['likes']['count'],
			'status' => 'true'
		);
		
		return $this->EE->TMPL->parse_variables($tagdata, $variables);
	 }


	/**
	 * Media Recent
	 *
	 * Get the most recent media published from a specified EE user that has authorized the Instagram application 
	 * http://instagram.com/developer/endpoints/users/#get_users_media_recent
	 *
	 * @access	public
	 * @param	tag param: 'user_id', the EE member ID of a user that has authorized the Instagram application
	 * @param 	tag param: 'limit', an integer that determines how many images to return
	 * @param 	use_stale_cache:
	 * @return	tag data: caption, media_id, next_max_id, low_resolution, thumbnail, standard_resolution, latitude, longitude, link, created_time
	 */

	public function media_recent()
	{
		$tagdata = $this->EE->TMPL->tagdata;
		
		if (!$this->applicationExists() ) {
			$variables[] = array(
				'error_type' => 'NoInstagramApp',
				'error_message' => 'There is no application stored in the Expression Engine data base. It appear set up is not complete.',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		};
		
		$this->use_stale = $this->EE->TMPL->fetch_param('use_stale_cache', 'yes');
		
		$variables = array();
		$user_id = $this->EE->TMPL->fetch_param('user_id');
		$limit = $this->EE->TMPL->fetch_param('limit');
		
		if(isset($limit))
		{
			$limit = "&count=$limit";
		}
		
		$min_id = $this->EE->TMPL->fetch_param('min_id');
		
		if(isset($min_id))
		{
			$min_id = "&min_id=$min_id";
		}
		
		
		$max_id = $this->EE->TMPL->fetch_param('max_id');
		if(isset($max_id))
		{
			$max_id = "&max_id=$max_id";
		}
		
		// Report error to user since user_id is required
		
		if($user_id == '') 
		{
			//return "ERROR: No user ID set for this function";
			$variables[] = array(
				'error_type' => 'MissingReqParameter',
				'error_message' => 'No user ID set for this function',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}
		
		$ig_user_id = $this->getInstagramId($user_id);
		$oauth = $this->getAuthCredsForUser($user_id);
		
		if(!$ig_user_id) 
		{
			$variables[] = array(
				'error_type' => 'UnauthorizedUser',
				'error_message' => 'User has not authorized Pic Puller for access to Instagram.',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}	
		
		$query_string = "https://api.instagram.com/v1/users/{$ig_user_id}/media/recent/?access_token={$oauth}". $limit.$max_id.$min_id;
		
		$data = $this->_fetch_data($query_string);
		
		if ($data['status'] === FALSE ) { // && $this->use_stale != 'yes') {
			$variables[] = array(
				'error_type' => $data['error_type'],
				'error_message' => $data['error_message'],
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}

		$node = $data['data'];
		
		$next_max_id = '';
		if (isset($data['pagination']['next_max_id'])){
			$next_max_id = $data['pagination']['next_max_id']; 
		}
		
		foreach($data['data'] as $node)
		{
			$variables[] = array(
				'created_time' => $node['created_time'],
				'link' => $node['link'],
				'caption' => $node['caption']['text'],
				'low_resolution' => $node['images']['low_resolution']['url'],
				'thumbnail' => $node['images']['thumbnail']['url'],
				'standard_resolution' => $node['images']['standard_resolution']['url'],
				'latitude' => isset($node['location']['latitude']) ? $node['location']['latitude'] : '',
				'longitude' => isset($node['location']['longitude']) ? $node['location']['longitude'] : '',
				'media_id' => $node['id'],
				'next_max_id' => $next_max_id,
				'likes' => $node['likes']['count'], 
				'status' => 'true'
			);
		}
		return $this->EE->TMPL->parse_variables($tagdata, $variables);
	}
	
	/**
	 * User Feed
	 *
	 * Get the feed of a specified EE user that has authorized the Instagram application 
	 * http://instagram.com/developer/endpoints/users/#get_users_feed
	 *
	 * @access	public
	 * @param	tag param: 'user_id', the EE member ID of a user that has authorized the Instagram application
	 * @param 	tag param: 'limit', an integer that determines how many images to return
	 * @param 	use_stale_cache:
	 * @return	tag data: caption, media_id, next_max_id, low_resolution, thumbnail, standard_resolution, latitude, longitude, link, created_time, profile_picture, username, website, full_name, user_id
	 */
	
	public function user_feed()
	{
		$tagdata = $this->EE->TMPL->tagdata;
		
		if (!$this->applicationExists() ) {
			$variables[] = array(
				'error_type' => 'NoInstagramApp',
				'error_message' => 'There is no application stored in the Expression Engine data base. It appear set up is not complete.',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		};
		
		$this->use_stale = $this->EE->TMPL->fetch_param('use_stale_cache', 'yes');
		
		$variables = array();
		$user_id = $this->EE->TMPL->fetch_param('user_id');
		$limit = $this->EE->TMPL->fetch_param('limit');
		
		if(isset($limit))
		{
			$limit = "&count=$limit";
		}
		
		$min_id = $this->EE->TMPL->fetch_param('min_id');
		
		if(isset($min_id))
		{
			$min_id = "&min_id=$min_id";
		}
		
		$max_id = $this->EE->TMPL->fetch_param('max_id');
		if(isset($max_id))
		{
			$max_id = "&max_id=$max_id";
		}
		
		// Report error to user since user_id is required
		
		if($user_id == '') 
		{
			//return "ERROR: No user ID set for this function";
			$variables[] = array(
				'error_type' => 'MissingReqParameter',
				'error_message' => 'No user ID set for this function',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}
		
		$ig_user_id = $this->getInstagramId($user_id);
		$oauth = $this->getAuthCredsForUser($user_id);
		
		if(!$ig_user_id) 
		{
			//return "ERROR: No user ID set for this function";
			$variables[] = array(
				'error_type' => 'UnauthorizedUser',
				'error_message' => 'User has not authorized Pic Puller for access to Instagram.',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}
		
		$query_string = "https://api.instagram.com/v1/users/self/feed?access_token={$oauth}". $limit.$max_id.$min_id;
		
		$data = $this->_fetch_data($query_string);
		
		if ($data['status'] === FALSE ) { // && $this->use_stale != 'yes') {
			$variables[] = array(
				'error_type' => $data['error_type'],
				'error_message' => $data['error_message'],
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}

		$node = $data['data'];
		//$next_url = isset($data['pagination']['next_url']) ? $data['pagination']['next_url'] : 'no';
		
		$next_max_id = '';
		if (isset($data['pagination']['next_max_id'])){
			$next_max_id = $data['pagination']['next_max_id']; 
		}		
		
		foreach($data['data'] as $node)
		{
			$variables[] = array(
				'created_time' => $node['created_time'],
				'link' => $node['link'],
				'caption' => $node['caption']['text'],
				'low_resolution' => $node['images']['low_resolution']['url'],
				'thumbnail' => $node['images']['thumbnail']['url'],
				'standard_resolution' => $node['images']['standard_resolution']['url'],
				'latitude' => isset($node['location']['latitude']) ? $node['location']['latitude'] : '',
				'longitude' => isset($node['location']['longitude']) ? $node['location']['longitude'] : '',
				'media_id' => $node['id'],
				'next_max_id' => $next_max_id, 
				'profile_picture' => $node['user']['profile_picture'],
				'username' => $node['user']['username'],
				'website' => $node['user']['website'],
				'full_name' => $node['user']['full_name'],
				'user_id' => $node['user']['id'],
				'status' => 'true'
			);
		}
		return $this->EE->TMPL->parse_variables($tagdata, $variables);
	}
	
	/**
	 * User Liked
	 *
	 * Get liked media of a specified EE user that has authorized the Instagram application 
	 * http://instagram.com/developer/endpoints/users/#get_users_liked_feed
	 *
	 * @access	public
	 * @param	tag param: 'user_id', the EE member ID of a user that has authorized the Instagram application
	 * @param 	tag param: 'limit', an integer that determines how many images to return
	 * @param 	use_stale_cache:
	 * @return	tag data: caption, media_id, next_max_id, low_resolution, thumbnail, standard_resolution, latitude, longitude, link, created_time, profile_picture, username, website, full_name, user_id
	 */
	
	public function user_liked()
	{
		$tagdata = $this->EE->TMPL->tagdata;
		
		if (!$this->applicationExists() ) {
			$variables[] = array(
				'error_type' => 'NoInstagramApp',
				'error_message' => 'There is no application stored in the Expression Engine data base. It appear set up is not complete.',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		};
		
		$this->use_stale = $this->EE->TMPL->fetch_param('use_stale_cache', 'yes');
		
		$variables = array();
		$user_id = $this->EE->TMPL->fetch_param('user_id');
		$limit = $this->EE->TMPL->fetch_param('limit');
		
		if(isset($limit))
		{
			$limit = "&count=$limit";
		}
		
		$min_id = $this->EE->TMPL->fetch_param('min_id');
		
		if(isset($min_id))
		{
			$min_id = "&min_id=$min_id";
		}
		
		
		$max_id = $this->EE->TMPL->fetch_param('max_id');
		if(isset($max_id))
		{
			$max_id = "&max_like_id=$max_id";
		}
		
		// Report error to user since user_id is required
		
		if($user_id == '') 
		{
			//return "ERROR: No user ID set for this function";
			$variables[] = array(
				'error_type' => 'MissingReqParameter',
				'error_message' => 'No user ID set for this function',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}
		
		$ig_user_id = $this->getInstagramId($user_id);
		$oauth = $this->getAuthCredsForUser($user_id);
		$query_string = "https://api.instagram.com/v1/users/self/media/liked?access_token={$oauth}". $limit.$max_id.$min_id;
		
		$data = $this->_fetch_data($query_string);
		
		if ($data['status'] === FALSE ) { // && $this->use_stale != 'yes') {
			$variables[] = array(
				'error_type' => $data['error_type'],
				'error_message' => $data['error_message'],
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}

		$node = $data['data'];
		$next_max_id = '';
		if (isset($data['pagination']['next_max_like_id'])){
			$next_max_id = $data['pagination']['next_max_like_id'];
		}
		
		foreach($data['data'] as $node)
		{
			$variables[] = array(
				'created_time' => $node['created_time'],
				'link' => $node['link'],
				'caption' => $node['caption']['text'],
				'low_resolution' => $node['images']['low_resolution']['url'],
				'thumbnail' => $node['images']['thumbnail']['url'],
				'standard_resolution' => $node['images']['standard_resolution']['url'],
				'latitude' => isset($node['location']['latitude']) ? $node['location']['latitude'] : '',
				'longitude' => isset($node['location']['longitude']) ? $node['location']['longitude'] : '',
				'media_id' => $node['id'],
				'next_max_id' => $next_max_id,
				'profile_picture' => $node['user']['profile_picture'],
				'username' => $node['user']['username'],
				'website' => $node['user']['website'],
				'full_name' => $node['user']['full_name'],
				'user_id' => $node['user']['id'],
				'status' => 'true'
			);
		}
		return $this->EE->TMPL->parse_variables($tagdata, $variables);
	}
	
	/**
	 * Recent Media by Tag
	 *
	 * Get a list of recently tagged media. Note that this media is ordered by when the media was tagged with this tag, rather than the order it was posted. 
	 * http://instagram.com/developer/endpoints/tags/#get_tags_media_recent
	 *
	 * @access	public
	 * @param	tag param: 'user_id', the EE member ID of a user that has authorized the Instagram application
	 * @param 	tag param: 'limit', an integer that determines how many images to return
	 * @param 	use_stale_cache:
	 * @return	tag data: caption, media_id, next_max_id, low_resolution, thumbnail, standard_resolution, latitude, longitude, link, created_time, profile_picture, username, website, full_name, user_id
	 */
	
	public function tagged_media()
	{
		$tagdata = $this->EE->TMPL->tagdata;
		
		if (!$this->applicationExists() ) {
			$variables[] = array(
				'error_type' => 'NoInstagramApp',
				'error_message' => 'There is no application stored in the Expression Engine data base. It appear set up is not complete.',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		};
		
		$this->use_stale = $this->EE->TMPL->fetch_param('use_stale_cache', 'yes');
		
		$variables = array();
		$user_id = $this->EE->TMPL->fetch_param('user_id');
		$limit = $this->EE->TMPL->fetch_param('limit');
		$tag = $this->EE->TMPL->fetch_param('tag');
		
		if(isset($limit))
		{
			$limit = "&count=$limit";
		}
		
		$min_id = $this->EE->TMPL->fetch_param('min_id');
		
		if(isset($min_id))
		{
			$min_id = "&min_id=$min_id";
		}
		
		$max_id = $this->EE->TMPL->fetch_param('max_id');
		if(isset($max_id))
		{
			$max_id = "&max_id=$max_id";
		}
		
		// Report error to user since user_id is required
		
		if($user_id == '') 
		{
			//return "ERROR: No user ID set for this function";
			$variables[] = array(
				'error_type' => 'MissingReqParameter',
				'error_message' => 'No user ID set for this function',
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}
		
		if($tag == '')
		{
			return "ERROR: No tag set for this function";
		}
		
		$ig_user_id = $this->getInstagramId($user_id);
		$oauth = $this->getAuthCredsForUser($user_id);
		$query_string = "https://api.instagram.com/v1/tags/$tag/media/recent?access_token={$oauth}". $limit.$max_id.$min_id;
		
		$data = $this->_fetch_data($query_string);
		
		if ($data['status'] === FALSE ) { // && $this->use_stale != 'yes') {
			$variables[] = array(
				'error_type' => $data['error_type'],
				'error_message' => $data['error_message'],
				'status' => 'false'
			);
			
			return $this->EE->TMPL->parse_variables($tagdata, $variables);
		}

		$node = $data['data'];
		
		$next_max_id = '';
		if (isset($data['pagination']['next_max_tag_id'])){
			$next_max_id = $data['pagination']['next_max_tag_id'];
		}
		/*
		echo '<pre>';
		var_dump($data['pagination']);
		echo '</pre>';
		*/
		foreach($data['data'] as $node)
		{
			$variables[] = array(
				'created_time' => $node['created_time'],
				'link' => $node['link'],
				'caption' => $node['caption']['text'],
				'low_resolution' => $node['images']['low_resolution']['url'],
				'thumbnail' => $node['images']['thumbnail']['url'],
				'standard_resolution' => $node['images']['standard_resolution']['url'],
				'latitude' => isset($node['location']['latitude']) ? $node['location']['latitude'] : '',
				'longitude' => isset($node['location']['longitude']) ? $node['location']['longitude'] : '',
				'media_id' => $node['id'],
				'next_max_id' => $next_max_id,
				'profile_picture' => $node['user']['profile_picture'],
				'username' => $node['user']['username'],
				'website' => $node['user']['website'],
				'full_name' => $node['user']['full_name'],
				'user_id' => $node['user']['id'],
				'status' => 'true'
			);
		}
		return $this->EE->TMPL->parse_variables($tagdata, $variables);
	}
	
	// The authorization function is access via an ACTION_ID to authenticate a user and generate an oAuth code
	
	public function authorization()
	{
		parse_str($_SERVER['QUERY_STRING'], $_GET);

		if (isset($_GET["code"]) && $_GET["code"] != ''){
			$user_data = $this->getOAuthFromCode($_GET["code"]);
		}

		if (isset($user_data->{'access_token'})){
			
			//echo "<pre>";
			//var_dump($this->_memberID);
			//echo "</pre>";

			$this->remove_auth_logged_in_user();
			$this->EE->db->set('oauth', $user_data->{'access_token'});
			// originally, I saved the member id from Instagram, but I switched that to saving
			// the member_id of the EE user this authorization is associated with the app		
			$this->EE->db->set('instagram_id', $user_data->{'user'}->id);
			
			$this->EE->db->set('member_id', $this->get_logged_in_user_id());

			$this->EE->db->set('app_id', $this->_appID);
			
			$this->EE->db->insert('ig_picpuller_oauths');
			
			$message =  "Success! Your Instagram app now has access to your photostream.";

			$response = "success";	
		} elseif (isset($_GET['error_description'])) {
			$response =  "definedError";
			$message = $_GET['error_description'];
		} else {
			$response =  "error";
			$message = '';
		}
				
		switch ($response)
		{
			case 'success':
				$this->showResult("Success", "You have authorized this site to access your Instagram photos.");
			break;
			case 'definedError':
			$this->showResult("Error", $message);
			break;

			case 'error':
			// echo "<pre>";
			// print_r($_GET);
			// echo "</pre>";
			$this->showResult("Error", "An error occurred in the authorization process with Instagram. No oAuth code was returned.<br><br>One cause of this type of error is the password not being identical in ExpressionEngine to the Instagram secret.<br><br>Another cause can be the Instagram API not responding as expected. Is the API operating normally? You can check at, <a href=\"http://api-status.com/6404/174981/Instagram-API\" target='_blank'>API Status</a>");
			break;
			
			default:
				$this->showResult("Error: No IG 'code' found.", "An error occurred in the authorization process with Instagram. <br><br>Is the API up currently? You can check at, <a href=\"http://api-status.com/6404/174981/Instagram-API\" target='_blank'>API Status</a>");
			break;
		}
	}

	// The authorization function is access via an ACTION_ID to authenticate a user and generate an oAuth code. Below is the deauthorization Action ID. This is only used by the 'exp:authorization_link' EE tab when a logged in user is already authorized with Instagram. The authorization button becomes a deauthorization button.
	
	public function deauthorization()
	{
		$this->remove_auth_logged_in_user();
		$this->showResult("Authorization Removed", "You have removed authorization to access your Instagram photos. You may reauthorize again later. The application will still appear in your Instagram applications on Instagram.com but may be removed.");
	}
	
	// Below are all "helper" functions
	// They are all PRIVATE to this class
	
	/**
	 * Get Authorization from Code
	 *
	 * Get the authorization credentials from the Pic Puller API based on code in second part of oAuth validation process
	 *
	 * @access	private
	 * @param	string - code, which is provided by Instagram in first step of a user authorizing with Instagram for an application
	 * @return	array
	 */
	
	private function getOAuthFromCode($code)
	{
		$urltopost = "https://api.instagram.com/oauth/access_token";
		
		$datatopost = array(
			'client_id'=>$this->getClientID(), 
			'client_secret'=>$this->getSecret(), 
			'grant_type'=>'authorization_code', 
			'redirect_uri'=> $this->get_auth_url(), 
			'code'=>$code
			);
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $urltopost);
		// to prevent the response from being outputted
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		// POST to the Instagram auth url
		curl_setopt($ch, CURLOPT_POST, 1);
		// adding the post variables to the request
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datatopost);
		// don't verify the SSL cert
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$returndata = curl_exec($ch);
		
		
		if ($returndata === FALSE) {  

		    //echo "cURL Error: " . curl_error($ch);  

		}
		$info = curl_getinfo($ch);  

		//echo '<br><br>Took ' . $info['total_time'] . ' seconds for url ' . $info['url'].'<br><br>';
		
		curl_close($ch);
		
		$json = json_decode($returndata);

		return $json;
	}
	
	/**
	 * Get Authorization Credentials for an EE user
	 *
	 * Get the authorization credentials from the Pic Puller oAuths table for a specified Expression Engine user Pic Puller application
	 *
	 * @access	private
	 * @param	string - User ID number for an EE member
	 * @return	mixed - returns Instagram oAuth credentials for a user if available in DB, or FALSE if unavailable
	 */
	
	private function getAuthCredsForUser($user_id)
	{
		$this->EE->db->select('oauth');
		$this->EE->db->where("member_id = " . $user_id );
		$this->EE->db->where('app_id', $this->_appID);
		$this->EE->db->limit(1);
		$query = $this->EE->db->get('ig_picpuller_oauths');

		foreach ($query->result() as $row)
		{
    		$oauth = $row->oauth;
		}
		if (isset($oauth)){
			return $oauth;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get Client ID
	 *
	 * Get the client ID from the Pic Puller Credentials table for the existing Pic Puller application
	 *
	 * @access	private
	 * @return	mixed - returns Instagram client ID if available in DB, or FALSE if unavailable
	 */
	
	private function getClientID()
	{
		$this->EE->db->select('ig_client_id');
		$this->EE->db->where('ig_site_id', $this->_currentSite);
		$this->EE->db->limit(1);
		$query = $this->EE->db->get('ig_picpuller_credentials');

		foreach ($query->result() as $row)
		{
    		$ig_client_id = $row->ig_client_id;
		}
		if (isset($ig_client_id))
		{
			return $ig_client_id;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get Instagram App ID
	 *
	 * Get the app ID from the Pic Puller Credentials table for the existing Pic Puller application for the active site
	 *
	 * @access	private
	 * @return	mixed - returns Instagram app ID if available in DB, or FALSE if unavailable
	 */
	
	private function getAppID()
	{
		$this->EE->db->select('app_id');
		$this->EE->db->where('ig_site_id', $this->_currentSite);
		$this->EE->db->limit(1);
		$query = $this->EE->db->get('ig_picpuller_credentials');

		foreach ($query->result() as $row)
		{
    		$app_id = $row->app_id;
		}
		if (isset($app_id))
		{
			return $app_id;
		} else {
			return FALSE;
		}
	}


	/**
	 * Get Secret
	 *
	 * Get the secret (aka password) from the Pic Puller Credentials table for the existing Pic Puller application
	 *
	 * @access	private
	 * @return	mixed - returns Instagram Secret (aka redirect) if available in DB, or FALSE if unavailable
	 */
	
	private function getSecret()
	{
		$this->EE->db->select('ig_client_secret');
		$this->EE->db->where('ig_site_id', $this->_currentSite);
		$this->EE->db->limit(1);
		$query = $this->EE->db->get('ig_picpuller_credentials');

		foreach ($query->result() as $row)
		{
    		$ig_client_secret = $row->ig_client_secret;
		}
		if (isset($ig_client_secret)){
			return $ig_client_secret;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get Authorization URL
	 *
	 * Get the authorization URL from the Pic Puller Credentials table for the existing Pic Puller application
	 *
	 * @access	private
	 * @return	mixed - returns Instagram Authorization (aka redirect) URL if available in DB, or FALSE if unavailable
	 */
	
	private function get_auth_url()
	{
		$this->EE->db->select('auth_url');
		$this->EE->db->where('ig_site_id', $this->_currentSite);
		$this->EE->db->limit(1);
		$query = $this->EE->db->get('ig_picpuller_credentials');

		foreach ($query->result() as $row)
		{
    		$auth_url = $row->auth_url;
		}
		if (isset($auth_url)){
			return $auth_url;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get Logged in user ID
	 *
	 * Get logged in user id for an EE member
	 *
	 * @access	private
	 * @return	string - member ID
	 */
	
	public function get_logged_in_user_id()
	{
		$this->EE->load->library('session');
		return $this->EE->session->userdata['member_id'];
		//return $this->EE->session;
	}
	
	/**
	 * Get Instagram ID
	 *
	 * Get Instagram ID for an EE member ID
	 *
	 * @access	private
	 * @param	string - User ID number for an EE member
	 * @return	mixed - returns Instagram ID if available in DB, or FALSE if unavailable
	 */
	
	private function getInstagramId($user_id)
	{
		$this->EE->db->select('instagram_id');
		$this->EE->db->where("member_id = " . $user_id );
		$this->EE->db->where('app_id', $this->_appID);
		$this->EE->db->limit(1);
		$query = $this->EE->db->get('ig_picpuller_oauths');

		foreach ($query->result() as $row)
		{
    		$instagram_id = $row->instagram_id;
		}
		if (isset($instagram_id)){
			return $instagram_id;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Remove Authorization of Logged In User
	 *
	 * Remove the logged in users oAuth credentials from the database
	 *
	 * @access	private
	 * @return	NULL
	 */
	
	private function remove_auth_logged_in_user()
	{
		// TO DO : remove the select * - not needed, but want to test first, so I've left it in for this version.

		$this->EE->db->select('*');
		$this->EE->db->limit('1');
		$this->EE->db->where('member_id', $this->get_logged_in_user_id() );
		$this->EE->db->where('app_id', $this->_appID);
		$this->EE->db->delete('ig_picpuller_oauths'); 
	}
	
	/**
	 * Fetch Data
	 *
	 * Using CURL, fetch requested Instagram URL and return with validated data
	 *
	 * @access	private
	 * @param	string - a full Instagram API call URL
	 * @return	array - the original data or cached data (if stale allowed) with the error array
	 */
	
	private function _fetch_data($url)
	{
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		// to prevent the response from being outputted
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		// POST to the Instagram auth url
		//curl_setopt($ch, CURLOPT_POST, 1);
		// adding the post variables to the request
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $datatopost);
		// don't verify the SSL cert
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$data = json_decode(curl_exec($ch), true);
		curl_close($ch);
		
		/*

		Digging around? Uncomment this out to see all the goodies returned by Instagram.

		echo '<pre>';
		var_dump($data);
		echo '</pre>';
		*/

		$valid_data = $this->_validate_data($data, $url);		
		return $valid_data;

	}
	
	/**
	 * Validate Data
	 *
	 * Validate that data coming in from an Instagram API call is valid data and respond with that data plus error_state details
	 *
	 * @access	private
	 * @param	string - the data to validate
	 * @param	string - the URL that generated that data
	 * @return	array - the original data or cached data (if stale allowed) with the error array
	 */
	
	private function _validate_data($data, $url){
		
		// to FAKE a non-responsive error from Instagram, change the initial conditional statement below
		
		if ($data != '')
		{
			$error_array;
			$meta = $data['meta'];	
			// 200 means IG api did respond with good data
			if ($meta['code'] == 200)
			{
				// There is an outlying chance that IG says 200, but the data array is empty.
				// Pic Puller considers that an error so we return a custom error message
				if(count($data['data']) == 0) {
					$error_array = array(
						'status' => FALSE,
						'error_message' => "There were no photos to return for that user.",
						'error_type' => 'NoData'
					);
				} 
				else
				{
					$error_array = array(
						'status' => TRUE,
						'error_message' => "Nothing wrong here. Move along.",
						'error_type' => 'NoError'
					);
					// Fresher valid data was received, so update the cache to reflect that.
					$this->_write_cache($data, $url);
				} 
			} 
			else // this ELSE loop is only executed when the meta['code'] is not 200, typically, it's a 400
			{
				// even though there is an error, we can use old data if
				// use_stale is set to YES. Here the $data passed in is replace with
				// a cache version of itself if available
				if ($this->use_stale == 'yes') 
				{
					$data = $this->_check_cache($url, $this->use_stale);

					if ($data) {

						$error_array = array(
							'status' => TRUE,
							'error_message' => (isset($meta['error_message']) ? $meta['error_message'] : 'Using cached data.' ), //. ' Using stale data as back up if available.',
							'error_type' =>  (isset($meta['error_type']) ? $meta['error_type'] : 'NoCodeReturned')
						);
					} 
					else 
					{

						$error_array = array(
							'status' => FALSE,
							'error_message' => (isset($meta['error_message']) ? $meta['error_message'] : 'No error message provided by Instagram.' ),
							'error_type' =>  (isset($meta['error_type']) ? $meta['error_type'] : 'NoCodeReturned')
						);

						return $error_array;
					}
				}
			}
			
		} 
		else // The was no response at all from Instagram, so make a custom error message.
		{
			
			if ($this->use_stale == 'yes') 
			{
				$data = $this->_check_cache($url, $this->use_stale);
			}
			
			
			
			$error_array = array (
				'error_message' => 'No data returned from Instagram API. Check http://api-status.com/6404/174981/Instagram-API.',
				'error_type' => 'NoResponse',
				'status' => FALSE
				);

		}
		return array_merge($data, $error_array);
		// merge the original data or cached data (if stale allowed) with the error array		
	}
	
	private function showResult($headline, $message) {
		echo "<!DOCTYPE HTML>
		<html lang=\"en-US\">
		<head>
			<meta charset=\"UTF-8\">
			<title>$headline</title>
			<style type=\"text/css\">
				body, html {
					background-color: #ecf1f4;
					color:  #5F6C74;
					margin: 0;
					padding: 0;
					font-family: \"Helvetica Neue\", Arial, Helvetica, Geneva, sans-serif;
				}

				h1 {
					font-size: 18px;
					margin: 0;
					background-color: #9badb8;
					padding: 10px 15px;
					color: white;
					text-shadow: 1px 1px 1px #474747;
					font-weight: normal;
					font-style: normal;
				}

				p {
					font-size: 12px;
					margin-left: 15px;
					margin-right: 15px;
				}
			</style>
			
		</head>
		<body>
			<h1 id=\"success\">$headline</h1>
			<div id=\"message\"><p>$message</p>
			<p><button onClick=\"window.close()\">Close this window</button></p>	
			</div>
			<script>
				var auth=location.href.split('access_token=')[1];
				var userid=auth.split('.')[0];
				if (auth != '') {
					// alert('Although the normal oAuth failed, your Instagram ID appears to be: ' + userid + ' \\nYour oAuth for this application : ' + auth + ' \\nThis can be used in the Advanced tab of Pic Puller.');
					var headline = document.getElementById('success');
					var content = document.getElementById('message');
					while( headline.firstChild ) {
						headline.removeChild( headline.firstChild );
					}
					while( content.firstChild ) {
						content.removeChild( content.firstChild );
					}
					var newmessage = '\<p\>Use the following information in the Advanced User Administration screen in the control panel for Pic Puller.<p><p>User ID: <strong>'+userid+'</strong></p><p>oAuth code: <strong>'+auth+'</strong></p>';
					var newP = document.createElement('p');
					newP.innerHTML = newmessage;
					headline.appendChild( document.createTextNode('Alternate Authorization Method') );
					content.appendChild( newP);
				}
			</script>
		</body>
		</html>";
	}
		
	// ---------- FRONT END AUTHORIZATION/ ------------- //


	private function deauthorization_url($urlEncoded = false) 
	{
		return $this->EE->functions->fetch_site_index(0, 0).QUERY_MARKER.'ACT='.$this->EE->functions->fetch_action_id('ig_picpuller', 'deauthorization');
		
	}

	//
	// ---------- /FRONT END AUTHORIZATION ------------- // 



	// ---------- CACHE CONTROL/ ------------- //
	
	/**
	 * Check Cache
	 *
	 * Check for cached data
	 *
	 * @access	private
	 * @param	string
	 * @param	bool	Allow pulling of stale cache file
	 * @return	mixed - string if pulling from cache, FALSE if not
	 */
	private function _check_cache($url, $use_stale = FALSE)
	{	
		// Check for cache directory
				
		$dir = APPPATH.'cache/'.$this->cache_name.'/';
		
		$this->EE->TMPL->log_item('CHECK CASHE: dir, '. $dir);
		
		if ( ! @is_dir($dir))
		{
			$this->EE->TMPL->log_item('CHECK CASHE: directory wasn\'t there');
			return FALSE;
		}
		
		// Check for cache file
		
        $file = $dir.md5($url);
		
		if ( ! file_exists($file) OR ! ($fp = @fopen($file, 'rb')))
		{
			return FALSE;
		}
		       
		flock($fp, LOCK_SH);
                    
		$cache = @fread($fp, filesize($file));
                    
		flock($fp, LOCK_UN);
        
		fclose($fp);

        // Grab the timestamp from the first line

		$eol = strpos($cache, "\n");
		
		$timestamp = substr($cache, 0, $eol);
		$cache = trim((substr($cache, $eol)));
		
		if ($use_stale == FALSE && time() > ($timestamp + ($this->refresh * 60)))
		{
			return FALSE;
		}
		
		$this->EE->TMPL->log_item("Instagram data retrieved from cache");
		
		$cache = json_decode($cache, true);

		return $cache;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Write Cache
	 *
	 * Write the cached data
	 *
	 * @access	private
	 * @param	string
	 * @return	void
	 */
	private function _write_cache($data, $url)
	{
		
		// Check for cache directory
		
		$this->EE->TMPL->log_item('Pic Puller: _write_cache $data '. gettype($data));
		
		$data = json_encode($data);
		
		$dir = APPPATH.'cache/'.$this->cache_name.'/';

		if ( ! @is_dir($dir))
		{
			if ( ! @mkdir($dir, 0777))
			{
				return FALSE;
			}
			
			@chmod($dir, 0777);            
		}
		
		// add a timestamp to the top of the file
		$data = time()."\n".$data;		
		
		// Write the cached data
		
		$file = $dir.md5($url);
	
		if ( ! $fp = @fopen($file, 'wb'))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);
        
		@chmod($file, 0777);

		// now clean up the cache
		$this->_clear_cache();
	}

	private function _clear_cache()
	{
		$file = '*';
		$dir = APPPATH.'cache/'.$this->cache_name.'/';

		$sorted_array = $this->listdir_by_date($dir.$file);

		/*
		echo "<pre>";
		print_r($sorted_array);
		echo "</pre>";
		*/

		$count = count($sorted_array);
		foreach ($sorted_array as $value) {
			if($count > 25 ){
			// unlinking, as in deleting, cache files that are oldest, but keeping 25 most recent
			unlink($dir.$value);
			}
			$count--;
		}
	}

	private function listdir_by_date($pathtosearch)
	{
		foreach (glob($pathtosearch) as $filename)
		{
			$file_array[filectime($filename)]=basename($filename); // or just $filename
		}
		ksort($file_array);
		
		return $file_array;
	}
	

	private function applicationExists() {
		$clientID = $this->getClientID();
	
		if ($clientID == ''){
			return FALSE;
		}		
		return TRUE;
	}
	
	
	// ---------- /CACHE CONTROL ------------- //
}
/* End of file mod.ig_picpuller.php */
/* Location: /system/expressionengine/third_party/ig_picpuller/mod.ig_picpuller.php */