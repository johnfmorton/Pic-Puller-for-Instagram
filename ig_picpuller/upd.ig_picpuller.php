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
 * ig_picpuller Module Install/Update File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		John Morton
 * @link		http://picpuller.com
 */

class Ig_picpuller_upd {
	
	public $version = '1.0';
	
	private $EE;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Installation Method
	 *
	 * @return 	boolean 	TRUE
	 */
	public function install()
	{
		$mod_data = array(
			'module_name'			=> 'Ig_picpuller',
			'module_version'		=> $this->version,
			'has_cp_backend'		=> "y",
			'has_publish_fields'	=> 'n'
		);
		
		$this->EE->db->insert('modules', $mod_data);
		
		$data = array(
			'class' => "Ig_picpuller",
			'method' => 'authorization'
		);

		$this->EE->db->insert('actions', $data);

		$this->EE->load->dbforge();

		$fields = array(
			'ig_client_id' => array('type' => 'varchar', 'constraint' => '64', 'null' => TRUE, 'default' => NULL),
			'ig_client_secret' => array('type' => 'varchar', 'constraint'=> '64', 'null' => TRUE, 'default' => NULL)
			,
			'auth_url' => array('type' => 'varchar', 'constraint'=> '256', 'null' => TRUE, 'default' => NULL)
		);

		$this->EE->dbforge->add_field($fields);
		//$this->EE->dbforge->add_key('ig_client_id', TRUE);
		$this->EE->dbforge->create_table('ig_picpuller_credentials');
		
		unset($fields);

		$fields = array(
			'member_id' => array('type' => 'varchar', 'constraint' => '64', 'null' => TRUE, 'default' => NULL),
			'instagram_id' => array('type' => 'varchar', 'constraint' => '64', 'null' => TRUE, 'default' => NULL),
			'oauth' => array('type' => 'varchar', 'constraint' => '255', 'null' => TRUE, 'default' => NULL)
		);
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->create_table('ig_picpuller_oauths');
		
		return TRUE;
	}

	// ----------------------------------------------------------------
	
	/**
	 * Uninstall
	 *
	 * @return 	boolean 	TRUE
	 */	
	public function uninstall()
	{
		$this->EE->load->dbforge();
		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array(
			'module_name'	=> 'Ig_picpuller'
		));
		
		/*$mod_id = $this->EE->db->select('module_id')
								->get_where('modules', array(
									'module_name'	=> 'ig_picpuller'
								))->row('module_id');
		*/
		
		// $mod_id is now an array, but it shouldn't be... that's causing the uninstall error, i think
		
		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');
		
		$this->EE->db->where('module_name', 'Ig_picpuller');
		$this->EE->db->delete('modules');
		
		$this->EE->db->where('class', 'Ig_picpuller');
		$this->EE->db->delete('actions');

		$this->EE->dbforge->drop_table('ig_picpuller_credentials');
		$this->EE->dbforge->drop_table('ig_picpuller_oauths');
		

		// No publish fields in this version to remove
		//$this->EE->load->library('layout');
		//$this->EE->layout->delete_layout_tabs($this->tabs(), 'ig_picpuller');
		
		return TRUE;
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Module Updater
	 *
	 * @return 	boolean 	TRUE
	 */	
	public function update($current = '')
	{
		// If you have updates, drop 'em in here.
		return TRUE;
	}
	
}
/* End of file upd.ig_picpuller.php */
/* Location: /system/expressionengine/third_party/ig_picpuller/upd.ig_picpuller.php */