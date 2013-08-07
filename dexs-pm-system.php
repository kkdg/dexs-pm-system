<?php
/*
 Plugin Name: Dexs PM System
 Plugin URI: http://wordpress.org/plugins/dexs-pm-system/
 Description: A private messages system for your WordPress Community. Finally a new update, after about 6 months. Sorry and thanks for 2 x 5 stars on WordPress.org! <3 ^.^ (Codename: PreHexley)
 Author: SamBrishes (PYTES.NET)
 Author URI: http://www.pytes.net
 Version: 1.0.1
 Version CodeName: PreHexley
 Copyright: 2012-2013, SamBrishes (PYTES.NET)
 */

	define("DPM_S_VER", "1.0.1");
	define("DPM_FOLDER", plugin_dir_path(__FILE__));
	
	/*
	 *	INCLUDE THE CLASSES, THE FUNCTIONS AND THE LANGUAGES
	 */
	function dexs_pms_language(){
		load_plugin_textdomain('dexs-pm', false, dirname(plugin_basename(__FILE__)) . '/include/lang/');
	}
	add_action('plugins_loaded', 'dexs_pms_language');
	
	include("include/class.dexs_pmsystem.php");			# PM SETTINGS CLASS / FUNCTIONS
	include("include/class.dexs_actions.php");			# PM SYSTEM CLASS / FUNCTIONS
	include("include/class.dexs_template.php");			# PM TEMPLATE CLASS / FUNCTIONS
		
	$dexsPM = new dexsPMSystem();
	$dexsPMA = new dexsPMActions();
	$dexsPMT = new dexsPMTemplate();
	
	include("include/admin.widget.php");				# PM WIDGET
	include("include/upgrade.batch10s.php");			# UPGRADE && DEPRECATED FUNCTIONS

	
	/*
	 * 	INSTALL PM SYSTEM
	 *	
	 *	@update 1.0.1 	NEW INSTALLATION CHECK FORM
	 */
	function dexs_pms_install(){
		global $wpdb;
		
		if(get_option("dexs_pm_system") !== false){
			/* UPDATE FORM */
			
			if(get_option("dexs_pm_system") == "1.0.0 RC.1"){
				/* UPDATE FOR THE NEW VERSION 1.0.0 RC.1 */
				if($options = get_option('dexs_pm_settings')){
					if(isset($options["use_archive"])){
						$options["use_archive"] = "1";
					}
					if(isset($options["use_trash"])){
						$options["use_trash"] = "1";
					}
					if(isset($options["use_attachments"])){
						$options["use_attachments"] = "1";
					}
					if(isset($options["attachment_type"])){
						$options["attachment_type"] = "0";
					}
					if(isset($options["use_backend"])){
						$options["use_backend"] = "1";
					}
					if(isset($options["use_frontend"])){
						$options["use_frontend"] = "1";
					}
				}
			} else {
				/* UPDATE FOR THE OLD BETA VERSION 0.9.1 */
				/* 
				 *	NOTE: 	Version 1.0.0 contains a complete new DB table
				 *			The data/messages from the old table will be automatically converted and inserted into the new table.
				 *			You just have to go into the “Dexs PM Settings” and click the blue “Update” button.
				 */
				$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."dexs_pmsystem(
					pm_id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					pm_subject VARCHAR(150) NOT NULL,
					pm_message TEXT NOT NULL,
					pm_sender BIGINT(20) NOT NULL,
					pm_recipients TEXT NOT NULL,				
					pm_meta TEXT NOT NULL,
					pm_send TIMESTAMP NOT NULL				
				);";
				
				/*
				 *	NOTE:	You need to reconfigure the Dexs PM System.
				 */
				delete_option('dexs_pm_settings');
				delete_option('dexs_pm_permissions');
				delete_option('dexs_pm_email');

			   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			   dbDelta($sql);
			}
			update_option("dexs_pm_system", DPM_S_VER);
		} else {
			/* INSTALLATION FORM */
			
			$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."dexs_pmsystem(
				pm_id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				pm_subject VARCHAR(150) NOT NULL,
				pm_message TEXT NOT NULL,
				pm_sender BIGINT(20) NOT NULL,
				pm_recipients TEXT NOT NULL,				
				pm_meta TEXT NOT NULL,
				pm_send TIMESTAMP NOT NULL				
			);";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			add_option("dexs_pm_system", DPM_S_VER, "", "no");
		}
	}
	register_activation_hook(__FILE__, 'dexs_pms_install');
	
	
	/*
	 * 	DEINSTALL PM SYSTEM
	 */
	function dexs_pms_uninstall(){
		 global $wpdb;
		
		# DELETE THE PM SYSTEM
		$wpdb->query("DROP TABLE ".$wpdb->prefix."dexs_pmsystem");
		delete_option('dexs_pm_system');
		delete_option('dexs_pm_settings');
		delete_option('dexs_pm_permissions');
		delete_option('dexs_pm_email');
		delete_option('dexs_pm_frontend');
		
		# DELETE USER SETTINGS
		$metausers = get_users('meta_key=dexs_pm_settings');
		foreach ($metausers as $user) {
		   delete_user_meta($user->ID, 'dexs_pm_settings');
		}
	}
	#register_deactivation_hook(__FILE__, 'dexs_pms_uninstall');			# DEVELOPER-MODE
	register_uninstall_hook(__FILE__, 'dexs_pms_uninstall');			# USER MODE
	
	
	/*
	 *	UPGRADE CHECK
	 */	
	function dexs_pms_upgrade_me(){
		$error = dexs_pm_deprecated();
		
		if(isset($error) && is_wp_error($error)){
			$errormes = $error->get_error_messages();
			echo "<div id='message' class='error'><p>";
				echo "<b>".__("Dexs PM System - Error:", "dexs-pm")."</b> ".$errormes[0];
			echo "</p></div>";
		}
	}
	add_action('shutdown', 'dexs_pms_upgrade_me');
	
	
	/*
	 *	POST FORMS
	 */
	function dexs_pms_action_batch(){
		global $current_user, $dexsPM, $dexsPMA, $WP_Error;
		get_currentuserinfo();
		
		$error = dexs_pm_deprecated();
		if(isset($error) && is_wp_error($error)){
			define("DPM_UPGRADE", TRUE);
		}
		
		if(defined("DPM_UPGRADE")){
			if(isset($_POST['upgrade']) && isset($_POST['upgrade_code']) && $_POST['upgrade_code'] == "314159265358979"){
				if(isset($_POST['convert_data'])){
					if(dexs_pm_convert_table()){
						if(delete_old_pm_table()){
							header("Location: options-general.php?page=pm_config");
						}
					}
				} else {
					if(delete_old_pm_table()){
						header("Location: options-general.php?page=pm_config");
					}
				}
			}
		} else {
			if((isset($_POST['dexs_pm']) && (isset($_POST['action']) || isset($_POST['send_action']))) || (isset($_GET['dpm']) && isset($_GET['action']))){
				$dpm = ""; $action = ""; $pm_id = ""; $s_action = 99;
				
				/* DELTE MEDIA CAP */
				if(isset($_POST['delete'])){
					$role = array_keys($_POST['delete']); $role = $role[0];
					if($role = get_role($role)){
						$role->remove_cap("upload_files");
					}
					header("Location: options-general.php?page=pm_config&tab=".$_POST['tab']."&success=1");
				}			
				
				/* LOCATION */
				if(isset($_POST['dexs_pm'])){
					$dpm = $_POST['dexs_pm'];
				} elseif(isset($_GET['dpm'])){
					$dpm = $_GET['dpm'];
				}
				if (!is_admin()){
					$load_page = "?page_id=".$dexsPM->get_frontend_id();
				} else {
					$load_page = "?page=pm";
				}
				
				/* ACTION */
				if(isset($_POST['action'])){
					$action = $_POST['action'];
				} elseif(isset($_POST['send_action'])){
					$action = 99;
					if(is_array($_POST['send_action'])){
						$s_action = array_keys($_POST['send_action']);
						$s_action = $s_action[0];
					} else {
						$s_action = $_POST['send_action'];
					}
				} elseif(isset($_GET['action'])){
					$action = $_GET['action'];
				}
				
				/* PM ID */
				if(isset($_POST['pm_id'])){
					$pm_id = $_POST['pm_id'];
				} elseif(isset($_GET['pmid'])){
					$pm_id = $_GET['pmid'];
					$_POST['pm_id'] = $_GET['pmid'];
				}

				if(is_array($action)){
					$actme = array_values($action);
					
					if(count($action) == 2){
						unset($action[array_search("-1", $action)]);
						$action = implode("", $action);
					} else if(count($action) == 1 && is_string($actme[0])){
						$action = implode("", array_keys($action));
					}
				}
				
				/* SETTINGS -> DEXS PM SYSTEM */
				if($dpm == "settings" && $action == "6"){
					if(isset($_POST['save'])){
						$success_url = "options-general.php?page=pm_config&tab=".$_POST['tab']."&success=1";
					
						if(!$dexsPM->set_pm_settings($_POST['tab'], $_POST)){
							$GLOBALS['pm_error'] = __("An unknown Error occurred! The settings couldn't be saved.", "dexs-pm");
						}
					} else if(isset($_POST['reset'])){
						$success_url = "options-general.php?page=pm_config&tab=".$_POST['tab']."&success=2";
					
						if(!$dexsPM->set_to_default($_POST['tab'])){
							$GLOBALS['pm_error'] = __("An unknown Error occurred! The settings couldn't be reset.", "dexs-pm");
						}
					} else if(isset($_POST['send_test_mail'])){
						$success_url = "options-general.php?page=pm_config&tab=".$_POST['tab']."&success=3";
						
						if(!$dexsPM->set_pm_settings($_POST['tab'], $_POST)){
							$GLOBALS['pm_error'] = __("An unknown Error occurred! The settings couldn't be saved.", "dexs-pm");
						} else {
							if(!$dexsPM->send_email_note(NULL, $_POST['test_mail_recipient'])){
								if(!isset($GLOBALS['pm_error'])){
									$GLOBALS['pm_error'] = __("An unknown Error occurred! The test eMail couldn't be sent.", "dexs-pm");
								}
							}
						}
					}
				}
				
				/* BULK ACTIONS */
				if(($dpm == "folder" || $dpm == "read_pm") && $action < 6){
					if(isset($_GET['folder'])){
						$folder = $_GET['folder'];
						if($folder == 7){
							$folder = 0;
						}
					} else {
						$folder = 0;
					}
					$success_url = $GLOBALS['pagenow'].$load_page."&folder=$folder&success=".$action;
					if(!$dexsPMA->pm_action($action, $pm_id, $current_user->ID)){
						$GLOBALS['pm_error'] = __("An unknown Error occurred!", "dexs-pm");
					}
				}
				
				/* USER SETTINGS */
				if($dpm == "user_settings" && $action == 7){
					$success_url = $GLOBALS['pagenow'].$load_page."&folder=6&success=1";
					
					if(isset($_POST['save'])){
						if(!$dexsPM->user_settings("update", $_POST)){
							$GLOBALS['pm_error'] = __("An unknown Error occurred! The settings couldn't be saved.", "dexs-pm");					
						}
					} else {
						if(!$dexsPM->user_settings("reset", $_POST)){
							$GLOBALS['pm_error'] = __("An unknown Error occurred! The settings couldn't be reset.", "dexs-pm");				
						}
					}
				}
				
				/* SEND PM */
				if($dpm == "send_pm" && ($s_action == 0 || $s_action == 8)){
					if($dexsPMA->send_pm_action($_POST, $current_user->ID)){
						$success_url = $GLOBALS['pagenow'].$load_page."&folder=1&send=1";
					}
				}
				
				/* ANSWER || FORWARD */
				if($dpm == "read_pm" && ($s_action == 1 || $s_action == 2)){
					header("Location: ".$GLOBALS['pagenow'].$load_page."&folder=5&send_action=".$s_action."&pmid=".$_GET['pmid']);				
				}
				
				/* HEADER */
				if(!isset($success_url)  && !isset($GLOBALS['pm_error'])){
					$GLOBALS['pm_error'] = __("An unknown Error occurred! The transferred datas are faulty.", "dexs-pm");
					
					if(empty($pm_id)){
						$GLOBALS['pm_error'] = __("An unknown Error occurred! You have no message selected.", "dexs-pm");
					}
				}	
				if(!isset($GLOBALS['pm_error'])){
					header("Location: $success_url");
				}
			}
		}
	}
	add_action('plugins_loaded', 'dexs_pms_action_batch');
	

	/*
	 *	ADD SETTINGS MENU ENTRY
	 */
	function dexs_pms_admin(){
		add_submenu_page('options-general.php', "Dexs PM System", "Dexs PM System", 'manage_options', 'pm_config', 'dexs_pms_admin_config');
	}
	add_action('admin_menu', 'dexs_pms_admin');
	
	
	/*
	 *	ADD SETTINGS MENU OUTPUT
	 */
	function dexs_pms_admin_config(){
		global $wpdb, $wp_roles, $dexsPM;
		if(defined("DPM_UPGRADE") && DPM_UPGRADE){
			dexs_pm_upgrade_table();
		} else {
			include("dexs-pm-admin.php");
		}
	}
	
	
	/*
	 *	ADD BACKEND INTERFACE MENU
	 */
	function dexs_pms_backend(){
		global $current_user, $dexsPM;
		get_currentuserinfo();
		
		if(!defined("DPM_UPGRADE")){
			$option = $dexsPM->load_pm_settings("settings");
			
			if($dexsPM->check_permissions("backend", $current_user->ID)){
				
				if($option['backend_navi']){
					if($option['backend_style'] == 0 || $option['backend_style'] == 1){
						/* ONE PAGE STYLE */
						add_menu_page(__('Private Messages', 'dexs-pm'), __('Private Messages', 'dexs-pm'), 'read', 'pm', 'user_pm', plugins_url('images/thisicon.png' , __FILE__));
					} elseif($option['backend_style'] == 2){
						/* FIVE PAGES STYLE */
						add_menu_page(__('Private Messages', 'dexs-pm'), __('Private Messages', 'dexs-pm'), 'read', 'pm', 'user_pm', plugins_url('images/thisicon.png' , __FILE__));
						add_submenu_page( 'pm', __('Send PM', 'dexs-pm'), __('Send PM', 'dexs-pm'), 'read', 'pm&folder=5', 'user_pm');
						add_submenu_page( 'pm', __('PM Outbox', 'dexs-pm'), __('PM Outbox', 'dexs-pm'), 'read', 'pm&folder=1', 'user_pm');
						add_submenu_page( 'pm', __('PM Trash', 'dexs-pm'), __('PM Trash', 'dexs-pm'), 'read', 'pm&folder=2', 'user_pm');
						add_submenu_page( 'pm', __('PM Archive', 'dexs-pm'), __('PM Archive', 'dexs-pm'), 'read', 'pm&folder=4', 'user_pm');
						add_submenu_page( 'pm', __('PM Settings', 'dexs-pm'), __('PM Settings', 'dexs-pm'), 'read', 'pm&folder=6', 'user_pm');
					}
				} else {
					/* SUPORDINATE UNDER THE USERS MENU */
					add_submenu_page( 'users.php', __('Private Messages', 'dexs-pm'), __('Private Messages', 'dexs-pm'), 'read', 'pm', 'user_pm');
				}
				
				if($option['backend_toolbar']){
					add_action('admin_bar_menu', 'dexs_pms_toolbar', 201);
					
					/* TOOLBAR */
					function dexs_pms_toolbar($wp_admin_bar){
						global $wpdb, $wp_admin_bar, $dexsPM, $dexsPMA;
						
						$count = $dexsPMA->count_messages(6);
						
						if($count["new"] == 0){
							$act = "";
							$title = __('Inbox', 'dexs-pm');
						} else {
							$act = "active";
							$title = $count["new"]." ".__('new Messages', 'dexs-pm');
							if($count["new"] == 1){
								$title = __('1 new Message', 'dexs-pm');						
							}
						}
						
						$href = ($dexsPM->load_pm_settings("settings", "backend_navi"))? "admin.php?page=pm" : "users.php?page=pm";
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
							#wp-admin-bar-pm_system #wp-admin-bar-pm_new_message,
							#wp-admin-bar-pm_system #wp-admin-bar-pm_write_new_message{
								text-align:center;						
							}				
							#wp-admin-bar-pm_system #wp-admin-bar-pm_new_message{
								font-weight:bold;
							}
							#wp-admin-bar-pm_system #wp-admin-bar-pm_write_new_message{
								background-color: #eaf2fa;
							}
							#wp-admin-bar-pm_system #wp-admin-bar-pm_write_new_message:hover{
								background-color: #eaf2fa;
							}
						-->
						</style>
						<?php					
						$args['main'] = array(
							'id' => 'pm_system',
							'parent' => 'top-secondary',
							'title' => "<span class='ab-icon $act'></span> <span class='$act'>".$count["new"]."</span>",
							'href' => $href,
							'class' => 'test',
							'meta' => array('class' => 'dexs_pm_system')
						);
						
						$args['write'] = array(
							'id' => 'pm_write_new_message',
							'parent' => 'pm_system',
							'title' => __('Write a new Message', 'dexs-pm'),
							'href' => $href."&folder=5",
							'meta' => array('class' => 'dexs_pm_system')
						);
						
						$args['new'] = array(
							'id' => 'pm_new_message',
							'parent' => 'pm_system',
							'title' => $title,
							'href' => $href."&folder=0",
							'meta' => array('class' => 'dexs_pm_system')
						);
						
						$args['outbox'] = array(
							'id' => 'pm_message_outbox',
							'parent' => 'pm_system',
							'title' => "<div style='color: rgb(33, 117, 155);text-shadow: none;display:inline-block;width:85%;'>".__('Folder: Outbox', 'dexs-pm')."</div>
										<div class='dexs-admin-bar-count' style='color: rgb(33, 117, 155);text-shadow: none;display:inline;text-align:right;'>(".$count["outbox"].")</div>",
							'href' => $href."&folder=1",
							'meta' => array('class' => 'dexs_pm_system')
						);
						
						$args['archive'] = array(
							'id' => 'pm_message_archive',
							'parent' => 'pm_system',
							'title' => "<div style='color: rgb(33, 117, 155);text-shadow: none;display:inline-block;width:85%;'>".__('Folder: Archive', 'dexs-pm')."</div>
										<div class='dexs-admin-bar-count' style='color: rgb(33, 117, 155);text-shadow: none;display:inline;text-align:right;'>(".$count["archive"].")</div>",
							'href' => $href."&folder=4",
							'meta' => array('class' => 'dexs_pm_system')
						);
						
						$args['trash'] = array(
							'id' => 'pm_message_trash',
							'parent' => 'pm_system',
							'title' => "<div style='color: rgb(33, 117, 155);text-shadow: none;display:inline-block;width:85%;'>".__('Folder: Trash', 'dexs-pm')."</div>
										<div class='dexs-admin-bar-count' style='color: rgb(33, 117, 155);text-shadow: none;display:inline;text-align:right;'>(".$count["trash"].")</div>",
							'href' => $href."&folder=2",
							'meta' => array('class' => 'dexs_pm_system')
						);
						
						$wp_admin_bar->add_node($args['main']);
						$wp_admin_bar->add_node($args['new']);
						$wp_admin_bar->add_node($args['write']);
						$wp_admin_bar->add_node($args['outbox']);
						$wp_admin_bar->add_node($args['archive']);
						$wp_admin_bar->add_node($args['trash']);
					}
				}
			}
		}
	}
	add_action('admin_menu', 'dexs_pms_backend');
	
	
	/*
	 *	ADD BACKEND INTERFACE MENU OUTPUT
	 */		
	function user_pm(){
		global $wpdb, $current_user, $wp_roles, $dexsPM, $dexsPMA, $dexsPMT;
		get_currentuserinfo();
		
		if(!defined("DPM_UPGRADE")){		
			if($dexsPM->check_permissions("images")){
				$userdata = get_userdata($current_user->ID);
				
				if(!current_user_can('upload_files')){
					$role = $userdata->roles[0];
					$add_role_cap = get_role($role);
					$add_role_cap->add_cap('upload_files');
				}
			}
			
			include("dexs-pm-backend.php");
		}
	}
	

	/*
	 *	ADD FRONTEND INTERFACE
	 */	
	add_shortcode('pm_system', 'dexs_pms_frontend');
	function dexs_pms_frontend(){
		global $dexsPM, $dexsPMT, $current_user;
		get_currentuserinfo();
		
		if(!defined("DPM_UPGRADE")){
			if(is_user_logged_in()){
				if($dexsPM->check_permissions("images")){
					$userdata = get_userdata($current_user->ID);
					
					if(!current_user_can('upload_files')){
						$role = $userdata->roles[0];
						$add_role_cap = get_role($role);
						$add_role_cap->add_cap('upload_files');
					}
				}
				$dexsPMT->get_pm_template();
			} else {			
				$args = array(
					'echo' => true,
					'redirect' => site_url( $_SERVER['REQUEST_URI'] ), 
					'form_id' => 'loginform',
					'label_username' => __( 'Username' ),
					'label_password' => __( 'Password' ),
					'label_remember' => __( 'Remember Me' ),
					'label_log_in' => __( 'Log In' ),
					'id_username' => 'user_login',
					'id_password' => 'user_pass',
					'id_remember' => 'rememberme',
					'id_submit' => 'wp-submit',
					'remember' => true,
					'value_username' => NULL,
					'value_remember' => false 
				);
				
				echo "<center>".__("You must be logged in to use this function!", "dexs-pm")."<br><br>";
				wp_login_form($args);
				echo "</center>";
			}		
		}		
	}
	
	/*
	 *	DEXS PM SYSTEM
	 *
	 *	@VERSION	1.0.1 (PreHexley)
	 *	@VERSION	StableVersion 1
	 *	@AUTHOR		SamBrishes (PYTES.NET)
	 *
	 *	@SUPPORT	Twitter: 	@PytesDev
	 *	@SUPPORT	eMail:		sambrishes@gmx.net
	 *	@SUPPORT	WordPress Plugin Page
	 */
?>