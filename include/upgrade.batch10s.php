<?php
	/*
	 *		UPGRADE SCRIPT
	 *		FROM VERSION	0.9.1 Beta 				Dexs PM System	(CodeName: x)
	 *		TO VERSION 		1.0.0 RC.1		(Pit) 	Dexs PM System 	(CodeName: PreHexley)
	 *
	 *		== NEW DATABASE TABLE ==
	 *		
	 *		OLD DB TABLE 	VS.		NEW DB TABLE
	 *		pm_id					pm_id					UNIQUE ID
	 *		pm_subject				pm_subject				SUBJECT (TITLE)
	 *		pm_message				pm_message				MESSAGE
	 *		pm_priority				-- deprecated --		-- moved to pm_meta --
	 *		pm_sender_id			pm_sender				SENDER ID
	 *		pm_recipient_ids		pm_recipients			RECIPIENTS META (contains ids, read_status, folder, time)
	 *		pm_status				pm_meta					MESSAGE META (contains priority, attachments, attachments_meta, sender_id, folder, send_timestamp)
	 *		pm_signature			-- deprecated --		-- deleted because: not used --
	 *		pm_send					pm_send					DATETIME SEND
	 */
	
	/*
	 *	CHECK THE DATABASE TABLE
	 */
	function dexs_pm_deprecated(){
		global $wpdb;
		
		$wpdb->flush();
		$wpdb->hide_errors();
		$db = $wpdb->query("SHOW TABLES LIKE '".$wpdb->prefix."dexs_pms'");
		$wpdb->flush();
		
		if(empty($db)){
			return false;
		}
		return new WP_Error('pmdb_deprecated', __("The database table for our Dexs PM System is deprecated.")." <a href='options-general.php?page=pm_config'>".__("Please click here", "dexs-pm")."</a> ".__("to go to the upgrade process.", "dexs-pm"));
	}
	
	
	/*
	 *	CONVERT PRIVATE MESSAGES
	 */	
	function dexs_pm_convert_table(){
		global $wpdb;
		$messages = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."dexs_pms");
		
		if(count($messages) == 0){
			return true;
		}
		
		foreach($messages AS $message){
			$pm_id = $message->pm_id;
			$pm_subject = $message->pm_subject;
			$pm_message = $message->pm_message;
			$pm_sender = $message->pm_sender_id;
			$pm_send = $message->pm_send;
			$pm_recipients = array();
			
			/* RECIPIENTS */
			$old_recipients = maybe_unserialize($message->pm_status);
			foreach($old_recipients AS $rec => $key){
				$rec_id = explode("_", $rec);
								
				if($rec_id[0] != "sender"){
					if($key == 0){
						$read = 0;
						$time = "";
					} else {
						$read = 0;
						$time = $message->pm_send;
					}
					
					$pm_recipients[$rec_id[1]] = array(
						"id"	=>	$rec_id[1],
						"read"	=>	$read,
						"table"	=>	$key,
						"time"	=>	$time
					);
				} else {
					$sender_folder = $key;
				}
			}
			
			/* META */
			$pm_meta['file'] = 0;
			$pm_meta['sender'] = array(
				"id"	=> $pm_sender,
				"table"	=> $sender_folder,
				"time"	=> $pm_send
			);
			$old_priority = $message->pm_priority;
			
			if($old_priority == 3){
				$pm_meta["priority"] = 2;
			} else {
				$pm_meta["priority"] = $old_priority;
			}
			
			/* INSERT DATAS */
			$insert_pm = $wpdb->insert(
				$wpdb->prefix."dexs_pmsystem",
				array( 
					'pm_id'				=>	$pm_id,
					'pm_subject'		=>	$pm_subject,
					'pm_message'		=>	$pm_message,
					'pm_sender'			=>	$pm_sender,
					'pm_recipients'		=>	maybe_serialize($pm_recipients),
					'pm_meta'			=>	maybe_serialize($pm_meta),
					'pm_send'			=>	$pm_send
				)
			);
			
			if(!$insert_pm){
				return false;
			}
		}
		return true;
	}
	
	/*
	 *	DELETE OLD PRIVATE MESSAGES-TABLE
	 */
	function delete_old_pm_table(){
		global $wpdb;
		
		$delete = $wpdb->query( 
			"DROP TABLE ".$wpdb->prefix."dexs_pms"
		);
		
		if(!$delete){
			return false;
		}
		return true;
	}
	
	/*
	 *	UPGRADE PROZESS TABLE
	 */
	function dexs_pm_upgrade_table(){
		global $wpdb;
		$check = $wpdb->get_var("SELECT count(*) FROM ".$wpdb->prefix."dexs_pms");
	?>
		<form method="post" action="">
			<div class="wrap">
				<div id="icon" class="icon32"><img src="<?php echo plugins_url('images/adminpm.png' , dirname(__FILE__)); ?>"><br></div>
				<h2><?php _e('Upgrade Dexs PM System to Version ', 'dexs-pm'); echo DPM_S_VER; ?></h2>
				<br class="clear">
				
				<h3 class="title"><?php _e("Convert Private Messages", "dexs-pm"); ?></h3>
				
				<p>
					<?php echo __("We found", "dexs-pm")." "; echo ($check == 1)? _e("one message", "dexs-pm") : $check." ".__("messages", "dexs-pm"); echo " ".__("in the old deprecated database table.", "dexs-pm"); ?>
				</p>
				
				<p>
					<?php if($check > 0){ ?>
						<label><input type="checkbox" name="convert_data" value="convert_me" checked="checked"> Convert ALL Private Messages</label>
					<?php } else { ?>
						<p class="description"><?php _e("You have no private messages!", "dexs-pm"); ?></p>
					<?php } ?>
				</p>
				<br class="clear">
				
				<h3>Start the Upgrade Process</h3>
				<b><?php _e("What will happen?", "dexs-pm"); ?></b>
				<p>
					<ol>
						<li><?php _e("All entries in the old database table will be checked, convert and copied into the new database table.", "dexs-pm"); ?>
							<ul><li><b><?php _e("But only if you have activate the checkbox above!", "dexs-pm"); ?></b></li></ul>
						</li>
						<li><?php _e("The old database table will be deleted.", "dexs-pm"); ?></li>
					</ol>
				</p>
				<br class="clear">
				
				<input type="hidden" name="upgrade" value="true">
				<input type="hidden" name="upgrade_code" value="314159265358979">
				<input type="submit" class="button button-primary button-hero" value="<?php _e("Upgrade Now", "dexs-pm"); ?>">
			</div>
		</form>
	<?php
	}

	/*
	 *	DEPRECATED FUNCTIONS
	 */	 
	/*
	 *	CHECK USER PERMISSIONS
	 *	
	 *	@since 			0.9.1
	 *	@deprecated 	1.0.0 RC.1
	 *
	 *	@param1			string		User ID
	 *	@param2 0.9.1	string 		"pm", "images", "backend", "frontend", "default"
	 *	@param2 1.0.0	string 		"system", "messages", "max_messages", "backend", "frontend", "attachments", "images"
	 *
	 *	@return			bool / string	-	(string only, if the type = max_messages)
	 */
	function check_permission($user_id = "", $type) {
		global $dexsPM;
		_deprecated_function( __FUNCTION__, '0.9.1 Beta', '$dexsPM->check_permissions()');
		
		if($type == "pm"){
			$type = "system";
		}
		if($type == "default"){
			$type = "system";
		}
		
		return $dexsPM->check_permissions($type, $user_id);
	}
	
	/*
	 *	COUNT MESSAGES
	 *	
	 *	@since 			0.9.1
	 *	@deprecated 	1.0.0 RC.1
	 *
	 *	@param1			string		User ID	(deprecated)
	 *	@param2			string		Folder ID or Folder Name {0 = inbox || 1 = outbox || 2 = trash || 4 = archive || 5 = new || 6 = all}
	 *	@param3			bool		True: Print the result || False: Return as an string (or an array, if @param1 == 6)
	 *
	 *	@return			string / array / print
	 */
	function count_pm($user_id = "", $count_this, $echo = false){
		global $dexsPMA;
		_deprecated_function( __FUNCTION__, '0.9.1 Beta', '$dexsPMA->count_messages()');
		
		return $dexsPMA->count_messages($count_this, $echo);
	}
?>