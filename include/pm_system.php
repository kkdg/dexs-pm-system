<?php
$table_name = $wpdb->prefix."dexs_pms";
/* BACKEND */
if(preg_match("#admin.php#", $_SERVER["PHP_SELF"])){
	$pm_url = "admin.php?page=".$_GET['page'];
	if(isset($_GET['table'])){ $pm_url .= "&table=".$_GET['table']; }
} else if(preg_match("#users.php#", $_SERVER["PHP_SELF"])){ 
	$pm_url = "users.php?page=".$_GET['page']; 
	if(isset($_GET['table'])){ $pm_url .= "&table=".$_GET['table']; }
}
/* FRONTEND */
if(!is_admin()){
	if(isset($_GET['p'])){
		$pm_url = "?p".$_GET['p'];
		if(isset($_GET['page'])){ $pm_url .= "&page=".$_GET['page']; }
	} else if(!isset($_GET['p'])){
		if(isset($_GET['page'])){
			$pm_url = "?page=".$_GET['page'];
		} else {
			$pm_url = "?page=pm";
		}
	}
}

		
/******************************
*	MOVE TO || MARK AS ...
******************************/
	if(isset($_GET['action']) && $_GET['action'] != "" && is_numeric($_GET['action']) && isset($_GET['id']) && $_GET['id'] != "" || 
	   isset($_POST['the_action_box']) && isset($_POST['action']) && is_numeric($_POST['action']) && $_POST['action'] != "" || 
	   isset($_POST['the_action_box']) && isset($_POST['action2']) && is_numeric($_POST['action2']) && $_POST['action2'] != ""){
	
		if(isset($_GET['action'])){
			$action = $_GET['action'];
		} elseif(isset($_POST['action'])){
			$action = $_POST['action'];		
		}
		
		if(isset($_GET['id'])){
			$ids = $_GET['id'];
		} elseif(isset($_POST['action'])){
			$ids = implode(',', $_POST['select']);		
		}
		
		if($action != "-1" && $action != "0" && $action != "1" && $action != "2" && $action != "3" && $action != "4"){
			$errors['nope'] = __('Invalid Action!', 'dexs-pm');
		}
		
		$get_user = $current_user->ID;
		$get_ids = explode(",", $ids);
		$count_actions = 0;
		foreach($get_ids AS $pm_id){ $count_actions++;
			$get_pm = $wpdb->get_row("SELECT * FROM $table_name WHERE pm_id='".$pm_id."'");	
			
			if(count($get_pm) != 0){
				$status = unserialize($get_pm->pm_status);
				
				if(array_key_exists("sender_$get_user", $status)){
					$current_action = $status["sender_$get_user"];				# GET CURRENT STATUS
					$edit_user 		= array("sender_$get_user" => $action);		# NEW PM STATUS
					$the_edit 		= array_replace($status, $edit_user);		# REPLACE IT WITH THE OLD STATUS
					$new_pm_status 	= serialize($the_edit);						# SERIALIZE FOR DB
				} elseif(array_key_exists("recipient_$get_user", $status)){
					$current_action = $status["recipient_$get_user"];			# GET CURRENT STATUS
					$edit_user		= array("recipient_$get_user" => $action);	# NEW PM STATUS
					$the_edit 		= array_replace($status, $edit_user);		# REPLACE IT WITH THE OLD STATUS
					$new_pm_status 	= serialize($the_edit);						# SERIALIZE FOR DB
				} else {
					$errors['no_reci_found'] = __('You are neither the sender nor the recipient of this Message!', 'dexs-pm');
				}
				
				$delete = false;
				if($current_action != "3" && $action == "3"){
					$deleted = array_keys($status, '3');
					
					if(count($status)-1 == count($deleted)){
						$delete = true;
					}
				}
				
			} else {
				$errors['no_pm_found'] = __('This message doesn\'t exist (anymore)!', 'dexs-pm');
			}
			
			if(!isset($errors)){

				if($delete == false){
					$wpdb->update(
					$table_name, 
						array( 
							'pm_status' => $new_pm_status
						), 
						array( 'pm_id' => $pm_id )
					);		
				} else {
					$wpdb->query( 
						"DELETE FROM $table_name
						WHERE pm_id = ".$pm_id
					);	
				}
			}
		}
		if(!isset($errors)){
			echo "<script type='text/javascript'>
				window.location.href='".$pm_url."&success=$count_actions'
			</script>";			
		}
	}
	
/******************************
*	SAVE USER SETTINGS
******************************/
	if(isset($_POST['submit_user'])){
		$user_settings = get_user_meta($current_user->ID, "dexs_pm_settings", true);
		if(!empty($user_settings)){
			if($_POST['email_notice'] != ""){ $e = $_POST['email_notice']; } else { $e = $user_settings['email_notice']; }
			update_user_meta($current_user->ID, "dexs_pm_settings", 
				array(
					"inbox_num" => $_POST['inbox_num'],
					"outbox_num" => $_POST['outbox_num'],
					"archive_num" => $_POST['archive_num'],
					"trash_num" => $_POST['trash_num'],
					"email_notice" => $e
				)				
			);
		} else {
			if($_POST['email_notice'] != ""){ $e = $_POST['email_notice']; } else { $e = "0"; }
			add_user_meta( $current_user->ID, "dexs_pm_settings", 
				array(
					"inbox_num" => $_POST['inbox_num'],
					"outbox_num" => $_POST['outbox_num'],
					"archive_num" => $_POST['archive_num'],
					"trash_num" => $_POST['trash_num'],
					"email_notice" => $e
				), true
			);
		}
		echo "<script type='text/javascript'>
			window.location.href='".$pm_url."&success=save'
		</script>";	
	}

/******************************
*	SEND A PRIVATE MESSAGE
******************************/
	if($_POST['send_pm']){
				
		if(check_permission($current_user->id, "pm") == false){
			$errors['goto_max'] = __('You have reached your maximum number of messages. Please delete some!', 'dexs-pm');
		}		
		if(!isset($_POST['subject']) || empty($_POST['subject'])){
			$errors['no_subject'] = __('Please enter a subject!', 'dexs-pm');
		}
		if(!isset($_POST['the_message']) || empty($_POST['the_message'])){
			$errors['no_message'] = __('Please enter a message!', 'dexs-pm');
		}
		if(!isset($_POST['the_recipient_ids']) || empty($_POST['the_recipient_ids'])){
			$errors['no_reci'] = __('Please enter minimum one recipient!', 'dexs-pm');
		} else {
			if(is_array($_POST['the_recipient_ids'])){
				foreach($_POST['the_recipient_ids'] AS $rec){
					$recis .= $rec.",";
					$one["recipient_$rec"] = "0";
				}
				
				$final_recis = substr($recis, 0, -1);
			} else {
				$final_recis = $_POST['the_recipient_ids'];
				$explode = explode(",", $_POST['the_recipient_ids']);
				
				foreach($explode AS $rec){
					$one["recipient_$rec"] = "0";
				}
			}		
		}
		
		if(!isset($errors)){
			$one["sender_$current_user->ID"] = "1";

			$pm_status = serialize($one);
			
			$mes = stripslashes($_POST['the_message']);
			$pm_message = preg_replace("/\[caption(.*)\](.*)\[\/caption\]/", "$2", $mes);
					
			$wpdb->insert(
				$table_name, 
				array( 
					'pm_subject' => $_POST['subject'],
					'pm_message' => $pm_message,
					'pm_priority' => $_POST['priority'],
					'pm_sender_id' => $current_user->ID,
					'pm_recipient_ids' => $final_recis,
					'pm_status' => $pm_status,
					'pm_signature' => "0",
					'pm_send' => current_time('mysql')
				)
			);
			
			$email_recipients = explode(",", $final_recis);
			foreach($email_recipients AS $email_recipient){
				$pm_sender = get_userdata( $current_user->ID );
				$pm_recipient = get_userdata( $email_recipient );
				
				$check = get_option('dexs_pm_system', $default);
				$check_recipient = get_usermeta( $email_recipient, "dexs_pm_settings" );
				
				if($check['email_notice'] == "1"){
					if(!empty($check_recipient) && $check_recipient != "" && $check_recipient['email_notice'] == "1" || empty($check_recipient) && $check_recipient == "" && check_permission($email_recipient, "default") == true){
						
						$mail = get_pm_email();
						$args = array(
							'post_type' => 'page',
							'post_status' => 'publish'
						); 
						$pages = get_pages($args);

						foreach($pages AS $page){
							if(preg_match("/\[pm_system\]/", $page->post_content)){
								$link = get_page_link( $page->ID );
							}
						}
						
						$get_pm_id = $wpdb->get_row( "SELECT pm_id FROM $table_name WHERE pm_subject='".$_POST['subject']."' AND pm_sender_id='".$current_user->ID."' AND pm_recipient_ids='".$final_recis."' ORDER BY pm_send DESC"); 
						
						if($check['showin_navigation'] == "1"){
							$admin = "admin.php?page=pm";
						} elseif($check['showin_navigation'] == "0"){
							$admin = "users.php?page=pm";
						}						
						if($link != ""){ $frontend_link = $link."?page=pm_inbox&action=read&id=".$get_pm_id->pm_id; } else { $frontend_link = get_bloginfo("url")."/wp-admin/".$admin."&action=read&id=".$get_pm_id->pm_id; }
						$backend_link = get_bloginfo("url")."/wp-admin/".$admin."&action=read&id=".$get_pm_id->pm_id;
						
						$search = array("%HOME_TIL%", "%HOME_URL%", "%ADM_MAIL%", "%RECI_NAME%", "%SEND_NAME%", "%PM_SUB%", "%PM_TXT%", "%PM_DATE%", "%PM_TIME%", "%PM_F_LINK%", "%PM_B_LINK%");
						$replace = array(
							get_bloginfo("name"),
							get_bloginfo("url"),
							get_bloginfo("admin_email"),
							$pm_recipient->display_name,
							$pm_sender->display_name,
							$_POST['subject'],
							$pm_message,
							date(get_option('date_format')),
							date(get_option('time_format')),
							$frontend_link,
							$backend_link
						);
						
						$the_message = str_replace($search, $replace, stripslashes(nl2br($mail['email_message'])));
						
						$recipient = $pm_recipient->user_email;
						$subject = str_replace($search, $replace, $mail['email_subject']);
						
						if(preg_match("/\%PM_EXC_(.*)\%/", $the_message, $length)){
						
							$message_size = preg_split("#\%PM_EXC_(.*)\%#Uis", $the_message);
							$excerpt = substr($pm_message, 0, $length[1]);
							
							$message = "<html><body>".$message_size[0].$excerpt.$message_size[1]."</body></html>";
							
						} else {
						
							$message = "<html><body>".$the_message."</body></html>";
						}
										
						$header .= 'MIME-Version: 1.0' . "\r\n";
						$header .= 'Content-type: text/html; charset=utf-8'."\r\n";
						$header .= 'To: '.$pm_recipient->user_email."\r\n";
						$header .= 'From: '.$mail['email_sender']."\r\n";		
						$header .= 'Reply-To: '.$mail['email_reply_to']."\r\n";		
						$header .= 'X-Mailer: PHP/'.phpversion();

						$search  = array ('Ä', 'Ö', 'Ü','ä', 'ö', 'ü', 'ß');
						$replace = array ('&Auml;', '&Ouml;', '&Uuml;', '&auml;', '&ouml;', '&uuml;', '&szlig');
						$send_message = str_replace($search, $replace, $message);
						
						mail($recipient, $subject, $send_message, $header);			
					}
				}
			}
			
			$send_url = str_replace('send_pm', 'pm_outbox' ,$pm_url);
			
			echo "<script type='text/javascript'>
				window.location.href='".$send_url."&send_status=true'
			</script>";
		}
	}
	
/******************************
*	READ THE PRIVATE MESSAGE
******************************/
	if(isset($_GET['action']) && $_GET['action'] == "read" && isset($_GET['id']) && $_GET['id'] != ""){
		$get_pm = $wpdb->get_row("SELECT * FROM $table_name WHERE pm_id='".$_GET['id']."'");
		
		if(count($get_pm) != 0){
			$get_status = unserialize($get_pm->pm_status);
			$get_user = $current_user->ID;
				
			if(array_key_exists("recipient_$get_user", $get_status) || array_key_exists("sender_$get_user", $get_status)){
				if($get_status["recipient_$get_user"] == "3" || $get_status["sender_$get_user"] == "3"){
					$errors['no_pm_found'] = __('This message doesn\'t exist (anymore)!', 'dexs-pm');
				}
			} else {
				$errors['no_reci_found'] = __('You are neither the sender nor the recipient of this Message!', 'dexs-pm');
			}		
		} else {
			$errors['no_pm_found'] = __('This message doesn\'t exist (anymore)!', 'dexs-pm');
		}
		
		if(!isset($errors)){
			if($get_status["recipient_$get_user"] == "0"){
				$edit_reci = array("recipient_$get_user" => "1");
				$the_edit = array_replace($get_status, $edit_reci);
				$new_pm_status = serialize($the_edit);	
				
				$wpdb->update( 
					$table_name, 
					array( 
						'pm_status' => $new_pm_status
					), 
					array('pm_id' => $_GET['id'])
				);					
			}
			if(is_admin()){			
				pm_read_table($_GET['id'], $get_pm);
			}
		}
	}

/******************************
*	GET A PRIVATE MESSAGE
******************************/
	if(isset($_POST['send']) && $_POST['send'] == "true" && isset($_POST['pm_id']) && $_POST['pm_id'] != "" && isset($_POST['send_pm_recipients']) && $_POST['send_pm_recipients'] != ""){
		if(isset($_POST['write_to_users'])){
			$send = "write_to_users";				
		} elseif(isset($_POST['answer_to_users'])){
			$send = "answer_to_users";
		}
		$users = implode(",", $_POST['send_pm_recipients']);
		
		if(preg_match("#pm_outbox#", $pm_url)){
			$send_url = str_replace('pm_outbox', 'send_pm', $pm_url);		
		}
		if(preg_match("#pm_inbox#", $pm_url)){
			$send_url = str_replace('pm_inbox', 'send_pm', $pm_url);	
		}
		if(preg_match("#pm_archive#", $pm_url)){
			$send_url = str_replace('pm_archive', 'send_pm', $pm_url);	
		}
		if(preg_match("#pm_trash#", $pm_url)){
			$send_url = str_replace('pm_trash', 'send_pm', $pm_url);	
		}
		echo "<script type='text/javascript'>
			window.location.href='".$send_url."&action=".$send."&id=".$_POST['pm_id']."&users=".$users."'
		</script>";
	}
	
	if(isset($_GET['table']) && $_GET['table'] == "send_pm" && isset($_GET['action']) && isset($_GET['id']) && $_GET['id'] != "" ||
	   isset($_GET['page']) && $_GET['page'] == "send_pm" && isset($_GET['action']) && isset($_GET['id']) && $_GET['id'] != ""){
	   
		$get_reci = ""; $get_subject = ""; $get_message = "";
				
		$send = $_GET['action'];	
		
		if($send == "write_to_users"){
			if(isset($_GET['users'])){
				$users = $_GET['users'];	
			} else {
				$users = $_GET['id'];
			}
		} else {
			$pm_id = $_GET['id'];
			if($send == "answer_to_users"){
				$users = $_GET['users'];
			}
		}
		
		if($send == "answer_to_users" || $send == "answer" || $send == "forward"){
			$get_pm = $wpdb->get_row( "SELECT * FROM $table_name WHERE pm_id='".$pm_id."'");
			if(count($get_pm) != 0){
				$get_status = unserialize($get_pm->pm_status);
				$get_id = $current_user->ID;
				$user_id = get_userdata($get_pm->pm_sender_id);
				$get_pm_date = date_create($pm->pm_send);
				
				if(array_key_exists("sender_$get_id", $get_status) || array_key_exists("recipient_$get_id", $get_status)){
					
					if($send == "answer" && $get_pm->pm_sender_id != $current_user->ID){
						$get_reci = $get_pm->pm_sender_id;
					}
					
					if($send == "answer_to_users"){
						$get_reci = $users;
					}
					
					if($send == "forward"){
						$get_subject = "FWD: ".$get_pm->pm_subject;
					} else {
						$get_subject = "RE: ".$get_pm->pm_subject;					
					}
					
					$get_message = "<br><br><b>".__('Original Message', 'dexs-pm')."</b><small><br>".$user_id->display_name." ".__('wrote on', 'dexs-pm')." ".date_format($get_pm_date, get_option('date_format'))." ".__('at', 'dexs-pm')." ".date_format($get_pm_date, get_option('time_format'))." ".__('o\'clock', 'dexs-pm').":<br><i>".$get_pm->pm_message."</i></small>";
				
				} else {
					$errors['no_reci_found'] = __('You are neither the sender nor the recipient of this Message!', 'dexs-pm');
				}
				
			} else {
				$errors['no_pm_found'] = __('This message doesn\'t exist (anymore)!', 'dexs-pm');
			}
		}
		
		if($send == "write_to_users"){
			$get_reci = $users;
		}
	}
?>