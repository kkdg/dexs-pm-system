<?php
/**
 *	Check the Permissions, for the PM System, of a User!
 *	
 *	@since 0.9.0 BETA
 * 
 *  @param string user_id (current_user will get if the parameter is empty!)
 *  @param string "pm", "images", "backend", "frontend", "default"
 *  @return true, if the user has the permission
 *  @return false, if the user hasn't the permission
 */
	function check_permission($user_id = "", $type){
		global $wpdb, $wp_roles, $default_permissions, $current_user;
		get_currentuserinfo();
		
		$check = get_option('dexs_pm_permissions', $default_permissions);
			$exc = $check['exclude_users'];
			$inc = $check['include_users'];
		
		if($user_id == "" || empty($user_id)){
			$user_id = $current_user->ID;
		}

		if($type == "images"){
			$type = 2;
		}
		if($type == "backend"){
			$type = 3;
		}
		if($type == "frontend"){
			$type = 4;
		}
		if($type == "default"){
			$type = 5;
		}
		
		foreach ($wp_roles->role_names as $role => $name){
			if (user_can($user_id, $role)){
				$perm = explode(",", $check[$role]);
			}
		}
		
		# Exclude User
		if(preg_match('#\b'.$user_id.'\b#', $exc)){
			return false;
		}
		
		# Deactivated System for Role
		if($perm[0] == 1 && !preg_match('#\b'.$user_id.'\b#', $inc)){
			return false;
		}
		
		# Check Permissions
		if($type != "pm"){
			if($perm[$type] == 0){
				return false;
			} elseif($perm[$type] == 1){
				return true;
			}
		} else {
			if($perm[1] != "-1"){
				if($perm[1] == "0"){
					return false;
				} else {
					$get_count = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."dexs_pms"); $i = 0;
					foreach($get_count AS $count){
						$checkstatus = unserialize($count->pm_status);
						if(array_key_exists("sender_$user_id", $checkstatus) || array_key_exists("recipient_$user_id", $checkstatus)){
							if($checkstatus["sender_$user_id"] != 3 || $checkstatus["recipient_$user_id"] != 3){
								$i++;
							}
						}
					}

					if($i >= $perm[1]){
						return false;
					} else {
						return true;
					}
				}
			} else {
				return true;
			}
		}
	}
	
/**
 *	Load the Toolbar Menu
 *	
 *	@since 0.9.0 BETA
 */	
	function load_toolbar(){
		function dexs_pm_system_toolbar($wp_admin_bar){
			global $wpdb, $wp_admin_bar, $current_user, $default;
			$option = get_option('dexs_pm_system', $default);
			get_currentuserinfo();
			
			$inbox_new2 = 0; $table_name = $wpdb->prefix."dexs_pms";
			$get_2_count = $wpdb->get_results( "SELECT * FROM $table_name WHERE pm_recipient_ids LIKE '%".$current_user->ID."%' AND pm_status LIKE '%\"recipient_".$current_user->ID."\"%'");
			foreach ($get_2_count AS $count){
				$checkstatus = unserialize($count->pm_status);
				if($checkstatus["recipient_$current_user->ID"] == 0){
					$inbox_new2++;
				}
			}
			if($inbox_new2 == 0){ $act = ""; }else{ $act = "active"; }
			?>
			<style type="text/css">
			<!--
				#wp-admin-bar-pm_system .ab-item .ab-icon{
					background-image: url('<?php echo plugins_url('images/admin-icons.png' , __FILE__); ?>');
					background-position: 0px 0px;
					background-repeat: no-repeat;
					width: 20px;
					height: 20px;
					margin-top: 5px;
					padding-left: 1px;
					padding-right: 2px;	
					float:left;
				}
				#wp-admin-bar-pm_system.hover .ab-item .ab-icon{
					background-position: 0 -18px;
				}
				
				#wp-admin-bar-pm_system .ab-item .active{
					background-position: 0 -36px;
					color: #e8840e;
				}
			-->
			</style>
			<?php
			if($option['showin_navigation'] == 1){
				$href = "admin.php?page=pm";
			} else {
				$href = "users.php?page=pm";			
			}
			
			$args = array(
				'id' => 'pm_system',
				'parent' => 'top-secondary',
				'title' => "<span class='ab-icon $act'></span> <span class='$act'>$inbox_new2</span>",
				'href' => $href,
				'class' => 'test',
				'meta' => array('class' => 'dexs_pm_system')
			);
			$wp_admin_bar->add_node($args);
			
			if($inbox_new2 == 1){
				$title = __('1 new Message', 'dexs-pm');
			} else {
				$title = $inbox_new2." ".__('new Messages', 'dexs-pm');			
			}
			$args2 = array(
				'id' => 'pm_system_entry',
				'parent' => 'pm_system',
				'title' => $title,
				'href' => $href,
				'meta' => array('class' => 'dexs_pm_system')
			);
			$wp_admin_bar->add_node($args2);
			
			$args3 = array(
				'id' => 'pm_system_entry_2',
				'parent' => 'pm_system',
				'title' => __('Write a new Message', 'dexs-pm'),
				'href' => $href."&pm_send",
				'meta' => array('class' => 'dexs_pm_system')
			);
			$wp_admin_bar->add_node($args3);
		}
	}
	
	
/**
 *	Get the Count of User PMs
 *	
 *	@since 0.9.0 BETA
 * 
 *  @param string user_id (current_user will get if this parameter is empty!)
 *  @param string "all", "inbox", "new", "outbox", "trash", "archive"
 *  @return string number of pm's 
 */
	function count_pm($user_id = "", $count_this){
		global $wpdb, $current_user;
		get_currentuserinfo();
		
		if($user_id == "" || empty($user_id)){
			$user_id = $current_user->ID;
		}
		
		$get_count = $wpdb->get_results("SELECT pm_sender_id, pm_recipient_ids, pm_status FROM ".$wpdb->prefix."dexs_pms"); $i = 0;
		
		foreach($get_count AS $count){
			$checkstatus = unserialize($count->pm_status);
			if(array_key_exists("sender_$user_id", $checkstatus) || array_key_exists("recipient_$user_id", $checkstatus)){
				if($count_this == "all"){
					if($checkstatus["sender_$user_id"] != 3 || $checkstatus["recipient_$user_id"] != 3){
						$i++;
					}
				}
				if($count_this == "inbox"){
					if(array_key_exists("recipient_$user_id", $checkstatus) && $checkstatus["recipient_$user_id"] == 1 || array_key_exists("recipient_$user_id", $checkstatus) && $checkstatus["recipient_$user_id"] == 0){
						$i++;
					}
				}
				if($count_this == "new"){
					if(array_key_exists("recipient_$user_id", $checkstatus) && $checkstatus["recipient_$user_id"] == 0){
						$i++;
					}
				}
				if($count_this == "outbox"){
					if(array_key_exists("sender_$user_id", $checkstatus) && $checkstatus["sender_$user_id"] == 1){
						$i++;
					}
				}
				if($count_this == "archive"){
					if(array_key_exists("sender_$user_id", $checkstatus) && $checkstatus["sender_$user_id"] == 4 || array_key_exists("recipient_$user_id", $checkstatus) && $checkstatus["recipient_$user_id"] == 4){
						$i++;	
					}
				}
				if($count_this == "trash"){
					if(array_key_exists("sender_$user_id", $checkstatus) && $checkstatus["sender_$user_id"] == 2 || array_key_exists("recipient_$user_id", $checkstatus) && $checkstatus["recipient_$user_id"] == 2){
						$i++;	
					}
				}
			}
		}
		
		return $i;
	}
	
/**
 *	Get Mail Informations
 *	
 *	@since 0.9.0 BETA
 *
 *	@return array
 */
	function get_pm_email(){
		if(is_admin()){
			if(file_exists(ADMIN_A_URL."/mail.php")){
				include(ADMIN_A_URL."/mail.php");			
				return array('email_sender' => $email_sender, 'email_reply_to' => $email_reply_to, 'email_subject' => $email_subject, 'email_message' => $email_message);
			} else {
				return array();
			}
		} else {
			if(file_exists(A_URL."/mail.php")){
				include(A_URL."/mail.php");			
				return array('email_sender' => $email_sender, 'email_reply_to' => $email_reply_to, 'email_subject' => $email_subject, 'email_message' => $email_message);
			} else {
				return array();
			}
		}
	}
?>