<?php
	/*
	 *	CLASS EXTEND: dexsPMActions
	 *
	 *	@since	1.0.0 RC.1
	 *
	 *	WHAT IS THIS FILE FOR?
	 *		Primary for all PM (inter)actions, except the Dexs PM and User Settings. All 
	 *		important and needed - not for the design relevant - functions are stored here.
	 */
	
	class dexsPMActions extends dexsPMSystem {
		
		/*
		 *	SEND MESSAGE VAR
		 */
		# RECIPIENTS
		public $pm_rec = array();
		
		# SUBJECT
		public $pm_sub;
		
		# MESSAGE
		public $pm_mes;
		
		# USERS
		public $pm_users = array();		
		
		# WP_EDITOR SETTINGS
		public $editor_settings;
		
		# RECIPIENT LISTING STYLE
		public $rec_list_style;
		
		# IMAGES PERMISSION
		public $user_images;
		
		# ATTACHMENT PERMISSION
		public $user_attachments;

		# WARNING BOOL
		public $warn = false;
		
		# WARN MESSAGE
		public $warn_message;
		
		# ONE MESSAGE OBJECT
		protected $one_message;
		
		# REC USERS ARRAY
		protected $pm_rec_users = array();
		
		# REC USERS ARRAY - THE SECON
		protected $pm_rec_array = array();
		
		
		/*
		 *	PM FOLDERS
		 */
		private $messages_folder = array(
			0	=> "inbox",
			1	=> "outbox",
			2	=> "trash",
			4	=> "archive",
			5	=> "new",
			6	=> "all"
		);
		

		/*
		 *	COUNT MESSAGES
		 *	Count the messages for a specified or for all current_user folders.
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  string						ID or Name {0 = inbox || 1 = outbox || 2 = trash || 4 = archive || 5 = new || 6 = all}
		 *	@param  bool						True: Print the result || False: Return as an string (or an array, if @param1 == 6)
		 *	@return string / array / print		String: @param1 != 6 || Array: @param1 == 6 || Print: @param2 == True && @param1 != 6
		 */
		function count_messages($folder, $echo = false){
			global $wpdb, $current_user;
			get_currentuserinfo();
			
			if($folder == 3){ $folder = 2; }			
			if(!is_numeric($folder)){
				if(!$folder = array_search($folder, $this->messages_folder)){
					return false;
				}
			}
			$user = $current_user->ID;
			
			$pms = $wpdb->get_results("SELECT pm_sender, pm_recipients, pm_meta FROM ".$wpdb->prefix."".parent::$this->pm_table);
			$return = array(
				0	=> 0,
				1	=> 0,
				2	=> 0,
				4	=> 0,
				5	=> 0,
				6	=> 0
			);
			
			foreach($pms AS $pm){
				$meta = unserialize($pm->pm_meta);
				$recipients = unserialize($pm->pm_recipients);
				
				if($meta['sender']['id'] == $user || array_key_exists($user, $recipients)){
					$return[6]++;
					
					if($meta['sender']['id'] == $user){
						if($meta['sender']['table'] != 3){
							$return[$meta['sender']['table']]++;
						}
					} else {
						if(isset($return[$recipients[$user]['table']])){
							$return[$recipients[$user]['table']]++;
						}
						if($recipients[$user]['read'] == 0){
							$return[5]++;
						}
					}
				}
			}
						
			if($folder != 6){
				if($echo){
					print($return[$folder]); return;
				} else {
					return $return[$folder];
				}
			} else {
				$return_array = array(
					'inbox'		=> $return[0],
					'outbox'	=> $return[1],
					'trash'		=> $return[2],
					'archive'	=> $return[4],
					'new'		=> $return[5],
					'all'		=> $return[6]
				);
				return $return_array;
			}
		}
		
		
		/*
		 *	LOAD USER MESSAGES
		 *	Get all current_user messages for a specified folder.
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  string			type {0 = inbox || 1 = outbox || 2 = trash || 4 = archive}
		 *	@param  string			pm_id
		 *	@return array
		 */		
		function load_messages($folder = "", $id = ""){
			global $wpdb, $current_user;
			get_currentuserinfo();
			
			$user = $current_user->ID;
			$return = array();
						
			if(empty($folder) && $folder != "0"){
				$folder = "single";
			}
			if($folder == 3){ $folder = 2; }
			
			
			if(empty($id)){
				$messages = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."".parent::$this->pm_table." ORDER BY pm_send DESC");
			} else {
				$messages = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."".parent::$this->pm_table." WHERE pm_id = $id");
				
				if(empty($messages) || $messages == ""){
					$_GLOBAL['pm_error'] = __("This message does not exist or is not meant for you!", "dexs-pm");
					return false;
				}
			}
						
			foreach($messages AS $message){
				$meta = unserialize($message->pm_meta);
				$recipients = unserialize($message->pm_recipients);
				
				if(is_string($folder ) && $folder == "single"){
					if($meta['sender']['id'] == $user || array_key_exists($user, $recipients)){
						if(array_key_exists($user, $recipients)){
							$this->pm_action("1", $id);
						}
						
						$message->pm_meta = maybe_unserialize($message->pm_meta);
						$message->pm_recipients = maybe_unserialize($message->pm_recipients);

						return $message;
					} else {
						return false;
					}
				} else {
					if($meta['sender']['id'] == $user && $meta['sender']['table'] == $folder){
						$return[] = $message;
					}				
					if(array_key_exists($user, $recipients) && $recipients[$user]['table'] == $folder){
						$return[] = $message;
					}				
				}
			}
			
			if($id == ""){
				$num = $this->messages_folder[$folder];
				$max_m = parent::user_settings("load", $user, $num."_num");
				$return_this = array();
				if(isset($_GET['row'])){
					$off = $_GET['row'];
					$offset = ($_GET['row']-1);
					if($offset != 0){
						$offset = $offset*$max_m;
						$max_m = $off*$max_m;
					}
				} else { 
					$offset = 0; 
				}
				
				for($i = $offset; $i < $max_m; $i++){
					if(isset($return[$i])){
						$return_this[] = $return[$i];
					} else {
						break;
					}
				}
				return $return_this;
			}
			
			return $return;
		}
		
		
		/*
		 *	CHECK PM READ STATUS
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  string	type
		 *	@param  string/array	pm_id
		 *	@return bool
		 */		
		function is_pm_read($meta, $user){			
			$meta = maybe_unserialize($meta);
			
			if(isset($meta[$user])){
				if($meta[$user]['read']){
					return true;
				}
			}
			
			return false;			
		}
		
		
		/*
		 *	PM ACTIONS
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  string	type
		 *	@param  string/array	pm_id
		 */		
		function pm_action($action, $pm_id, $user = ""){
			global $wpdb, $current_user;
			get_currentuserinfo();
			
			if(empty($user)){
				if(function_exists("get_currentuserinfo")){
					$user = $current_user->ID;
				} else {
					$GLOBALS['pm_error'] = __("The message was not found! Please try again.", "dexs-pm");	
					return false;
				}
			}
			
			if(empty($pm_id)){
				$GLOBALS['pm_error'] = __("The message was not found! Please try again.", "dexs-pm");	
				return false;			
			}
			
			if($action > 5){
				$GLOBALS['pm_error'] = __("The action can not be performed! Please try again.", "dexs-pm");	
				return false;
			}
			
			if(!is_array($pm_id)){
				if($pm_id = explode(",", $pm_id)){} else {
					$pm_id = array($pm_id);
				}				
			}
			
			foreach($pm_id AS $pm){
				$message = $wpdb->get_results("SELECT pm_meta, pm_recipients, pm_send FROM ".$wpdb->prefix."".parent::$this->pm_table." WHERE pm_id = '$pm'");
				$pm_meta = maybe_unserialize($message[0]->pm_meta);
				$pm_rec_meta = maybe_unserialize($message[0]->pm_recipients);
				
				if($action == 3){
					/* DELETE THE MESSAGE */
					$delete = true;
					
					foreach($pm_rec_meta AS $key => $rec){
						if($key != $user){
							if($rec['table'] != 3){
								$delete = false;
							}
						}
					}					
					if($pm_meta['sender']['id'] != $user){
						if($pm_meta['sender']['table'] != 3){
							$delete = false;
						}
					}
					if($delete){
						$wpdb->delete($wpdb->prefix."".parent::$this->pm_table, array('pm_id' => $pm));
					}
				}

				if($pm_meta['sender']['id'] == $user){
					/* EDIT THE MESSAGE */
					if($action < 2){
						$GLOBALS['pm_error'] = __("The action can not be performed! Please try again.", "dexs-pm");	
						return false;
					}
					
					if($action >= 2 && $action < 5){
						$pm_meta['sender']['table'] = $action;
					}
					
					if($action == 5){
						$pm_meta['sender']['table'] = '1';
					}
					
					$wpdb->update(
						$wpdb->prefix."".parent::$this->pm_table, 
						array( 
							'pm_meta' => maybe_serialize($pm_meta),
							'pm_send' => $message[0]->pm_send
						), 
						array('pm_id' => $pm)
					);
					
				} else if(array_key_exists($user, $pm_rec_meta)){
					if($action < 2){
						$pm_rec_meta[$user]['read'] = $action;
						$pm_rec_meta[$user]['time'] = time();
					}
					
					if($action >= 2 && $action < 5){
						$pm_rec_meta[$user]['read'] = "1";
						$pm_rec_meta[$user]['table'] = $action;
					}
					
					if($action == 5){
						$pm_rec_meta[$user]['table'] = '0';
					}
					
					$wpdb->update( 
						$wpdb->prefix."".parent::$this->pm_table, 
						array(
							'pm_recipients' => maybe_serialize($pm_rec_meta),
							'pm_send' => $message[0]->pm_send
						), 
						array('pm_id' => $pm)
					);
				} else {
					$GLOBALS['pm_error'] = __("The message was not found! Please try again.", "dexs-pm");	
					return false;
				}
			}
			return true;
		}
		
		
		/*
		 *	CHECK MESSAGE
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  array	POST GLOBAL ARRAY
		 */		
		function send_pm_check(){
			global $current_user;
			get_currentuserinfo();
		
			$maxPM = parent::check_permissions("max_messages");
			$count = $this->count_messages("6", false)['all'];
			
			if($maxPM == "-1" || ($maxPM != "0" && $maxPM > $count)){
				
				$user = $current_user->ID;
				$this->pm_users = parent::load_users($user);
				$this->user_images = parent::check_permissions("images");
				$this->user_attachments = parent::check_permissions("attachments");
				$this->rec_list_style = parent::load_pm_settings("settings", "list_style");
				$this->editor_settings = array('dfw' => false, 'textarea_name' => 'the_message', 'media_buttons' => $this->user_images, 'quicktags' => false);
				
				# RECIPIENTS
				if(isset($_GET['rec'])){ $this->pm_rec = $_GET['rec']; }
				if(isset($_POST['recipients'])){ $this->pm_rec = $_POST['recipients']; }
				
				# SUBJECT
				if(isset($_GET['sub'])){ $this->pm_sub = $_GET['sub']; }
				if(isset($_POST['subject'])){ $this->pm_sub = $_POST['subject']; }
				
				# MESSAGE
				if(isset($_POST['the_message'])){ $this->pm_mes = $_POST['the_message']; }
				
				# PMID
				if(isset($_GET['pmid']) && !empty($_GET['pmid'])){
					$this->one_message = $this->load_messages("", $_GET['pmid']);
					
					if(!empty($this->one_message)){
						$this->pm_sub = "FW: ".$this->one_message->pm_subject;
						$this->pm_mes = "<br><br><small><b><i>".__("Original Message:", "dexs-pm")."</i></b><br>";
						
						if(isset($_GET['send_action']) && $_GET['send_action'] == 2){
							$this->pm_sub = "RE: ".$this->one_message->pm_subject;
							$this->pm_rec = implode(",", array_keys($this->one_message->pm_recipients)).",".$this->one_message->pm_sender;
						}
						
						$this->pm_mes .= $this->one_message->pm_message."</small>";
					}
				}
				
				if(!is_array($this->pm_rec)){
					if(preg_match("#,#", $this->pm_rec)){
						$this->pm_rec_array = explode(",", $this->pm_rec);
					} else {
						$this->pm_rec_array = array($this->pm_rec);
					}
					$this->pm_rec = "";
				}
				
				foreach($this->pm_users AS $user_id){
					if(!in_array($user_id->ID, $this->pm_rec_array) && $user_id->ID != $user){
						$this->pm_rec_users[$user_id->ID] = $user_id->display_name;					
					} else {
						if($user_id->ID != $user){
							$this->pm_rec[$user_id->ID] = $user_id->display_name;
						}
					}
				}
				return true;
			} else {
				$this->warn = true;
				$this->warn_message = __("An error occurred, please contact an administrator.", "dexs-pm");
				
				if($maxPM == "0"){
					$this->warn_message = __("You do not have the permission to send a private message.", "dexs-pm");
				} else
				
				if($maxPM <= $count){
					$this->warn_message = __("Limit exceeded! You must delete some private messages to send further ones.", "dexs-pm");
				}
				
				if(isset($_GET['action'])){
					$this->action = $_GET['action'];
				}
				return false;
			}
		}
		
		
		/*
		 *	SEND MESSAGE
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  array	POST GLOBAL ARRAY
		 */		
		function send_pm_action($post, $user = ""){
			global $wpdb, $current_user;
			get_currentuserinfo();
			
			if(empty($user)){
				if(function_exists("get_currentuserinfo")){
					$user = $current_user->ID;
				} else {
					return false;
				}
			}
			
			if(!isset($post['recipients']) || str_replace(" ", "", $post['recipients']) == ""){
				$GLOBALS['pm_error'] = __('No recipients specified! Please enter at least one recipient.', 'dexs-pm'); return false;
			}
			if(!isset($post['subject']) || str_replace(" ", "", $post['subject']) == ""){
				$GLOBALS['pm_error'] = __('No subject specified! Please enter a subject.', 'dexs-pm'); return false;
			}
			if(!isset($post['the_message']) || str_replace(" ", "", $post['the_message']) == ""){
				$GLOBALS['pm_error'] = __('No message specified! Please enter a message', 'dexs-pm'); return false;
			}
			if(isset($_FILES['attachment']) && $_FILES['attachment']['error'] != 4){
				$ext = explode(".", $_FILES['attachment']['name']);
				$ext = array_reverse($ext);						
				$allowed = explode(",", str_replace(' ','', parent::load_pm_settings("settings", "attachment_formats")));
				
				if($_FILES['attachment']['error'] > 0){
					$GLOBALS['pm_error'] = __('An unknown error appeared: The upload of your file failed! Please try again.', 'dexs-pm'); return false;
				}						
				if(!in_array(".".$ext[0], $allowed)){
					$GLOBALS['pm_error'] = __('The file extension is not allowed! Please use an allowed file extension.', 'dexs-pm'); return false;
				}
				if($_FILES['attachment']['size'] > (parent::load_pm_settings("settings", "attachment_size")*1024)){
					$GLOBALS['pm_error'] = __('The file is too large. Pack the file in an archive to reduce the file size.', 'dexs-pm'); return false;
				}
				
				$real_name = $_FILES['attachment']['name'];
				$_FILES['attachment']['name'] = time()."_".$real_name;						
				$meta['file'] = 1;
				$meta['file_meta'] = array(
					"name"		=>	$real_name,
					"file"		=>	$_FILES['attachment']['name'],
					"extension"	=>	".".$ext[0],
					"size"		=>	$_FILES['attachment']['size'],
					"uploader"	=>	$user
				);
			} else {
				$meta['file'] = 0;
			}
			$post['send_date'] = date("Y-m-d H:i:s");
			$post['send_user'] = $user;
			
			/* META FILES */
			$meta['sender'] = array(
				"id"	=>	$post['send_user'],
				"table"	=>	'1',
				'time'	=>	$post['send_date']
			);
			if(!isset($post['priority'])){
				$meta['priority'] = 0;
			} else {
				$meta['priority'] = $post['priority'];					
			}					
			
			/* RECIPIENTS META FILES */
			if(!is_array($post['recipients'])){
				$post['recipients'] = explode(",", $post['recipients']);
			}
			foreach($post['recipients'] AS $rec){
				$recipient[$rec] = array(
					'id'	=>	$rec,
					'read'	=>	'0',
					'table'	=>	'0',
					'time'	=>	$post['send_date']
				);
			}
			
			/* FILE UPLOAD && DB INSERT */
			if($meta['file']){
				if(!file_exists(wp_upload_dir()['basedir']."/dexspm_files")){
					if(!$upload_url = mkdir(wp_upload_dir()['basedir']."/dexspm_files", 644)){
						$upload_url = wp_upload_dir()['basedir'];
					}						
				} else {
					$upload_url = wp_upload_dir()['basedir']."/dexspm_files";
				}
			
				if(!move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_url."/".$_FILES['attachment']['name'])){
					$GLOBALS['pm_error'] = __('An unknown error appeared: The upload of your file failed! Please try again.', 'dexs-pm'); return false;
				}
			}					
			$wpdb->insert(
				$wpdb->prefix."".parent::$this->pm_table,
				array( 
					'pm_subject'		=>	$post['subject'],
					'pm_message'		=>	stripslashes($post['the_message']),
					'pm_sender'			=>	$post['send_user'],
					'pm_recipients'		=>	maybe_serialize($recipient),
					'pm_meta'			=>	maybe_serialize($meta),
					'pm_send'			=>	$post['send_date']
				)
			);
			
			if(!parent::load_pm_settings("settings", "email_note")){
				return true;
			} else {
				$getpm = $wpdb->get_results("SELECT pm_id FROM ".($wpdb->prefix."".parent::$this->pm_table)." WHERE 
											pm_subject = '".$post['subject']."' AND pm_meta = '".maybe_serialize($meta)."' AND pm_send = '".$post['send_date']."'");
				$getpmid = $getpm[0]->pm_id;
				
				if(parent::send_email_note($getpmid)){
					return true;
				} else {
					return false;
				}
			}
			return false;
		}
	}	

?>