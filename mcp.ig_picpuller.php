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
 * ig_picpuller Module Control Panel File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		John Morton
 * @link		http://picpuller.com
 */

class ig_picpuller_mcp {
	
	public $return_data;
	
	private $_base_url;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		
		$this->_the_server = $_SERVER['HTTP_HOST'];
		// the AMP returns &amp; See the getRedirectURL for where I needed to replace that with a real &
		$this->_base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ig_picpuller';
		
		$this->EE->load->library('session');
		$theUserGroup = $this->EE->session->userdata['group_id'];
		
		#echo "member info" . $theUser;
		
		if ($theUserGroup === '1') {
		
		$this->EE->cp->set_right_nav(array(
			'ig_set_up'	=> $this->_base_url,
			'ig_info' => $this->_base_url.AMP.'method=ig_info',
			'ig_users' => $this->_base_url.AMP.'method=ig_users'
			// Add more right nav items here.
		));
		} else {
			// ==============================================================
			// = A non-SuperAdmin doesn't get to see the rest of the module =
			// = so no extra menus in the right hand nav are supplied.      =
			// ==============================================================
		}
		
		// set the name of the CP title
		$this->EE->cp->set_variable('cp_page_title', lang('ig_picpuller_module_name'));
		
	}
	
	// ----------------------------------------------------------------

	/**
	 * Index Function
	 *
	 * @return 	void
	 */
	public function index()
	{
		
		$vars['moduleTitle'] = lang('ig_picpuller_module_name');
		
		// the base without the Session variable
		//$thestrippedbase = preg_replace('/S=.+?&amp;/', '', $this->_base_url);

		if ($this->appAuthorized()) {
			$vars['delete_method'] = $string = $this->_base_url.'&method=removeAuthorization';
			
			return $this->EE->load->view('authorized', $vars, TRUE);
		}
		
		$this->EE->load->library('javascript');
		$this->EE->load->library('table');	
		$this->EE->load->helper('form');
		
		
		// Help the user figure out the oAuth redirect URL
		
		$vars['full_auth_url'] = $this->getRedirectURL();
		
		$vars['redirect_url'] = $this->getRedirectURL(true);

		$action_id = $this->EE->functions->fetch_site_index(0, 0).QUERY_MARKER.'ACT='.$this->EE->cp->fetch_action_id('ig_picpuller', 'authorization');
		
		$vars['action_id'] = $action_id;
		
		/*
		$this->EE->javascript->output(array(
				'// add my own jQuery here.'
				)
		);
		$this->EE->javascript->compile();
		*/	
		
		$vars['form_hidden'] = NULL;
		$vars['options'] = array(
						'edit'  => lang('edit_selected'),
						'delete'    => lang('delete_selected')
						);
		
		if ( $this->appExistsInDb() )
		{
			// app exists in DB so the Action URL is the Redirect URL for authroizing an app
			
			$vars['preexisting_app'] = TRUE;
			$vars['action_url'] = $this->getRedirectURL();
			$vars['clientID'] = $this->getClientID();
			
		} else {
			// no app exists in DB so the Action URL is to save setting to the DB
			
			$vars['preexisting_app'] = FALSE;
			$vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ig_picpuller'.AMP.'method=save_settings';
		}
		
		
		return $this->EE->load->view('index', $vars, TRUE);
	}

	public function ig_info() 
	{
		$vars['client_id'] = $this->getClientID();
		$vars['client_secret'] = $this->getSecret();
		$vars['delete_method'] = $this->_base_url.'&method=delete_app';
		$vars['edit_secret'] = $this->_base_url.'&method=edit_secret';
		
		return $this->EE->load->view('ig_about', $vars, TRUE);	
	}
	
	public function ig_users()
	{
		$this->EE->db->select('ig_picpuller_oauths.member_id, screen_name, oauth');
		$this->EE->db->from('ig_picpuller_oauths');
		$this->EE->db->join('members', 'ig_picpuller_oauths.member_id = members.member_id');
		$query = $this->EE->db->get();
		
		$member_ids= array();
		$screen_names= array();
		$oauths= array();
		
		
		foreach ($query->result() as $row)
		{
    		array_push($member_ids, $row->member_id);
			array_push($screen_names, $row->screen_name);
			array_push($oauths, $row->oauth);
		}
		
			
		$vars['member_ids'] = $member_ids;
		$vars['screen_names'] = $screen_names;
		$vars['oauths']	= $oauths;
		
		return $this->EE->load->view('ig_users', $vars, TRUE);
	}
	
	public function edit_secret()
	{
		$vars['client_id'] = $this->getClientID();
		$vars['client_secret'] = $this->getSecret();
		$vars['form_hidden'] = NULL;
		//$vars['delete_method'] = $this->_base_url.'&method=update_secret';
	//	$vars['update_secret_url'] = $this->_base_url. '&method=update_secret';
		$vars['update_secret_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ig_picpuller'.AMP.'method=update_secret';
		$vars['cancel_url'] = $this->_base_url.'&method=ig_info';
		
		
		return $this->EE->load->view('ig_secret_update', $vars, TRUE); 
		
	}
	
	public function update_secret()
	{
		$this->EE->cp->set_variable('cp_page_title', lang('ig_picpuller_module_name'));
		//var_dump($this->EE->input->post('ig_client_secret', TRUE));
		$ig_client_id = $this->getClientID();
		$ig_client_secret = $this->EE->input->post('ig_client_secret', TRUE);
		/*
			TODO 
			
			FINISH This function --- link to it from the app info screen
		*/
		$data = array(
			'ig_client_secret' => $ig_client_secret
		);

		$this->EE->db->where('ig_client_id', $ig_client_id);
		$this->EE->db->update('ig_picpuller_credentials', $data);

		$vars['ig_client_secret'] = $ig_client_secret;
		$vars['client_id'] = $ig_client_id;
		$vars['client_secret'] = $ig_client_secret;
		
		// TO DO : add proper protocl here with getProtocol()
		$vars['homeurl'] = $this->_base_url;
		return $this->EE->load->view('save_settings', $vars, TRUE);
	}

	public function save_settings()
	{
		// in this function save the client ID and the client secret for the user created application
		
		// table: ig_picpuller_credentials
		// fields:
		//  ig_client_id
		//  ig_client_secret
		$this->EE->cp->set_variable('cp_page_title', lang('ig_picpuller_module_name'));
		$ig_client_id = $this->EE->input->post('ig_client_id', TRUE);
		$ig_client_secret = $this->EE->input->post('ig_client_secret', TRUE);
		
		// Update new settings
		$this->EE->db->empty_table('ig_picpuller_credentials'); 
		$this->EE->db->set('ig_client_id', $ig_client_id);
		$this->EE->db->set('ig_client_secret', $ig_client_secret);
		$this->EE->db->set('auth_url', $this->getRedirectURL() );
		$this->EE->db->insert('ig_picpuller_credentials'); 

		$vars['client_id'] = $ig_client_id;
		$vars['client_secret'] = $ig_client_secret;
		
		// TO DO : add proper protocl here with getProtocol()
		$vars['homeurl'] = $this->_base_url;
		return $this->EE->load->view('save_settings', $vars, TRUE);	
	}
	
	public function delete_app()
	{
		/// need to check for proper USER level before deleting app
		
		$this->EE->db->empty_table('ig_picpuller_credentials'); 
		$this->EE->db->empty_table('ig_picpuller_oauths');
		return $this->index();
	}
	
	public function removeAuthorization()
	{
		$vars['moduleTitle'] = lang('ig_picpuller_module_name');
		$this->EE->db->select('*');
		$this->EE->db->limit('1');
		$this->EE->db->where('member_id', $this->getLoggedInUserId() );
		$this->EE->db->delete('ig_picpuller_oauths'); 
		//$this->EE->db->delete('ig_picpuller_oauths', array('member_id', $this->getLoggedInUserId() ));
		return $this->EE->load->view('authorized_removed', $vars, TRUE);
	}
	
	private function getProtocol()
	{
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
			// this is a secure_connection so return https
			return "https://";
		} else {
			return "http://";
		}
	}
	
	private function getClientID()
	{
		$this->EE->db->select('ig_client_id');
		$this->EE->db->limit(1);
		$query = $this->EE->db->get('ig_picpuller_credentials');

		foreach ($query->result() as $row)
		{
    		$ig_client_id = $row->ig_client_id;
		}
		if (isset($ig_client_id)){
			return $ig_client_id;
		} else {
			return;
		}
	}
	
	private function getSecret()
	{
		$this->EE->db->select('ig_client_secret');
		$this->EE->db->limit(1);
		$query = $this->EE->db->get('ig_picpuller_credentials');

		foreach ($query->result() as $row)
		{
    		$ig_client_secret = $row->ig_client_secret;
		}
		if (isset($ig_client_secret)){
			return $ig_client_secret;
		} else {
			return;
		}
	}

	private function getRedirectURL($urlEncoded = false) 
	{
		return $this->EE->functions->fetch_site_index(0, 0).QUERY_MARKER.'ACT='.$this->EE->cp->fetch_action_id('ig_picpuller', 'authorization');
		
	}

	private function appAuthorized()
	{
		$this->EE->db->select('oauth');
		$this->EE->db->where('member_id', $this->getLoggedInUserId() );
		$this->EE->db->limit('1');
		$this->EE->db->from('ig_picpuller_oauths');
		$query = $this->EE->db->get();
		
		if ($query->num_rows() == 1)
		{
			return true;
		} else {
			return false;
		}
	}
	
	private function appExistsInDb()
	{
		// is there an application already defined in the database?
		$this->EE->db->select('*');
		$this->EE->db->limit('1');
		$this->EE->db->from('ig_picpuller_credentials');
		$query = $this->EE->db->get();
		
		if ($query->num_rows() == 0)
		{
			return false;
		} else {
			return true;
		}
	}
	
	private function getLoggedInUserId()
	{
		$this->EE->load->library('session');
		//$all_userdata = $this->EE->session->all_userdata();
		//print_r ($memberid);
		//return $all_userdata['member_id'];
		
		return $this->EE->session->userdata('member_id');
		
	}
	
}
/* End of file mcp.ig_picpuller.php */
/* Location: /system/expressionengine/third_party/ig_picpuller/mcp.ig_picpuller.php */