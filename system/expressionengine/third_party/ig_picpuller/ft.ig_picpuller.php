<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ig_picpuller_ft extends EE_Fieldtype {
	
	var $info = array(
		'name'		=> 'Pic Puller for Instagram Browser',
		'version'	=> '1.0.0'
	);

	static $counter = 0;
	
	// --------------------------------------------------------------------
	
	/**
	 * Display Field on Publish
	 *
	 * @access	public
	 * @param	$data existing data from the field
	 * @return	string of HTML field
	 *
	 */
	function display_field($data)
	{
		$this->EE->cp->load_package_css('colorbox');
		$this->EE->cp->load_package_js('jquery.colorbox-min');
		$this->EE->cp->load_package_js('jquery-ui-1.8.17.custom.min');
		$this->EE->cp->load_package_js('scripts');

		$this->EE->lang->loadfile('ig_picpuller');

		$pp_theme_views = URL_THIRD_THEMES.'ig_picpuller/views/';

		$this->EE->cp->add_to_head('<style>#cboxLoadingGraphic{background:url('.$pp_theme_views.'images/loading.gif) no-repeat center center;};</style>');

		////////////////
		// Get oAuth  //
		////////////////

		$user_id = $this->EE->session->userdata('member_id');
		$oauth = $this->getAuthCredsForUser($user_id);
		
		if ($oauth != '') {
			$pp_select = $pp_theme_views.'pp_select.php?access_token='.$oauth; //.'&target_field='.$this->field_name;

			if ($this->settings['display_pp_instructions'] === 'yes') {
				$instructions = '<div class="instruction_text"><p style="margin-left: 1px;">'.lang('default_instructions').'</p></div>';
			} else {
				$instructions = '';
			}

			$input = $instructions . '<br>' .
				form_input(array(
				'name'  => $this->field_name,
				'value' => $data
			))."<br><br><a class='igbrowserbt' href='$pp_select' style='display:none;'>".lang('launch_browser')." &raquo;</a>";

			return $input;
		} 
		else 
		{
			////////////////////////////////////////////////////////////////
			// no oauth means the user has not authorized with Instagram. //
			////////////////////////////////////////////////////////////////

			return lang('unauthorized_field_type_access');
		}	
	}

	/**
	 * Display the Cell for Matrix
	 * @param  $data existing data from the field
	 * @return string of HTML to display in Matrix
	 */
	function display_cell( $data )
	{
		$this->EE->cp->load_package_css('colorbox');
		$this->EE->cp->load_package_js('jquery.colorbox-min');
		$this->EE->cp->load_package_js('jquery-ui-1.8.17.custom.min');
		$this->EE->cp->load_package_js('scripts');

		$this->EE->lang->loadfile('ig_picpuller');

		$pp_theme_views = URL_THIRD_THEMES.'ig_picpuller/views/';

		$this->EE->cp->add_to_head('<style>#cboxLoadingGraphic{background:url('.$pp_theme_views.'images/loading.gif) no-repeat center center;};</style>');

		////////////////
		// Get oAuth  //
		////////////////

		$user_id = $this->EE->session->userdata('member_id');
		$oauth = $this->getAuthCredsForUser($user_id);
		
		if ($oauth != '') {
			
			$pp_select = $pp_theme_views.'pp_select.php?access_token='.$oauth; //.'&target_field='.'id_'.$this->field_id.'_col_'.$this->col_id;

			if ($this->settings['display_pp_instructions'] === 'yes') {
				$instructions = '<div class="instruction_text"><p style="margin-left: 0px;">'.lang('default_instructions').'</p></div>';
			} else {
				$instructions = '';
			}

			$html = $instructions.'<input value="'.$data.'" name="'.$this->cell_name.'" style="width: 90%; padding: 2px; margin: 5px 0;"><br>
				<a class="igbrowserbtmatrix" href="'.$pp_select.'" style="display:none;">'.lang('launch_browser').' &raquo;</a>';
			return $html;
		} 
		else 
		{
			////////////////////////////////////////////////////////////////
			// no oauth means the user has not authorized with Instagram. //
			////////////////////////////////////////////////////////////////

			return lang('unauthorized_field_type_access');
		}	
	}

	/**
	 * Display the global settings for the field type
	 * @return string that is the HTML of the form that lets user alter settings
	 */
	function display_global_settings()
	{
		// load the language file
		$this->EE->lang->loadfile('ig_picpuller');

		// load the table library
		$this->EE->load->library('table');

		$val = array_merge($this->settings, $_POST);

		$display_pp_instructions = $val['display_pp_instructions'];

		$checked = TRUE; 

		if ($display_pp_instructions === 'no') {
			$checked = FALSE;
		}

		$radio1 = array(
			'name' => 'display_pp_instructions',
			'value' => 'yes',
			'checked' => $checked
		);

		$radio2 = array(
			'name' => 'display_pp_instructions',
			'value' => 'no',
			'checked' => !$checked
		);

		$this->EE->table->set_template(array(
			'table_open'    => '<table class="mainTable padTable" border="0" cellspacing="0" cellpadding="0">',
			'row_start'     => '<tr class="even">',
			'row_alt_start' => '<tr class="odd">'
		));

		$this->EE->table->set_heading(array('data' => lang('preference'), 'style' => 'width: 50%'), lang('setting'));

		$this->EE->table->add_row(
			lang('display_instructions_option_text', 'display_instructions_option_text'),
			 'Yes: '.form_radio($radio1).NBS.' No: '.form_radio($radio2)
			 'Yes: '.form_radio($radio1).NBS.NBS.' No: '.form_radio($radio2)
		);

		return $this->EE->table->generate();

	}

	/**
	 * Saves the global settings
	 * @return an array of the settings
	 */
	function save_global_settings()
	{
		return array_merge($this->settings, $_POST);
	}

	/**
	 * Display settings for an individual instance of a Pic Puller fieldtype
	 * @param  $data existing settings for this fieldtype
	 * @return string that is the HTML of the form that lets user alter settings
	 */
	function display_settings($data)
	{
		$this->EE->lang->loadfile('ig_picpuller');
		$display_pp_instructions = isset($data['display_pp_instructions']) ? $data['display_pp_instructions'] : $this->settings['display_pp_instructions'];

		$checked = TRUE; 

		if ($display_pp_instructions === 'no') {
			$checked = FALSE;
		}

		$radio1 = array(
			'name' => 'display_pp_instructions',
			'value' => 'yes',
			'checked' => $checked
		);

		$radio2 = array(
			'name' => 'display_pp_instructions',
			'value' => 'no',
			'checked' => !$checked
		);

		$this->EE->table->add_row(
			lang('display_instructions_option_text'),
			'Yes: '.form_radio($radio1).NBS.' No: '.form_radio($radio2)
		);
	}

	/**
	 * Display settings for an individual instance of a Pic Puller fieldtype in Matrix
	 * @param  $data existing settings for this fieldtype
	 * @return string that is the HTML of the form that lets user alter settings
	 */
	function display_cell_settings( $data )
	{
		$this->EE->lang->loadfile('ig_picpuller');
		$display_pp_instructions = isset($data['display_pp_instructions']) ? $data['display_pp_instructions'] : $this->settings['display_pp_instructions'];
		$checked = TRUE; 

		if ($display_pp_instructions === 'no') {
			$checked = FALSE;
		}

		$radio1 = array(
		'name' => 'display_pp_instructions',
		'value' => 'yes',
		'checked' => $checked
		);

		$radio2 = array(
			'name' => 'display_pp_instructions',
			'value' => 'no',
			'checked' => !$checked
		);
		return array(
		array (  lang('display_instructions_option_text') ,
			'Yes: '.form_radio($radio1).NBS.' No: '.form_radio($radio2) )
		);
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
			'ig_media_id' => '',
			'display_pp_instructions'  => $this->EE->input->post('display_pp_instructions')
			'display_pp_instructions'  => $this->EE->input->post('display_pp_instructions'),
			'the_function' => 'media_recent'
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
		return array(
			'ig_media_id'	=> '',
			'display_pp_instructions' => 'yes'
			'display_pp_instructions' => 'yes',
			'the_function' => 'media_recent'
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

}

/* End of file ft.ig_picpuller.php */
/* Location: ./system/expressionengine/third_party/ig_picpuller/ft.ig_picpuller.php */