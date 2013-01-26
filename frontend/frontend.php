<?php
	if(is_user_logged_in()){
		
		if(check_permission("", "frontend") == true){
		
			include("functions.php");
			include(A_URL."/include/pm_system.php");
			$options = get_option('dexs_pm_system', $default);
			
			if(isset($_GET['page'])){
				if($_GET['page'] == "pm"){
					$table = "inbox";				
				}
				if($_GET['page'] == "pm_outbox"){
					$table = "outbox";				
				}
				if($_GET['page'] == "pm_archive"){
					$table = "archive";				
				}
				if($_GET['page'] == "pm_trash"){
					$table = "trash";				
				}
				if($_GET['page'] == "pm_settings"){
					$table = "settings";
				}
				if($_GET['page'] == "send_pm"){
					$table = "send";
				}
			} else {
				$table = "inbox";
			}
			$messages = get_private_messages($table);
			
			if(!preg_match("#page#", $_SERVER["REQUEST_URI"]) && preg_match("#\?#", $_SERVER["REQUEST_URI"]) && isset($_GET['p'])){
				$url = "?p=".$_GET['p']."&page=";
			} else {
				$url = "?page=";	
			}
			
			if($table == "inbox"){
				$bulk_actions .= '<option value="4">'.__('Archive', 'dexs-pm').'</option>';
				$bulk_actions .= '<option value="2">'.__('Trash', 'dexs-pm').'</option>';
				$bulk_actions .= '<option value="1">'.__('Mark as Read', 'dexs-pm').'</option>';
				$bulk_actions .= '<option value="0">'.__('Mark as Unread', 'dexs-pm').'</option>';
			}
			if($table == "outbox"){
				$bulk_actions .= '<option value="4">'.__('Archive', 'dexs-pm').'</option>';
				$bulk_actions .= '<option value="2">'.__('Trash', 'dexs-pm').'</option>';
			}
			if($table == "archive"){			
				$bulk_actions .= '<option value="1">'.__('Move Back', 'dexs-pm').'</option>';
				$bulk_actions .= '<option value="2">'.__('Trash', 'dexs-pm').'</option>';
			}
			if($table == "trash"){
				$bulk_actions .= '<option value="1">'.__('Restore', 'dexs-pm').'</option>';
				$bulk_actions .= '<option value="3">'.__('Delete Permanently', 'dexs-pm').'</option>';
			}
			
			$user_settings = get_user_meta( $current_user->ID, "dexs_pm_settings", true ); $user_id = $current_user->ID;
			if(empty($user_settings[$table."_num"])){ $limit = 20; } else { $limit = $user_settings[$table."_num"]; }
			if(!isset($_GET['view'])){ $page = 0; } else { $page = $_GET['view']*$limit; }
			$get_messages = $wpdb->get_results("SELECT * FROM $table_name ORDER BY pm_send DESC LIMIT $page,$limit");
		
			$counter = count_pm("", $table);
			if(!isset($_GET['view'])){ $disa = "disabled"; } else { $disa = ""; }
			if($counter <= ($_GET['view']+1)*$limit){ $disb = "disabled"; } else { $disb = ""; }
			
			/* GET PAGE LINKS */
			$prev_link = ""; $next_link = ""; $last_link = "";
			if(isset($_GET['view']) && $_GET['view'] > 2){
				$prev_link = "&view=".$_GET['view']-1;
			}
			if(!isset($_GET['view'])){
				if($counter > $limit){
					$next_link = "&view=1";
				}
			} else {
				if($counter > $limit){
					if(($_GET['view']+1)*$limit >= $counter){
						$next_link = "&view=".$_GET['view'];		
					} else {
						$next_link = "&view=".$_GET['view']+1;		
					}
				}
			}
			if($counter > $limit){
				$code = ceil($counter/$limit)-1;
				$last_link = "&view=".$code;
			}
			
			$style_url = get_bloginfo('url')."/wp-content/plugins/dexs-pm-system/frontend/themes/";
			$plugin = plugins_url('dexs-pm-system');
						
			/* INLCUDE DESIGN */
			#if(file_exists($style_url.$options["frontend_theme"].'/style.css')){
				echo '<link rel="stylesheet" type="text/css" href="'.$style_url.$options["frontend_theme"].'/style.css">';
			#}
						
			if(file_exists(A_URL."/frontend/themes/".$options['frontend_theme']."/header.php")){
				include(A_URL."/frontend/themes/".$options['frontend_theme']."/header.php");
			}
			
			if(isset($_GET['page']) && $_GET['page'] == "send_pm"){
				if(check_permission($current_user->id, "pm") == false){
					$errors['goto_max'] = __('You have reached your maximum number of messages. Please delete some!', 'dexs-pm');
				}
			}
		
			if(isset($errors)){
				echo '<div id="settings-error-settings_updated" class="error settings-error">';
					foreach($errors AS $e){
					echo "<p>".$e."</p>";
					}
				echo '</div>';
			}
			if($_GET['send_status'] == "true"){
				echo '<div id="settings-error-settings_updated" class="updated settings-error">';
					echo "<p>".__('PM successfully sent!', 'dexs-pm')."</p>";
				echo '</div>';
			}
			if(isset($_GET['success']) && $_GET['success'] != "save"){ $number = $_GET['success'];
				echo '<div id="settings-error-settings_updated" class="updated settings-error">';
					if($number == "1"){
						echo "<p>".__('1 Action was carried out successfully!', 'dexs-pm')."</p>";
					} else {
						echo "<p>".$number." ".__('Actions was carried out successfully!', 'dexs-pm')."</p>";				
					}
				echo '</div>';
			}
			if(isset($_GET['success']) && $_GET['success'] == "save"){
				echo '<div id="settings-error-settings_updated" class="updated settings-error">';
					echo "<p>".__('Your personal preferences have been saved!', 'dexs-pm')."</p>";
				echo '</div>';
			}
			
			if(isset($_GET['action']) && $_GET['action'] == "read"){
				if(file_exists(A_URL."/frontend/themes/".$options['frontend_theme']."/message.php")){
					include(A_URL."/frontend/themes/".$options['frontend_theme']."/message.php");
				} else {
					include(A_URL."/frontend/themes/".$options['frontend_theme']."/frontend.php");				
				}			
			} else {
			
				if($table == "settings"){
				
					$user_settings = get_user_meta($current_user->ID, "dexs_pm_settings", true);
					$general_settings = get_option('dexs_pm_system', $default);

					if(!empty($user_settings['email_notice'])){
						$check = $user_settings['email_notice'];
					} else {
						if(check_permission($current_user->ID, "default") == true){
							$check = "1";
						} else {
							$check = "0";
						}
					}
			
					if(file_exists(A_URL."/frontend/themes/".$options['frontend_theme']."/settings.php")){
						include(A_URL."/frontend/themes/".$options['frontend_theme']."/settings.php");
					} else {
						include(A_URL."/frontend/themes/".$options['frontend_theme']."/frontend.php");				
					}
				
				} elseif($table == "send"){
				
					if(!isset($get_reci)){ $get_reci = ""; }
					if(!isset($get_subject)){ $get_subject = ""; }
					if(!isset($get_message)){ $get_message = ""; }
					
					$user_names = $wpdb->get_results( "SELECT display_name, ID FROM $wpdb->users WHERE ID != '".$current_user->ID."' ORDER BY display_name ASC" );
					
					if(isset($_POST['the_recipient_ids']) && $_POST['the_recipient_ids'] != ""){ $get_reci = $_POST['the_recipient_ids']; }
					if(is_array($get_reci)){ $get_reci = implode(",", $get_reci); }
					if(isset($_POST['subject'])){ $get_subject = $_POST['subject']; }
					if(isset($_POST['the_message'])){ $get_message = $_POST['the_message'];	}
										
					echo '<link type="text/css" rel="stylesheet" href="'.$plugin.'/include/admin_form.css">';
					if($options['recipient_listing'] == "1"){
						# The FaceBook List
						foreach ($user_names AS $user_name){
							$usernames2 .= '{"caption":"'.$user_name->display_name.'","value":"'.$user_name->ID.'"}, ';
						}
						$usernames = substr($usernames2, 0, -2);
						if (!is_admin()){ add_action("wp_enqueue_scripts", "my_jquery_enqueue", 11); }
						
						/* LOAD AUTOCPMPLETE INPUT FIELD */
						echo '<script type="text/javascript" src="'.$plugin.'/include/js/prototype.js"></script>';
						echo '<script type="text/javascript" src="'.$plugin.'/include/js/scriptaculous.js"></script>';
						echo '<script type="text/javascript" src="'.$plugin.'/include/js/facebooklist.js"></script>';
					} else {
						/* LOAD DROP DOWN INPUT FIELD */
						echo '<script type="text/javascript" src="'.$plugin.'/include/js/dropdown.js"></script>';
					}		
					
						if(file_exists(A_URL."/frontend/themes/".$options['frontend_theme']."/send_form.php")){
							include(A_URL."/frontend/themes/".$options['frontend_theme']."/send_form.php");
						} else {
							include(A_URL."/frontend/themes/".$options['frontend_theme']."/frontend.php");				
						}
				
				} else {
				
					if($options['frontend_style'] == "1"){
						if(file_exists(A_URL."/frontend/themes/".$options['frontend_theme']."/frontend_full.php")){
							include(A_URL."/frontend/themes/".$options['frontend_theme']."/frontend_full.php");
						} else {
							include(A_URL."/frontend/themes/".$options['frontend_theme']."/frontend.php");				
						}
					} else {
						include("themes/".$options['frontend_theme']."/frontend.php");
					}
					
				}
			}
			
			if(file_exists(A_URL."/frontend/themes/".$options['frontend_theme']."/footer.php")){
				include(A_URL."/frontend/themes/".$options['frontend_theme']."/footer.php");
			}
			
		} else {
		
			_e("You can't access to this area!", "dexs-pm");
		
		}
		
	} else {
	
		_e("You can't access to this area!", "dexs-pm");
	
	}
	
?>