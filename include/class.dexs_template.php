<?php
	/*
	 *	CLASS EXTEND: dexsPMTemplate
	 *
	 *	@since	1.0.0 RC.1
	 *
	 *	WHAT IS THIS FILE FOR?
	 *		Primary for the front-End template. This file contains all template functions and 
	 *		integration processes. Also a few functions will be used in the backend interface.
	 */
	 
	final class dexsPMTemplate extends dexsPMActions{
		
		/*
		 *	TEMPLATE VARIABLES
		 */
		public $pm_folder;
		public $pm_read = false;
		public $pm_offset;
		public $pm_max_view;
		public $use_page;
		public $pm_post_id;
		public $cur_user;
		public $cur_pm_id = NULL;
		public $frontend_id;
		
		/*
		 *	TEMPLATE DESIGN
		 */
		public $table_header = array();
		public $bulk_actions = array();
		
		/*
		 *	TEMPLATE SETTINGS
		 */
		public $template_style;
		private $template_id;
		private $template_folder;
		private $template_dir;
		private $list_style;
		
		
		/*
		 *	PHP 5.0 CONSTRUCTOR
		 */
		function __construct(){
			global $dexsPM;
			
			$this->dexsPMTemplate();	/* I know that is really unnecessary :D */
		}
		
		
		/*
		 *	PHP 4.0 CONSTRUCTOR
		 */
		function dexsPMTemplate(){
			
			/* 
			 *	SET TEMPLATE SETTINGS && CONFIG 
			 */
			$this->template_id = parent::load_pm_settings("settings", "frontend_theme");
			$this->template_style = parent::load_pm_settings("settings", "frontend_style");
			$this->template_folder = DPM_FOLDER."templates/";			
			$this->template_dir = $this->template_folder.$this->template_id;		
			$this->list_style = parent::load_pm_settings("settings", "list_style");	
			$this->frontend_id = parent::get_frontend_id();
		}
		
		
		/*
		 *	LOAD PM HEADER
		 */		
		function load_pm_template_header(){
			global $dexsPM, $current_user;
			get_currentuserinfo();
			
			/* 
			 * SET CURRENT USER ID 
			 */
			$this->cur_user = $current_user->ID;
			$this->pm_post_id = get_the_ID();
			$user_settings = parent::user_settings("load", $this->cur_user);
			
			/* 
			 *	GET CURRENT FOLDER
			 */
			if(isset($_GET['folder'])){
				if($_GET['folder'] < 8){
					$this->pm_folder = $_GET['folder'];
				} else {
					$this->pm_folder = 0;
				}
			} else {
				$this->pm_folder = 0;
			}
			
			/*
			 *	GET READ STATUS
			 */
			if(isset($_GET['read']) && isset($_GET['pmid'])){
				$this->pm_read = true;
				$this->cur_pm_id = $_GET['pmid'];
			}
			
			
			if($this->pm_folder < 5){
				if(!$this->pm_read){
					$this->pm_messages = parent::load_messages($this->pm_folder);					# LOAD MESSAGES
				} else {
					$this->pm_messages = parent::load_messages($this->pm_folder, $this->cur_pm_id);	# LOAD MESSAGE				
				}
				
				$this->pm_counter = parent::count_messages($this->pm_folder, false);				# COUNT MESSAGES
				if(isset($_GET['row'])){
					$this->pm_offset = $_GET['row']++;
				} else {
					$this->pm_offset = 1;
				}
				
				$this->pm_max_view = $user_settings[$dexsPM->folder[$this->pm_folder]."_num"];
			}
		}

		
		/*
		 *	LOAD USERS STRUCTUR
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  string	type {0 = inbox || 1 = outbox || 2 = trash || 4 = archive}
		 *	@param  string	array
		 *	@return string / print
		 */		
		function load_user_structur($folder, $message){
			global $wpdb;
			if($folder == 3){ $folder = 2; }
			
			if($folder == 0){
			
				$pm_meta = maybe_unserialize($message->pm_meta);
				$user = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '".$pm_meta['sender']['id']."'");
				$user = $user[0];
				print($user->display_name); return;
				
			} else if($folder == 1){
			
				$pm_recipients = maybe_unserialize($message->pm_recipients);				
				foreach($pm_recipients AS $user_id => $rec){
					$user = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '".$user_id."'");
					$user = $user[0];
					$user_name = $user->display_name;
					if(!$rec['read']){ 
						$read = "(<a href='#' title='".__("This user has not yet read this message.", "dexs-pm")."'>✘</a>)"; 
					} else { 
						$read = "(<a href='#' title='".__("This user has already read this message.", "dexs-pm")."'>✔</a>)"; 
					}
					
					$display[] = $user_name." ".$read;
				}
				print_r(implode(", ", $display)); return;	
				
			} else {
			
				$pm_meta = maybe_unserialize($message->pm_meta);
				$pm_recipients = maybe_unserialize($message->pm_recipients);
				
				$user = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '".$pm_meta['sender']['id']."'");
				$user = $user[0];
				
				print("<b>".$user->display_name."</b> (".__("Sender", "dexs-pm").")<br>");
				
				foreach($pm_recipients AS $user_id => $rec){
					$user = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE ID = '".$user_id."'");
					$user = $user[0];
					$user_name = $user->display_name;
					if(!$rec['read']){ 
						$read = "(<a href='#' title='".__("This user has not yet read this message.", "dexs-pm")."'>✘</a>)"; 
					} else { 
						$read = "(<a href='#' title='".__("This user has already read this message.", "dexs-pm")."'>✔</a>)"; 
					}
					
					$display[] = $user_name." ".$read;
				}
				print_r(implode(", ", $display)); return;	
			}
		}


		/*
		 *	GET AND PRINT BULK ACTIONS
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  string				FOLDER ID
		 *	@param  bool				ECHO (True = PRINT || False = RETURN AS AN ARRAY)
		 *	@return array / print
		 */
		function get_bulk_actions($folder = NULL, $echo = false){
			
			if($folder == NULL){
				$folder = $this->pm_folder;
			}
			
			$this->bulk_actions = array(
			0	=> 	array(
						"4" =>	__("Move to Archive", "dexs-pm"),
						"2" =>	__("Move to Trash", "dexs-pm"),
						"1"	=>	__("Mark as Read", "dexs-pm"),
						"0"	=>	__("Mark as Unread", "dexs-pm")
					),
			1	=> 	array(
						"4" =>	__("Move to Archive", "dexs-pm"),
						"2" =>	__("Move to Trash", "dexs-pm")
					),
			2	=> 	array(
						"5" =>	__("Restore", "dexs-pm"),
						"3" =>	__("Delete Permanently", "dexs-pm")
					),		
			4	=> 	array(
						"5" =>	__("Restore", "dexs-pm"),
						"2" =>	__("Move to Trash", "dexs-pm")
					)
			);
			
			if($echo){
				foreach($this->bulk_actions[$folder] AS $act => $desc){
					echo "<option value=\"$act\">$desc</option>";
				}
				
			} else {
				return $this->bulk_actions[$folder];
			}
		}
		
		
		/*
		 *	GET AND PRINT TABLE HEADER
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  string				FOLDER ID
		 *	@param  bool				ECHO (True = PRINT || False = RETURN AS AN ARRAY)
		 *	@param  bool				BACKEND FUNCTION (True = Is backend || False = Is Frontend)
		 *	@return array / print
		 */
		function get_table_header($folder, $echo = false, $backend = false){
			
			$this->table_header = array(
				0 => array(
					"20"	=> __('Subject', 'dexs-pm'),
					"25"	=> __('Sender', 'dexs-pm'),
					"38"	=> __('Excerpt', 'dexs-pm'),
					"15"	=> __('Date', 'dexs-pm')
				),
				1 => array(
					"20"	=> __('Subject', 'dexs-pm'),
					"25"	=> __('Recipients', 'dexs-pm'),
					"38"	=> __('Excerpt', 'dexs-pm'),
					"15"	=> __('Date', 'dexs-pm')
				),
				2 => array(
					"20"	=> __('Subject', 'dexs-pm'),
					"25"	=> __('Users', 'dexs-pm'),
					"38"	=> __('Excerpt', 'dexs-pm'),
					"15"	=> __('Date', 'dexs-pm')
				),
				4 => array(
					"20"	=> __('Subject', 'dexs-pm'),
					"25"	=> __('Users', 'dexs-pm'),
					"38"	=> __('Excerpt', 'dexs-pm'),
					"15"	=> __('Date', 'dexs-pm')
				)
			);
			
			
			if(!$backend){
				if($this->template_style == 0){			
					$this->table_header[$folder] = array(
						"40"	=>	$this->table_header[$folder][20],
						"35"	=>	$this->table_header[$folder][25],
						"23"	=>	$this->table_header[$folder][15]
					);
				}
			}
			
			if($echo){
				$foreach = $this->table_header[$folder];
				foreach($foreach AS $tkey => $tdesc){
					$align = "left";
					if($tkey == "15"){ $align = "center"; }
					print("<th style='width:$tkey%;text-align:$align;' scope='col' class='manage-column'>$tdesc</th>");
				}
				return;
			}
			
			return $this->table_header[$folder];
		}
		
		
		/*
		 *	GET FOLDER URL
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  string				FOLDER ID
		 *	@param  bool				ECHO (True = PRINT || False = RETURN AS AN ARRAY)
		 *	@return string / print
		 */
		function get_folder_url($folder = "", $echo = false){

			$return = "?page_id=".$this->frontend_id."&dpm=folder&folder=".$folder;
			
			if($echo){
				print($return);
				return;
			}
			return $return;
		}
		
		/*
		 *	GET TEMPLATE URL
		 *	
		 *	@since 	1.0.0
		 *
		 *	@return string
		 */
		function get_template_folder(){
			return $this->template_dir."/";
		}
		
		/*
		 *	GET CSS STYLESHEET URL
		 *	
		 *	@since 	1.0.0
		 *
		 *	@return string
		 */
		function get_template_css(){
		
			return plugins_url("/templates/".$this->template_id."/style.css", dirname(__FILE__));
		}
		
		/*
		 *	GET AND COMPARE CURRENT PM FOLDER
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  string				FOLDER ID
		 *	@return string
		 */
		function current_folder($folder = ""){
			
			if(!empty($folder)){
				if($this->pm_folder == $folder){
					return true;
				} else {
					return false;
				}
			} else {
				return $this->pm_folder;
			}

		}
		
		/*
		 *	GET AND PRINT ATTACHMENT URL
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param 	bool			echo
		 *	@return string / print
		 */
		function pm_attachment_url($echo = false){
			$basedir = wp_upload_dir();
			if(file_exists($basedir['basedir']."/dexspm_files")){
				$upload_url = $basedir['baseurl']."/dexspm_files";
			} else {
				$upload_url = $basedir['baseurl'];
			}			
			
			if($echo){
				print($upload_url);
				return;
			} else {
				return $upload_url;			
			}
		}
		
		/*
		 *	LOAD JAVASCRIPTS
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param 	string			"top", "bottom"
		 *	@return print
		 */
		function load_send_js($type){
			global $dexsPM, $dexsPMT, $current_user;
			get_currentuserinfo();
			
			if($type == "top"){
				print("<link rel='stylesheet' type='text/css' href='".plugins_url('include/css/recipients.css', dirname(__FILE__))."'>\r\n");
				if($this->list_style){
					print("<script type='text/javascript' src='".plugins_url('include/js/prototype.js', dirname(__FILE__))."'></script>\r\n");
					print("<script type='text/javascript' src='".plugins_url('include/js/facebooklist.js', dirname(__FILE__))."'></script>\r\n");
				} else {
					print("<script type='text/javascript' src='".plugins_url('include/js/dropdown.js', dirname(__FILE__))."'></script>\r\n");
				}
			}
			
			if($type == "bottom"){
				if($this->list_style){
					$users = $dexsPM->load_users($this->cur_user);
					$un_users = "";
					
					$dexsPMT->send_pm_check();
										
					foreach ($dexsPMT->pm_rec_users AS $id => $user){
						$un_users .= '{"caption":"'.$user.'","value":"'.$id.'"}, ';
					}
					$usernames = substr($un_users, 0, -2);
										
					print("<script language='JavaScript' id='pm_autocpmplete_js'>
						document.observe('dom:loaded', function() {
						
						  tlist2 = new FacebookList('recipients', 'facebook-auto',{ newValues: false });

						  var myjson = [$usernames];
						  myjson.each(function(t){tlist2.autoFeed(t)});
						});    
					</script>");
				}
			}
		}
		
		/*
		 *	GET FRONTEND TEMPLATE
		 *	
		 *	@since 	1.0.0
		 */
		function get_pm_template(){
			global $dexsPM, $dexsPMA, $dexsPMT, $current_user;
			
			$this->load_pm_template("header");
			
			if($this->pm_folder < 5){
				if($this->pm_read){
					if(!$this->load_pm_template("message")){
						$this->load_pm_template("index");
					}
				} else {
					$this->load_pm_template("index");				
				}
			}
			
			if($this->pm_folder == 5){
				$dexsPMA->send_pm_check();
				$this->load_send_js("top");
				
				if(!$this->load_pm_template("send_form")){
					$this->load_pm_template("index");
				}
				
				$this->load_send_js("bottom");				
			}
			
			if($this->pm_folder == 6){
				if(!$this->load_pm_template("settings")){
					$this->load_pm_template("index");
				}
			}
			
			$this->load_pm_template("footer");
		}
		
		/*
		 *	LOAD FRONTEND TEMPLATE
		 *	
		 *	@since 	1.0.0
		 *	
		 *	@param 	string		TEMPLATE_FILE
		 *	@return bool
		 */
		function load_pm_template($template){
			global $dexsPM, $dexsPMA, $dexsPMT, $current_user;
						
			if(file_exists($this->template_dir."/".$template.".php")){
				require_once($this->template_dir."/".$template.".php");
				return true;
			}
			
			return false;
		}
		
		/*
		 *	LOAD BACKEND TABLES
		 *	
		 *	@since 	1.0.0
		 *
		 *	@param  string	type
		 *	@param  bool	echo
		 */		
		function load_backend_table($folder){
						
			require_once("backend.tables.php");
			
			if(isset($_GET['action'])){
				$this->action = $_GET['action'];
			}

			load_pm_table($folder);
		}
	}
?>