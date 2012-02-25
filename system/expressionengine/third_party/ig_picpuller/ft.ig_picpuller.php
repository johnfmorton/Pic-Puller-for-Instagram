<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ig_picpuller_ft extends EE_Fieldtype {
	
	var $info = array(
		'name'		=> 'Pic Puller for Instagram Browser',
		'version'	=> '1.0.0'
	);
	
	// --------------------------------------------------------------------
	
	/**
	 * Display Field on Publish
	 *
	 * @access	public
	 * @param	existing data
	 * @return	field html
	 *
	 */
	function display_field($data)
	{
		$this->EE->cp->load_package_css('colorbox');
		$this->EE->cp->load_package_js('jquery.colorbox-min');
		$this->EE->cp->load_package_js('jquery-ui-1.8.17.custom.min');
		$this->EE->cp->load_package_js('scripts');

		$pp_theme_views = URL_THIRD_THEMES.'ig_picpuller/views/';

		$this->EE->cp->add_to_head('<style>#cboxLoadingGraphic{background:url('.$pp_theme_views.'images/loading.gif) no-repeat center center;};</style>');

		////////////////
		// Get oAuth  //
		////////////////

		$user_id = $this->EE->session->userdata('member_id');
		$oauth = $this->getAuthCredsForUser($user_id);
		
		//$this->EE->javascript->output(PATH_THIRD."ig_picpuller/js/scripts.js");
		if ($oauth != '') {
			$pp_select = $pp_theme_views.'pp_select.php?access_token='.$oauth.'&target_field='.$this->field_name;

			$input = "Enter an Instagram media_id number or use the <em>Instagram Browser</em> to select one from your Instagram account." . 
				form_input(array(
				'name'  => $this->field_name,
				//'id'    => $this->field_id,
				'id'    => $this->field_name,
				'value' => $data
			))."<br><br><a class='igbrowserbt' href='$pp_select' style='display:none;'>Launch Instagram Browser</a>";

			return $input;
		} 
		else 
		{
			////////////////////////////////////////////////////////////////
			// no oauth means the user has not authorized with Instagram. //
			////////////////////////////////////////////////////////////////

			return "You must authorize this user account with Instagram before you can browse for an image.";
		}	

		/*
		return form_input(array(
			'name'  => $this->field_name,
			'id'    => $this->field_id,
			'value' => $data
		));
		*/
	}
	
	// --------------------------------------------------------------------

	/**
	 * Replace tag
	 *
	 * @access	public
	 * @param	existing data
	 * @return	field html
	 *
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		
	static $script_on_page = FALSE;
	$ret = '';
	
	return $data;

	}
	
	// --------------------------------------------------------------------

	/**
	 * Save Settings
	 *
	 * @access	public
	 * @return	field settings
	 *
	 */
	function save_settings($data)
	{
		return array(
			'ig_media_id'	=> $this->EE->input->post('ig_media_id')
		);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Install Fieldtype
	 *
	 * @access	public
	 * @return	default global settings
	 *
	 */
	function install()
	{	
		// need to pic a good default IG pic
		return array(
			'ig_media_id'	=> ''
		);
	}
	
	// --------------------------------------------------------------------
	
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
	 * Control Panel Javascript
	 *
	 * @access	public
	 * @return	void
	 *
	 */
	/*
	function _cp_js()
	{
		// This js is used on the global and regular settings
		// pages, but on the global screen the map takes up almost
		// the entire screen. So scroll wheel zooming becomes a hindrance.
		
		
		$this->EE->javascript->set_global('gmaps.scroll', ($_GET['C'] == 'content_admin'));
		
		$this->EE->cp->add_to_head('<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>');
		$this->EE->cp->load_package_js('cp');
		
	}
	*/
}

/* End of file ft.google_maps.php */
/* Location: ./system/expressionengine/third_party/google_maps/ft.google_maps.php */