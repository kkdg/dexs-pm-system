<?php
/**
 *	Get the Private Messages for the Frontend
 *	
 *	@since 0.9.0 BETA
 * 
 *  @param string "inbox", "outbox", "archive", "trash"
 *  @return array
 */
function get_private_messages($table){
	global $wpdb, $current_user, $default;
	
	if($table == "send" || $table == "settings"){
		return array();
	}
	
	$table_name = $wpdb->prefix."dexs_pms";
	
	$user_id = $current_user->ID;
	
	/* GET/SET PM SETTINGS */
	$user_settings = get_user_meta($user_id, "dexs_pm_settings", true);
	if(empty($user_settings[$table."_num"])){ $limit = 15; } else { $limit = $user_settings[$table."_num"]; }
	if(!isset($_GET['view'])){ $page = 0; } else { $page = $_GET['view']*$limit; }
	$get_messages = $wpdb->get_results("SELECT * FROM $table_name ORDER BY pm_send DESC LIMIT $page,$limit");
		
	foreach($get_messages AS $message){
		$checkstatus = unserialize($message->pm_status);
		if($table == "inbox"){
			if(array_key_exists("recipient_$user_id", $checkstatus) && $checkstatus["recipient_$user_id"] == 1 || array_key_exists("recipient_$user_id", $checkstatus) && $checkstatus["recipient_$user_id"] == 0){
				$messages[] = $message;
			}
		}
		
		if($table == "outbox"){
			if(array_key_exists("sender_$user_id", $checkstatus) && $checkstatus["sender_$user_id"] == 1){
				$messages[] = $message;
			}
		}
		
		if($table == "archive"){
			if(array_key_exists("sender_$user_id", $checkstatus) && $checkstatus["sender_$user_id"] == 4 || array_key_exists("recipient_$user_id", $checkstatus) && $checkstatus["recipient_$user_id"] == 4){
				$messages[] = $message;
			}
		}
		
		if($table == "trash"){
			if(array_key_exists("sender_$user_id", $checkstatus) && $checkstatus["sender_$user_id"] == 2 || array_key_exists("recipient_$user_id", $checkstatus) && $checkstatus["recipient_$user_id"] == 2){
				$messages[] = $message;
			}
		}
	}
		
	if(count($messages) != 0){
		return $messages;	
	} else {
		return array();
	}
}

/**
 *	Get current theme informations
 *	
 *	@since 0.9.0 BETA
 * 
 *  @return array
 */
function get_pm_theme(){
	global $default;
	$options = get_option('dexs_pm_system', $default);

	if(file_exists(A_URL."/frontend/themes/".$options['frontend_theme']."/theme.ini")){ 
		$load_theme = parse_ini_file(A_URL."/frontend/themes/".$options['frontend_theme']."/theme.ini");			
		return $load_theme;
	} else {
		return array();
	}
}

?>