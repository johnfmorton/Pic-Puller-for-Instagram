/**
 * Create a new channel for Pic Puller
 *
 * This function should be called by the advanced menu instead of this current method which
 * requires adding tags to a template to execute them.
 *
 * @access	private
 * @param	none
 * @return	string - beeps
 */

public function create_pp_channel_field_group()
{
	$this->EE->load->library('api'); 
	$this->EE->api->instantiate('channel_structure');
	$this->EE->api->instantiate('channel_fields');
	$this->EE->api_channel_fields->fetch_custom_channel_fields();
	
	$prefix = $this->EE->db->dbprefix;
	$pp_field_group_name = 'Pic Puller Group-Do Not Update Manually';
	$pp_channel_name = 'Pic Puller-Do Not Update Manually';

	$query = $this->EE->db->query("SELECT group_name FROM ". $prefix. "field_groups WHERE group_name = 'Pic Puller Group-Do Not Update Manually' AND site_id = '".  $this->_currentSite . "'" ) ;

	echo ("Does field group exist? ");
	echo ($query->num_rows ? 'yes' : 'no' );
	echo "<br >";
	// If the field group doesn't exist yet for this site, create it.
	if(!$query->num_rows){
		$query = $this->EE->db->query("SELECT group_id FROM ". $prefix. "field_groups ORDER BY group_id DESC LIMIT 1")->result();
		$highest_existing_group_id = $query[0]->group_id; 
		$next_group_id = $highest_existing_group_id + 1;
		
		// echo "<pre>Highest Number of field_group that exists: ";
		// echo( $query[0]->group_id . ", next: " . $next_group_id);
		// echo "</pre>";

		// Create the new field group

		$sql = "INSERT INTO ". $prefix. "field_groups (group_id, site_id, group_name) 
		VALUES (".$next_group_id.", ".$this->_currentSite.", 'Pic Puller Group-Do Not Update Manually')";
		// Commented out only for dev reasons... need to check to see it this field group exists before creating a new one

		$this->EE->db->query($sql);
		echo ('Success field group creation? '. $this->EE->db->affected_rows());
		$this->EE->TMPL->log_item('Pic Puller: create_pp_channel_field_group');
		// Now create the Pic Puller channel
		// 
		$query = $this->EE->api_channel_structure->get_channels($this->_currentSite);

		//$query = $this->EE->api_channel_structure->get_channel_info(2); //->result_object;
		//$data =$this->_currentSite;
		//$query = $this->EE->api_channel_structure->get_channel_info($channel_id); ;
		

		// foreach ($query->result() as $row)
		// {
		// 	//$site_label = $row->site_label;
		// 	echo "<pre>Existing Channels: ";
		// 	echo($row->channel_title . " (" . $row->channel_id . ")");
		// 	echo "</pre>";
		// }


		//echo "<pre>";
		//print_r($query->result());
		//echo "</pre>";


		$data = array(
			'channel_title'     => $pp_channel_name,
			'channel_name'      => 'ig_pp_autochannel',
			'field_group'       => $next_group_id,
			'channel_url'       => 'http://yoursite.com/index.php/you/can/update/this',
			'status_group'      => 1
		);

		$query = $this->EE->db->query("SELECT channel_name FROM ". $prefix. "channels WHERE channel_name = 'ig_pp_autochannel' AND site_id = '".  $this->_currentSite . "'" ) ;

		echo ("Does channel exist? ");
		echo $query->num_rows ? 'yes' : 'no';

		if(!$query->num_rows){
			if ($this->EE->api_channel_structure->create_channel($data) === FALSE)
			{
					show_error('An Error Occurred Creating the Channel');
			}
		}
	}
	
	return "Beep.";

}
