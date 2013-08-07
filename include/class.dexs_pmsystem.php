<?php
	
	class dexsPMSystem{
		/*
		 *	PM FOLDER SYSTEM
		 */
		public $folder = array(
			0	=> "inbox",
			1	=> "outbox",
			2	=> "trash",
			3	=> "delete",
			4	=> "archive",
			5	=> "send",
			6	=> "user_settings"
		);
		
		/*
		 *	DB TABLE
		 */
		protected $pm_table = "dexs_pmsystem";
		
		/*
		 *	PM DEFAULT CONFIGURATION
		 */
		private $settings;
		private $user_settings;
		private $set_tabs = array(
			"dexs_pm_settings",
			"dexs_pm_permissions",
			"dexs_pm_email"
		);
		private $set_tab_defaults = array(
			"settings" => array(
				'email_note'		=> '0',
				'show_copy'			=> '1',
				'standard_role'		=> 'subscriber',
				'use_archive'		=> '1',
				'use_trash'			=> '1',
				'list_style'		=> '1',
				'use_attachments'	=> '1',
				'attachment_type'	=> '0',
				'attachment_size'	=> '1024',
				'attachment_formats' => '.jpeg, .jpg, .gif, .png, .tar, .zip, .7z, .rar',
				'use_backend'		=> '1',
				'backend_style'		=> '1',
				'backend_toolbar'	=> '1',
				'backend_navi'		=> '1',
				'use_frontend'		=> '1',
				'frontend_style' 	=> '0',
				'frontend_theme' 	=> 'the_system'
			),
			"permissions" => array(
				"administrator" 	=> array(
					"activate"		=> "1",
					"max_number"	=> "-1",
					"enable"		=> array("images", "attachments"),
					"access"		=> array("frontend", "backend")
				),
				"editor" 			=> array(
					"activate"		=> "1",
					"max_number"	=> "-1",
					"enable"		=> array("images", "attachments"),
					"access"		=> array("frontend", "backend")
				),
				"author" 			=> array(
					"activate"		=> "1",
					"max_number"	=> "150",
					"enable"		=> array("images", "attachments"),
					"access"		=> array("frontend", "backend")
				),
				"contributor" 		=> array(
					"activate"		=> "1",
					"max_number"	=> "100",
					"enable"		=> array("attachments"),
					"access"		=> array("frontend", "backend")
				),
				"subscriber" 		=> array(
					"activate"		=> "1",
					"max_number"	=> "50",
					"enable"		=> array(),
					"access"		=> array("frontend", "backend")
				),
				
				"exclude_users" 	=> "",
				"include_users" 	=> ""
			),
			"email" => array(
				"mail_address"		=> "",
				"reply_address"		=> "",
				"mail_subject"		=> "",
				"mail_message"		=> ""
			)
		);
		private $user_set_defaults = array(
			"inbox_num"			=> "20",
			"outbox_num"		=> "20",
			"trash_num"			=> "20",
			"archive_num"		=> "20",
			"user_email_note"	=> "0"
		);
		
					
		/*
		 *	LOAD THE SETTINGS FOR THE DEXS PM SYSTEM
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param 	string (optional)		{settings || permissions || email}
		 *	@param 	string (optional) 		Get an specific option
		 *	@return array / string			If the second parameter is set and valid the result returned as a string, otherwise as an array.
		 */
		function load_pm_settings($tab = "settings", $set = ""){
			if($tab == "email"){
				$this->set_tab_defaults['email'] = array(
					"mail_address"		=> get_bloginfo("name")." <notice@".$_SERVER['SERVER_NAME'].">",
					"reply_address"		=> get_bloginfo("name")." <notice@".$_SERVER['SERVER_NAME'].">",
					"mail_subject"		=> "You have a new private message on ".get_bloginfo('name')."!",
					"mail_message"		=> ""
				);
			}
			$this->settings = get_option("dexs_pm_".$tab, $this->set_tab_defaults[$tab]);
			
			if($tab == "email"){
				if(file_exists(DPM_FOLDER."mail.php")){
					$dexs_mail = file_get_contents(DPM_FOLDER."mail.php");
					
					$this->settings['mail_message'] = $dexs_mail;
				}
			}
			
			if(!empty($set) && isset($this->settings[$set])){
				return $this->settings[$set];
			}
			return $this->settings;	
		}

		
		/*
		 *	SAVES THE SETTINGS FOR THE DEXS PM SYSTEM
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param 	string 			 	{0 = settings || 1 = permissions || 2 = email}
		 *	@param 	array 			 	HTTP $_POST Array
		 *	@return bool				True, if the insert and check process is ended successfully. False, if not.
		 */
		function set_pm_settings($tab, $post){
			
			unset($post['action']);
			unset($post['tab']);
			unset($post['save']);
			unset($post['dexs_pm']);
			
			switch($tab){
				case '0':
					$act = "settings";
	
					/* SECURITY CHECK */
					if(phpversion() > "5.1.0"){
						$check = array_diff_key($post, $this->set_tab_defaults[$act]);		# If the post array contains other keys than the default array, it will return false.
						if(!empty($check)){
							return false;
						}
					} else {
						foreach($post AS $key => $values){
							if(!array_key_exists($key, $this->set_tab_defaults[$act])){		# Contains the post array all important informations?
								return false;
							}
						}
						if(count($post) != count($this->set_tab_defaults[$act])){			# Contains the post array MORE informations than needed?
							return false;
						}
					}
					$insert = array(
						'email_note'		=> $post['email_note'],
						'show_copy'			=> $post['show_copy'],
						'standard_role'		=> $post['standard_role'],
						'use_archive'		=> "1",
						'use_trash'			=> "1",
						'list_style'		=> $post['list_style'],
						'use_attachments'	=> "1",
						'attachment_type'	=> "0",
						'attachment_size'	=> $post['attachment_size'],
						'attachment_formats' => $post['attachment_formats'],
						'use_backend'		=> "1",
						'backend_style'		=> $post['backend_style'],
						'backend_toolbar'	=> $post['backend_toolbar'],
						'backend_navi'		=> $post['backend_navi'],
						'use_frontend'		=> "1",
						'frontend_style' 	=> $post['frontend_style'],
						'frontend_theme' 	=> $post['frontend_theme']
					);
					break;
					
				case '1':
					$act = "permissions";
					
					/* SECURITY CHECK */
					if(!isset($post['exclude_users']) || !isset($post['include_users'])){
						return false;
					}
					foreach($post AS $key => $values){
						if($key != "exclude_users" && $key != "include_users"){				
							if(get_role($key) == NULL){
								return false;
							}
							
							if(count($post[$key]) != "4" || !array_key_exists("activate", $post[$key]) || !array_key_exists("max_number", $post[$key]) || 
							!array_key_exists("enable", $post[$key]) || !array_key_exists("access", $post[$key])){
								return false;
							}
						}
					}
					$insert = $post;
					break;
					
				case '2':
					$act = "email";
					$message = $post['mail_message'];
					
					if(file_put_contents(DPM_FOLDER."mail.php", $message) === false){
						return false;
					}
					$post['mail_message'] = "";
					unset($post['test_mail_recipient']);
					if(isset($post['send_test_mail'])){ unset($post['send_test_mail']); }
					
					/* SECURITY CHECK */
					if(phpversion() > "5.1.0"){
						$check = array_diff_key($post, $this->set_tab_defaults[$act]);		# If the post array contains other keys than the default array, it will return false.
						if(!empty($check)){
							return false;
						}
					} else {
						foreach($post AS $key => $values){
							if(!array_key_exists($key, $this->set_tab_defaults[$act])){		# Contains the post array all important informations?
								return false;
							}
						}
						if(count($post) != count($this->set_tab_defaults[$act])){			# Contains the post array MORE informations than needed?
							return false;
						}
					}
					$insert = $post;
					break;
					
				default:
					return false;
					break;
			}			
			
			if(!get_option($this->set_tabs[$tab])){
				add_option($this->set_tabs[$tab], $insert, '', 'no');
				return true;
			} else {
				if(get_option($this->set_tabs[$tab]) == $insert){
					return true;
				} else {
					update_option($this->set_tabs[$tab], $insert);
					return true;
				}
			}			
			return false;
		}


		/*
		 *	RESETS THE SETTINGS OF THE CURRENT SETTINGS-TAB
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param 	string 			 	{0 = settings || 1 = permissions || 2 = email}
		 *	@return bool				True, if the delete prozess ended successfully. False, if not.
		 */		
		function set_to_default($tab){
			
			if(!get_option($this->set_tabs[$tab])){
				return true;
			} else {
				if($tab == 1){
					$insert = $this->set_tab_defaults['permissions'];
					$insert['exclude_users'] = $this->load_pm_settings("permissions", "exclude_users");
					$insert['include_users'] = $this->load_pm_settings("permissions", "include_users");					
					
					if(delete_option($this->set_tabs[$tab])){
						if(add_option($this->set_tabs[$tab], $insert, "", "no")){
							return true;
						}
					}
				} else {
					if($tab == 2){
$d_mail ='Hello %SEND_NAME%!

<p>You received a new private message on our website: %HOME_TIL%.</p>
<p style="padding:0;margin:0;line-height:16px;">Sender: <i style="color:#333;background-color:#E6E6FA;padding:3px;">%RECI_NAME%</i></p>
<p style="padding:0;margin:0;line-height:16px;">Subject: <i style="color:#333;background-color:#E6E6FA;padding:3px;">%PM_SUB%</i></p>
<p style="padding:0;margin:0;line-height:16px;">Send Date: <i style="color:#333;background-color:#E6E6FA;padding:3px;">%PM_DATE%  at %PM_TIME% clock</i><p>
<p><a href="%PM_F_LINK%" style="text-decoration:none;color:#fff;background-color:#4682B4;padding:4px;">Click here</a> and go straight to your inbox folder.

<a href="%HOME_URL%" style="text-decoration:none;color:#fff;background-color:#4682B4;padding:4px;">Or Click here</a> and go straight to our website.</p>

<p style="padding:0;margin:0;line-height:16px;">Sincerely,
Your %HOME_TIL% Team.</p>

<hr style="border-left:none;border-right:none;border-bottom:none;border-top:1px solid #aaa;"><i style="color:#999;"><small>This email was generated automatically, so please do not reply to this email.</small></i>';

						if(delete_option($this->set_tabs[$tab])){
							if(file_put_contents(DPM_FOLDER."mail.php", $d_mail) === false){
								return false;
							}
							return true;
						}
					} else {
						if(delete_option($this->set_tabs[$tab])){
							return true;
						}
					}
				}
			}
			return false;
		}
		
		
		/*
		 *	LOAD THE PM FRONTEND TEMPLATES
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  bool					If true = The result will be formatted displayed
		 *	@return array || output			If false = The result returns as an array (without the “option”-HTML elements)
		 */		
		function load_pm_templates($echo = false){
		
			$dir = DPM_FOLDER."templates/";
			$themes = array(); $templates = array();
			
			# Search directory
			$handle = opendir($dir);			
			if (false === $handle) {
				die;
			}
			while (false !== ($file = readdir($handle))) {
				if ('.' == $file || '..' == $file) {
					continue;
				}
				if (is_file($dir.$file)){
					continue;
				}
				$themes[] = $file;
			}
			closedir($handle);	
			
			# Check the existence of the theme.ini and the index.php file
			for($i = 0; $i < count($themes); $i++){
				if(file_exists($dir.$themes[$i]."/theme.ini") && file_exists($dir.$themes[$i]."/index.php")){
					if($load_template = parse_ini_file($dir.$themes[$i]."/theme.ini")){
						$templates[] = array_merge(array("id" => $themes[$i]), $load_template);
					}
				}
			}
			
			# Return or Print the result
			if($echo){
				if(count($templates) == 0){
					print("<option value='-1'>We found no template D:</option>");
					return;
				}
				$current = $this->load_pm_settings("settings", "frontend_theme");
				foreach($templates AS $template){
					$cur = "";
					if($current == $template['id']){
						$cur = "selected='selected'";
					}
					print("<option id='".$template['id']."' value='".$template['id']."' $cur>".$template['name']." (".$template['version'].")</option>");
				}
				return;
			} else {
				return $tempaltes;
			}
		}
		
		
		/*
		 *	SEND eMAIL NOTIFICATION
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  string			PM ID
		 *	@param  string			TEXT eMAIL
		 *	@return bool			True, If the message has been sent successfully. False, if not.
		 */		
		function send_email_note($pmid, $demo = NULL){
			global $dexsPMA;
			
			if($pmid == NULL && $demo == true){
				if(!is_email($demo)){
					$GLOBALS['pm_error'] = __("The eMail address for the Demo Mail is invalid! Please enter a valid eMail address!", "dexs-pm");
					return false;
				}
				
				/* 4 DEMO */ $user_info = get_userdata("1");
				/* 4 DEMO */ $pm_sender_id = array("id" => "1", "name" => $user_info->display_name);
				/* 4 DEMO */ $pm_recipient[] = array("id" => "1", "name" => $user_info->display_name, "email" => $demo);
				
				/* 4 DEMO */ $pm_subject = "[Dexs PM System] Test eMail - Lorem Ipsum";
				/* 4 DEMO */ $pm_message = "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. ";
				/* 4 DEMO */ $pm_date = date(get_option('date_format'));
				/* 4 DEMO */ $pm_time = date(get_option('time_format'));
				
				/* 4 DEMO */ $frontend = "&dpm=folder&folder=0";
				/* 4 DEMO */ $backend = "&folder=0";
				
			} else {
				if(!$this->load_pm_settings("settings", "email_note")){
					return true;
				}
				
				if(!$single_m = $dexsPMA->load_messages(NULL, $pmid)){
					$GLOBALS['pm_error'] = __("The private message could not be found.!", "dexs-pm");
					return false;
				}
				
				$pm_id = $single_m->pm_id;
				$sender_info = get_userdata($single_m->pm_sender);
				$pm_sender_id = array("id" => $sender_info->ID, "name" => $sender_info->display_name);
				$pm_recipient = array_keys($single_m->pm_recipients);
				
				$pm_subject = $single_m->pm_subject;
				$pm_message = $single_m->pm_message;
				
				$datetime = explode(" ", $single_m->pm_send);				
				$date = explode("-", $datetime[0]);
				$time = explode(":", $datetime[1]);
				$timestamp = mktime($time[0],$time[1],$time[2],$date[1],$date[2],$date[0]); 
				
				$pm_date = date(get_option('date_format'), $timestamp);
				$pm_time = date(get_option('time_format'), $timestamp);
				
				$frontend = "&dpm=folder&folder=0&read=1&pmid=".$pm_id;
				$backend = "&folder=0&read=1&pmid=".$pm_id."&dpm=folder";
				
				/* CHECK RECIPIENTS */
				foreach($pm_recipient AS $pm_rec){
					if($this->user_settings("load", $pm_rec, "user_email_note")){
						$user_info = get_userdata($pm_rec);
						$pm_recipient_new[] = array("id" => $user_info->ID, "name" => $user_info->display_name, "email" => $user_info->user_email);
					}
				}
				if(!isset($pm_recipient_new)){
					return true;
				}
				$pm_recipient = $pm_recipient_new;
			}
			
			# eMAIL DATA (mail_address, reply_address, mail_subject, mail_message)
			$email = $this->load_pm_settings("email");
			$mail_address = $email["mail_address"];
			$reply_address = $email["reply_address"];
			$mail_subject = $email["mail_subject"];
			$mail_message = $email["mail_message"];
			
			# BLOGINFO
			$blog_title = get_bloginfo("name");
			$blog_url = get_bloginfo("url");
			$blog_mail = get_bloginfo("admin_mail");
			
			# BACKEND
			if($this->load_pm_settings("settings", "backend_navi")){
				$pm_backend = "admin.php?page=pm".$backend;
			} else {
				$pm_backend = "users.php?page=pm".$backend;
			}
			$pm_backend = get_bloginfo("url")."/wp-admin/".$pm_backend;
			
			# FRONTEND
			if($this->get_frontend_id() != false){
				$pm_frontend = get_bloginfo("url")."/?page_id=".$this->get_frontend_id().$frontend;
			} else {
				$pm_frontend = $pm_backend;
			}
			
			# CONVERT eMAIL MESSAGE && SUBJECT
			$search = array("%HOME_TIL%", "%HOME_URL%", "%ADM_MAIL%", "%SEND_NAME%", "%PM_SUB%", "%PM_TEXT%", "%PM_DATE%", "%PM_TIME%", "%PM_F_LINK%", "%PM_B_LINK%");
			$replace = array(
				$blog_title,
				$blog_url,
				$blog_mail,
				$pm_sender_id['name'],
				$pm_subject,
				$pm_message,
				$pm_date,
				$pm_time,
				$pm_frontend,
				$pm_backend
			);
			$mail_message_con = str_replace($search, $replace, stripslashes(nl2br($mail_message)));
			
			if(preg_match("#\%PM_EXC_(.*)\%#", $mail_message_con, $length)){			
				$message_split = preg_split("#\%PM_EXC_(.*)\%#Uis", $mail_message_con);
				$pm_excerpt = substr($pm_message, 0, $length[1]);
				
				$final_message = "<html><body style='color: #444;font-family: \"Open Sans\", Helvetica, Arial, sans-serif;'>".$message_split[0].$pm_excerpt.$message_split[1]."</body></html>";	
			} else {
				$final_message = "<html><body style='color: #444;font-family: \"Open Sans\", Helvetica, Arial, sans-serif;'>".$mail_message_con."</body></html>";
			}
			
			/* SEND NOTIFICATION eMAIL */
			foreach($pm_recipient AS $pm_rec){
				$final_message = str_replace("%RECI_NAME%", $pm_rec["name"], $final_message);
				
				$header = 'MIME-Version: 1.0' . "\r\n";
				$header .= 'Content-type: text/html; charset=utf-8'."\r\n";
				$header .= 'To: '.$pm_rec["email"]."\r\n";
				$header .= 'From: '.$mail_address."\r\n";		
				$header .= 'Reply-To: '.$reply_address."\r\n";		
				$header .= 'X-Mailer: PHP/'.phpversion();

				$search  = array ('Ä', 'Ö', 'Ü','ä', 'ö', 'ü', 'ß');
				$replace = array ('&Auml;', '&Ouml;', '&Uuml;', '&auml;', '&ouml;', '&uuml;', '&szlig');
				$send_message = str_replace($search, $replace, $final_message);
				
				if(!mail($pm_rec["email"], $mail_subject, $send_message, $header)){
					$GLOBALS['pm_error'] = __("Unknown Error: The eMail could not be sent!", "dexs-pm");
					return false;
				}
			}
			
			return true;
		}
		
		
		/*
		 *	GET FRONTEND ID
		 *	
		 *	@since 	1.0.0
		 *
		 *	@return string / bool		True = page_id || False = False
		 */		
		function get_frontend_id(){
			if($options = get_option("dexs_pm_frontend")){
				if(is_array($options)){
					$page_r = maybe_unserialize($options['ids']);
					for($i = 0; $i < count($page_r); $i++){
						$post = get_post($page_r[$i]);
						if(!empty($post) && $post->post_status == "publish"){
							if(preg_match("#(.*)\[pm_system\](.*)#", $post[$i]->post_content)){
								return $post[$i]->ID;
							}
						}
					}
				} else {
					$post = get_post($options);
					if(!empty($post) && $post->post_status == "publish"){
						if(preg_match("#(.*)\[pm_system\](.*)#", $post->post_content)){
							return $options;
						}
					}
				}
			}
			
			$pages = get_pages(array('sort_order' => 'ASC',	'sort_column' => 'post_content', 'post_status' => 'publish')); 
			$page_id = array();
			foreach($pages AS $page){
				if(preg_match("#(.*)\[pm_system\](.*)#", $page->post_content)){
					$page_id[] = $page->ID;
				}
			}
			
			if(count($page_id) != 0){
				if(count($page_id) > 1){
					$insert = array("error" => true, "ids" => maybe_serialize($page_id));
					if(!$options){
						add_option("dexs_pm_frontend", $insert, "", "no");
					} else {
						update_option("dexs_pm_frontend", $insert);
					}
					return $page_id[0];
				} else {
					$insert = $page_id[0];
					if(!$options){				
						add_option("dexs_pm_frontend", $insert, "", "no");
					} else {
						update_option("dexs_pm_frontend", $insert);
					}				
					return $insert;
				}
			}			
			return false;
		}
		
		
		
		/*
		 *	CHECK USER PERMISSIONS
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  string			“system” 		=> 	Has the user access to the pm system?				(returns as bool)
		 *							“messages”		=>	Can the user send still more messages? 				(returns as bool)
		 *							“max_messages”	=>	How many messages can the user send? 				(returns as string)
		 *							“backend”		=>	Has the user access to the backend pm system?		(returns as bool)
		 *							“frontend”		=>	Has the user access to the frontend pm system?		(returns as bool)
		 *							“attachments”	=>	Has the user the permission to send attachments?	(returns as bool)
		 *							“images”		=>	Has the user the permission to send images?			(returns as bool)
		 *	@return string / bool	See above
		 */		
		function check_permissions($type, $user = ""){
			global $wpdb, $wp_roles, $current_user;
			get_currentuserinfo();
			
			$permissions = $this->load_pm_settings("permissions");
			
			/* GET USER AND HIS ROLE */
			if($user == " " || $user == "" || empty($user)){
				if(is_user_logged_in()){
					$user = $current_user->ID;
				} else {
					return false;
				}
			}
			$userdata = get_userdata($user);
			$userrole = $userdata->roles[0];
			
			if(isset($permissions[$userrole])){
				$user_settings = $permissions[$userrole];
			} else {
				$user_settings = $permissions[$this->load_pm_settings("settings", "standard_role")];
			}
			
			
			/* CHECK */
			if(preg_match('#\b'.$user.'\b#', $permissions['exclude_users'])){
				return false;
			}
			
			if(!$user_settings['activate'] && !preg_match('#\b'.$user.'\b#', $permissions['include_users'])){
				return false;
			}
			
			switch($type){
				case "system":
					if(!$user_settings['activate']){
						return false;
					}
					break;
					
				case "messages":
					if(!$user_settings['max_number']){
						return false;
					}
					break;
					
				case "max_messages":
					$return_mes = (string)$user_settings['max_number'];
					return $return_mes;
					break;
					
				case "backend":
					if(!in_array("backend", $user_settings['access'])){
						return false;
					}
					break;
					
				case "frontend":
					if(!in_array("frontend", $user_settings['access'])){
						return false;
					}
					break;
					
				case "attachments":
					if(!in_array("attachments", $user_settings['enable'])){
						return false;
					}
					break;
					
				case "images":
					if(!in_array("images", $user_settings['enable'])){
						return false;
					}
					break;
			}
			return true;
		}
		
		
		/*
		 *	LOAD USERS
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  string	type		user_id
		 *	@return object				users
		 */		
		function load_users($user){
			global $wpdb;
			
			$return = $wpdb->get_results("SELECT display_name, ID FROM $wpdb->users WHERE ID != '".$user."' ORDER BY display_name ASC");
			
			return $return;
		}
		
		
		/*
		 *	LOAD AND SET USER SETTINGS
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  string						“load”			Get the User Settings
		 *										“update”		Update the User Settings
		 *										“reset”			Reset the User Settings
		 *	@param  string / array				FOR “update”	Data to be updated
												FOR “load”		user_ID
		 *	@param  string						FOR “load”		Get an specific user option
		 *	@return bool / array / string		
		 */		
		function user_settings($type, $data = "", $set = ""){
			global $current_user;
			get_currentuserinfo();
			
			if($type == "load"){
				if(empty($data) || $data == " "){
					$data = $current_user->ID;
				}
				
				$this->user_settings = get_user_meta($data, "dexs_pm_settings", true);
				
				if(empty($this->user_settings)){
					$this->user_settings = $this->user_set_defaults;
				}				
				if(!empty($set) && isset($this->user_settings[$set])){
					return $this->user_settings[$set];
				}
				
				return $this->user_settings;				
			} else {
				$user = $current_user->ID;
				
				if($type == "reset"){
					if(!delete_user_meta($user, 'dexs_pm_settings')){
						return false;
					}
					return true;
				} else {
					if($data["inbox_num"] > 100 || $data["inbox_num"] < 5){
						$insert["inbox_num"] = 20;
					} else {
						$insert["inbox_num"] = $data["inbox_num"];
					}
					if($data["outbox_num"] > 100 || $data["outbox_num"] < 5){
						$insert["outbox_num"] = 20;
					} else {
						$insert["outbox_num"] = $data["outbox_num"];
					}
					if($data["trash_num"] > 100 || $data["trash_num"] < 5){
						$insert["trash_num"] = 20;
					} else {
						$insert["trash_num"] = $data["trash_num"];
					}
					if($data["archive_num"] > 100 || $data["archive_num"] < 5){
						$insert["archive_num"] = 20;
					} else {
						$insert["archive_num"] = $data["archive_num"];
					}
					if(!isset($data['user_email_note'])){
						$insert['user_email_note'] = 0;
					} else {
						$insert['user_email_note'] = $data['user_email_note'];
					}
					
					if(!get_user_meta($user, "dexs_pm_settings", true)){
						if(add_user_meta($user, "dexs_pm_settings", $insert, true)){
							return true;						
						} else {
							return false;
						}
					} else {
						if(get_user_meta($user, "dexs_pm_settings", true) == $insert){
							return true;
						} else {
							update_user_meta($user, "dexs_pm_settings", $insert);
							return true;
						}
					}
					return false;
				}				
			}
		}
	}
	
?>