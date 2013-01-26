<?php
/*
Plugin Name: Dexs PM System
Plugin URI: http://www.sambrishes.net/wordpress/plugin/pm-system/
Description: Attention: <a title='This plugin is currently in the Beta Phase!'>BETA</a>, please report Bugs! A Private Messages System for your WordPress Community. Use it on the Back-End and also on the Front-End!
A Private Messages System for your WordPress Community. The access is possible from frontend and also from backend. Need Help? Go to the "Dexs PM System" Settings, under Settings, and click on the "Help" menu above the side!
Author: SamBrishesWeb
Version: 0.9.1 BETA
Author URI: http://www.sambrishes.net/wordpress
Copyright: 2012-2013, SamBrishesWeb WordPress
*/

/***************************
*	PLUGIN SETTINGS
***************************/
function dexs_pm_system_lang() {
	load_plugin_textdomain( 'dexs-pm', false, dirname(plugin_basename(__FILE__)) . '/language/' );
}
add_action('plugins_loaded', 'dexs_pm_system_lang');

define("D_URL", get_bloginfo('wpurl')."/wp-content/plugins/".dirname(plugin_basename(__FILE__)));
define("ADMIN_A_URL", "../wp-content/plugins/".dirname(plugin_basename(__FILE__)));
define("A_URL", "wp-content/plugins/".dirname(plugin_basename(__FILE__)));
define("VERSION", "0.9.0 BETA");

# *role* == Deactivate,NumOfPMS,Images,ShowBackend,ShowFrontend,Default
$default = array(
	'recipient_listing' 	=> '1',
	'email_notice' 			=> '0',
	'backend_style' 		=> '1',
	'showin_toolbar'		=> '1',
	'showin_navigation' 	=> '1',
	'backend_copyright' 	=> '1',
	'frontend_style' 		=> '0',
	'frontend_theme' 		=> 'the_system',
	'frontend_tcopy' 		=> '1',
	'frontend_copy' 		=> '1'
);
$default_permissions = array(
	'administrator' 		=> '0,-1,1,1,1,0',
	'editor' 				=> '0,-1,0,1,1,0',
	'author' 				=> '0,100,0,1,1,0',
	'contributor' 			=> '0,50,0,1,1,0',
	'subscriber' 			=> '0,10,0,1,1,0',
	'exclude_users' 		=> '',
	'include_users' 		=> ''
);


/***************************
*	CREATE PM SYSTEM
***************************/
function dexs_pm_system_install(){
	global $wpdb;
	
	$table_name = $wpdb->prefix."dexs_pms";
	
	# pm_status == user_id=>{0=>UNREAD || 1=>READ || 2=>TRASH || 3=>REMOVE || 4=>ARCHIVE};
	$sql = "CREATE TABLE IF NOT EXISTS $table_name(
		pm_id INT(255) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		pm_subject VARCHAR(120) NOT NULL,
		pm_message TEXT NOT NULL,
		pm_priority VARCHAR(1) NOT NULL DEFAULT '0',
		pm_sender_id INT(255) NOT NULL,
		pm_recipient_ids TEXT NOT NULL,
		pm_status TEXT NOT NULL,
		pm_signature VARCHAR(1) NOT NULL DEFAULT '0',
		pm_send DATETIME NOT NULL
	);";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
   
	$sender = "notice@".preg_replace("#http://www.(.*)/(.*)#", "$1", get_bloginfo("wpurl"));
	$reply_to = "notice@".preg_replace("#http://www.(.*)/(.*)#", "$1", get_bloginfo("wpurl"));
	$subject = __('New PM on', 'dexs-pm')." ".get_bloginfo("name");
$message = "<font style='font-size:14px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;'>".__('Welcome!
	
A new message found his way to your inbox on our Website (<a href=\'%HOME_URL%\'>%HOME_URL%</a>). This private message comes from <b>%SEND_NAME%</b> on %PM_DATE% at %PM_TIME% o\'clock!

<b>PM Subject:</b> %PM_SUB%
<b>PM Excerpt:</b>
%PM_EXC_120%...

<a href=\'%PM_F_LINK%\'>Go to your Inbox and answer!</a>

You can deactivate this automatically System in your PM Settings on our Website!

Yours Sincerely,
%HOME_TIL%', 'dexs-pm')."</font>
<hr style='border: 0;border-top:1px solid #888;'><font style='color:#888;font-style:italic;'>".__('This email is automatically generated. Please do not reply!', 'dexs-pm')."</font>";
				
$content = '<?php
/* MAIL INFORMATIONS */
$email_sender = "'.$sender.'";
$email_reply_to = "'.$reply_to.'";
$email_subject = "'.$subject.'";

$email_message = "'.$message.'";
?>';

	file_put_contents(ADMIN_A_URL."/mail.php", $content); 
}
register_activation_hook(__FILE__, 'dexs_pm_system_install');


/***************************
*	DELETE PM SYSTEM
***************************/
function dexs_pm_system_uninstall() {
    global $wpdb;
	
	# Delete Table
	$table_name = $wpdb->prefix."dexs_pms";
	$wpdb->query("DROP TABLE $table_name");
	
	# Delete Options
	delete_option('dexs_pm_system'); 
	delete_option('dexs_pm_permissions'); 
	
	# Delete user-generated datas (i.a. User Settings, ...)
	$metausers = get_users('meta_key=dexs_pm_settings');
    foreach ($metausers as $user) {
       delete_user_meta(  $user->ID, 'dexs_pm_settings' );
    }
	
}
#register_deactivation_hook(__FILE__, 'dexs_pm_system_uninstall');
register_uninstall_hook(__FILE__, 'dexs_pm_system_uninstall');


/***************************
	INCLUDE FUNCTIONS
***************************/
include("functions.php");


/***************************
	ADMIN MENUS
***************************/
add_shortcode('pm_system', 'pm_frontend_shortcode');

/* Administration Menu */
add_action('admin_menu', 'dexs_pm_admin');
function dexs_pm_admin(){
    global $help_config;
    $help_config = add_submenu_page( 'options-general.php', "Dexs PM System", "Dexs PM System", 'manage_options', 'pm_config', 'pm_config');

    add_action('load-'.$help_config, 'help_config_tab');
}

/* PM System Menu */
add_action('admin_menu', 'dexs_pm_backend');
function dexs_pm_backend() {
	global $current_user, $default;
	$option = get_option('dexs_pm_system', $default);
	
	if(check_permission($current_user->ID, "backend") == true){
		
		if($option['showin_navigation'] == 1){
			if($option['backend_style'] == 0 || $option['backend_style'] == 1){		# One Page
				add_menu_page(__('Private Messages', 'dexs-pm'), __('Private Messages', 'dexs-pm'), 'read', 'pm', 'user_pm', plugins_url('images/thisicon.png' , __FILE__));
			} elseif($option['backend_style'] == 2){								# Five Pages
				add_menu_page(__('Private Messages', 'dexs-pm'), __('Private Messages', 'dexs-pm'), 'read', 'pm', 'user_pm', plugins_url('images/thisicon.png' , __FILE__));
				add_submenu_page( 'pm', __('Send PM', 'dexs-pm'), __('Send PM', 'dexs-pm'), 'manage_options', 'pm_send', 'user_pm');
				add_submenu_page( 'pm', __('PM Outbox', 'dexs-pm'), __('PM Outbox', 'dexs-pm'), 'manage_options', 'pm_outbox', 'user_pm');
				add_submenu_page( 'pm', __('PM Archive', 'dexs-pm'), __('PM Archive', 'dexs-pm'), 'manage_options', 'pm_archive', 'user_pm');
				add_submenu_page( 'pm', __('PM Trash', 'dexs-pm'), __('PM Trash', 'dexs-pm'), 'manage_options', 'pm_trash', 'user_pm');
				add_submenu_page( 'pm', __('PM Settings', 'dexs-pm'), __('PM Settings', 'dexs-pm'), 'manage_options', 'pm_settings', 'user_pm');
			}
		} else {																	# Subordinate under USERS
			add_submenu_page( 'users.php', __('Private Messages', 'dexs-pm'), __('Private Messages', 'dexs-pm'), 'manage_options', 'pm', 'user_pm');
		}
		
		if($option['showin_toolbar'] == "1"){
			add_action('admin_bar_menu', 'dexs_pm_system_toolbar', 201);
			load_toolbar();
		}
	}
}


/***************************
	OUTPUT
***************************/
function pm_frontend_shortcode(){
	global $wpdb, $current_user, $default, $default_permissions;
	$style_options = get_option('dexs_pm_system', $default);
		
	include("frontend/frontend.php");
}

include('admin/pm_help.php');
function pm_config(){
    global $wpdb, $default, $default_permissions, $wp_roles;
	include_once('admin/pm_settings.php');
	
	$option = get_option('dexs_pm_system', $default);
	$option_permissions = get_option('dexs_pm_permissions', $default_permissions);
		
	$permission = ""; $mail = ""; $general = "";
	if(isset($_GET['act']) && $_GET['act'] == 'permission'){
		$permission = 'nav-tab-active';
	} elseif(isset($_GET['act']) && $_GET['act'] == 'mail'){
		$mail = 'nav-tab-active';
	} else {
		$general = 'nav-tab-active';
	}
	
	echo "<div class='wrap'>";
		echo "<div id='icon-options-general' class='icon32'></div>";
		echo "<h2 class='nav-tab-wrapper'>";
			echo "<a href='options-general.php?page=pm_config' class='nav-tab $general'>".__('General', 'dexs-pm')."</a>";
			echo "<a href='options-general.php?page=pm_config&act=permission' class='nav-tab $permission'>".__('Permissions', 'dexs-pm')."</a>";
			if($option['email_notice'] == "1"){
				echo "<a href='options-general.php?page=pm_config&act=mail' class='nav-tab $mail'>".__('eMail Notification', 'dexs-pm')."</a>";
			}
		echo "</h2>";
		echo "<br class='clear'>";
		
		if(isset($_GET['act']) && $_GET['act'] == 'permission'){
			include("admin/pm_permissions.php");
		} elseif(isset($_GET['act']) && $_GET['act'] == 'mail'){
			include("admin/pm_notification.php");
		} else {
			include("admin/pm_general.php");
		}		
	echo "</div>";
}

function user_pm(){
    global $wpdb, $current_user, $wp_roles, $default, $default_permissions;
	get_currentuserinfo();
	$table_name = $wpdb->prefix."dexs_pms";
	$option = get_option('dexs_pm_system', $default);
	
	if($_GET["page"] == "pm" && isset($_GET['table']) && $_GET['table'] == "send_pm" || $_GET["page"] == "pm_send"){
		$cur = "s";
	} elseif($_GET["page"] == "pm" && isset($_GET['table']) && $_GET['table'] == "pm_outbox" || $_GET["page"] == "pm_outbox"){
		$cur = "o";
	} elseif($_GET["page"] == "pm" && isset($_GET['table']) && $_GET['table'] == "pm_trash" || $_GET["page"] == "pm_trash"){
		$cur = "t";
	} elseif($_GET["page"] == "pm" && isset($_GET['table']) && $_GET['table'] == "pm_archive" || $_GET["page"] == "pm_archive"){
		$cur = "a";
	} elseif($_GET["page"] == "pm" && isset($_GET['table']) && $_GET['table'] == "pm_settings" || $_GET["page"] == "pm_settings"){
		$cur = "c";
	} else {
		$cur = " ";
	}
	
	echo "<div class='wrap'>";	
		echo "<div id='icon' class='icon32'><a href='admin.php?page=pm'><img src='".plugins_url('images/adminpm.png' , __FILE__)."'></a><br></div>";
		
		/* Load Header Style */
		if($option['showin_navigation'] == 1){
			if($option['backend_style'] == 0){			# Tabs Style (Like Themes)
			
				echo "<h2 class='nav-tab-wrapper'>";
					echo "<a href='admin.php?page=pm&send_pm' class='nav-tab"; if($cur == "s"){ echo " nav-tab-active"; } echo "'>".__('Send PM', 'dexs-pm')."</a>";
					echo "<a href='admin.php?page=pm' class='nav-tab"; if($cur == " "){ echo " nav-tab-active"; } echo "'>".__('Inbox', 'dexs-pm')."</a>";
					echo "<a href='admin.php?page=pm&table=pm_outbox' class='nav-tab"; if($cur == "o"){ echo " nav-tab-active"; } echo "'>".__('Outbox', 'dexs-pm')."</a>";
					echo "<a href='admin.php?page=pm&table=pm_trash' class='nav-tab"; if($cur == "t"){ echo " nav-tab-active"; } echo "'>".__('Trash', 'dexs-pm')."</a>";
					echo "<a href='admin.php?page=pm&table=pm_archive' class='nav-tab"; if($cur == "a"){ echo " nav-tab-active"; } echo "'>".__('Archive', 'dexs-pm')."</a>";
					echo "<a href='admin.php?page=pm&table=pm_settings' class='nav-tab"; if($cur == "c"){ echo " nav-tab-active"; } echo "'>".__('Settings', 'dexs-pm')."</a>";
				echo "</h2>";
				echo "<br>";	
				
			} elseif($option['backend_style'] == 1){	# Link Style (Like Posts)

				echo "<h2>".__('Private Messages', 'dexs-pm')."<a href='admin.php?page=pm&table=send_pm' class='add-new-h2'>".__('Write a new PM', 'dexs-pm')."</a></h2>";
				echo "<ul class='subsubsub'>";
					echo "<li class='inbox'><a href='admin.php?page=pm'"; if($cur == " "){ echo " class='current'"; } echo ">".__('Inbox', 'dexs-pm')." <span class='count'>(".count_pm("", "inbox").")</span></a> |</li>";
					echo "<li class='outbox'><a href='admin.php?page=pm&table=pm_outbox'"; if($cur == "o"){ echo " class='current'"; } echo ">".__('Outbox', 'dexs-pm')." <span class='count'>(".count_pm("", "outbox").")</span></a> |</li>";
					echo "<li class='archive'><a href='admin.php?page=pm&table=pm_archive'"; if($cur == "a"){ echo " class='current'"; } echo ">".__('Archive', 'dexs-pm')." <span class='count'>(".count_pm("", "archive").")</span></a> |</li>";
					echo "<li class='trash'><a href='admin.php?page=pm&table=pm_trash'"; if($cur == "t"){ echo " class='current'"; } echo ">".__('Trash', 'dexs-pm')." <span class='count'>(".count_pm("", "trash").")</span></a> |</li>";
					echo "<li class='settings'><a href='admin.php?page=pm&table=pm_settings'"; if($cur == "c"){ echo " class='current'"; } echo ">".__('Settings', 'dexs-pm')." <span class='count'>(".count_pm("", "all").")</span></a></li>";
				echo "</ul>";	
				
			} elseif($option['backend_style'] == 2){	# Menu Style (Like Nothing =D)
				if($cur == "s"){ echo "<h2>".__('Private Messages', 'dexs-pm')." &rsaquo; ".__('Send', 'dexs-pm')."</h2>"; }
				if($cur == "o"){ echo "<h2>".__('Private Messages', 'dexs-pm')." &rsaquo; ".__('Outbox', 'dexs-pm')."</h2>"; }
				if($cur == "t"){ echo "<h2>".__('Private Messages', 'dexs-pm')." &rsaquo; ".__('Trash', 'dexs-pm')."</h2>"; }
				if($cur == "a"){ echo "<h2>".__('Private Messages', 'dexs-pm')." &rsaquo; ".__('Archive', 'dexs-pm')."</h2>"; }
				if($cur == "c"){ echo "<h2>".__('Private Messages', 'dexs-pm')." &rsaquo; ".__('Settings', 'dexs-pm')."</h2>"; }
				if($cur == " "){ echo "<h2>".__('Private Messages', 'dexs-pm')." &rsaquo; ".__('Inbox', 'dexs-pm')."</h2>"; }
			}
		} else {
			if($option['backend_style'] == 0){											# Tabs Style (Like Themes)
			
				echo "<h2 class='nav-tab-wrapper'>";
					echo "<a href='admin.php?page=pm&table=send_pm' class='nav-tab"; if($cur == "s"){ echo " nav-tab-active"; } echo "'>".__('Send PM', 'dexs-pm')."</a>";
					echo "<a href='admin.php?page=pm' class='nav-tab"; if($cur == " "){ echo " nav-tab-active"; } echo "'>".__('Inbox', 'dexs-pm')."</a>";
					echo "<a href='admin.php?page=pm&table=pm_outbox' class='nav-tab"; if($cur == "o"){ echo " nav-tab-active"; } echo "'>".__('Outbox', 'dexs-pm')."</a>";
					echo "<a href='admin.php?page=pm&table=pm_trash' class='nav-tab"; if($cur == "t"){ echo " nav-tab-active"; } echo "'>".__('Trash', 'dexs-pm')."</a>";
					echo "<a href='admin.php?page=pm&table=pm_archive' class='nav-tab"; if($cur == "a"){ echo " nav-tab-active"; } echo "'>".__('Archive', 'dexs-pm')."</a>";
					echo "<a href='admin.php?page=pm&table=pm_settings' class='nav-tab"; if($cur == "c"){ echo " nav-tab-active"; } echo "'>".__('Settings', 'dexs-pm')."</a>";
				echo "</h2>";
				echo "<br>";

			} elseif($option['backend_style'] == 1 || $option['backend_style'] == 2){	# Link Style (Like Posts)

				echo "<h2>".__('Private Messages', 'dexs-pm')."<a href='admin.php?page=pm&table=send_pm' class='add-new-h2'>".__('Write a new PM', 'dexs-pm')."</a></h2>";
				echo "<ul class='subsubsub'>";
					echo "<li class='inbox'><a href='admin.php?page=pm'"; if($cur == " "){ echo " class='current'"; } echo ">".__('Inbox', 'dexs-pm')." <span class='count'>(".count_pm("", "inbox").")</span></a> |</li>";
					echo "<li class='outbox'><a href='admin.php?page=pm&table=pm_outbox'"; if($cur == "o"){ echo " class='current'"; } echo ">".__('Outbox', 'dexs-pm')." <span class='count'>(".count_pm("", "outbox").")</span></a> |</li>";
					echo "<li class='archive'><a href='admin.php?page=pm&table=pm_archive'"; if($cur == "a"){ echo " class='current'"; } echo ">".__('Archive', 'dexs-pm')." <span class='count'>(".count_pm("", "archive").")</span></a> |</li>";
					echo "<li class='trash'><a href='admin.php?page=pm&table=pm_trash'"; if($cur == "t"){ echo " class='current'"; } echo ">".__('Trash', 'dexs-pm')." <span class='count'>(".count_pm("", "trash").")</span></a> |</li>";
					echo "<li class='settings'><a href='admin.php?page=pm&table=pm_settings'"; if($cur == "c"){ echo " class='current'"; } echo ">".__('Settings', 'dexs-pm')." <span class='count'>(".count_pm("", "all").")</span></a></li>";
				echo "</ul>";
			
			}
		}
		
		echo "<br class='clear'>";
		
		/* Load Body Content */
		echo '<link type="text/css" rel="stylesheet" href="'.ADMIN_A_URL.'/include/admin_form.css">';
		include("include/pm_tables.php");
		include("include/pm_system.php");
		
		if(isset($_GET['table']) && $_GET['table'] == "send_pm" || !isset($_GET['table']) && $_GET['page'] == "send_pm"){
			if(check_permission($current_user->id, "pm") == false){
				$errors['goto_max'] = __('You have reached your maximum number of messages. Please delete some!', 'dexs-pm');
			}
		}
		
		if(isset($errors)){
			echo '<div id="settings-error-settings_updated" class="error settings-error">';
				foreach($errors AS $e){
				echo "<p><b>".$e."</b></p>";
				}
			echo '</div>';
		}
		if($_GET['send_status'] == "true"){
			echo '<div id="settings-error-settings_updated" class="updated settings-error">';
				echo "<p><b>".__('PM successfully sent!', 'dexs-pm')."</b></p>";
			echo '</div>';
		}
		if(isset($_GET['success']) && $_GET['success'] != "save"){ $number = $_GET['success'];
			echo '<div id="settings-error-settings_updated" class="updated settings-error">';
				if($number == "1"){
					echo "<p><b>".__('1 Action was carried out successfully!', 'dexs-pm')."</b></p>";
				} else {
					echo "<p><b>".$number." ".__('Actions was carried out successfully!', 'dexs-pm')."</b></p>";				
				}
			echo '</div>';
		}
		if(isset($_GET['success']) && $_GET['success'] == "save"){
			echo '<div id="settings-error-settings_updated" class="updated settings-error">';
				echo "<p><b>".__('Your personal preferences have been saved!', 'dexs-pm')."</b></p>";
			echo '</div>';
		}
		
		if(!isset($get_reci)){ $get_reci = ""; }
		if(!isset($get_subject)){ $get_subject = ""; }
		if(!isset($get_message)){ $get_message = ""; }
		
		if($cur == "s"){ pm_send_table($get_reci, $get_subject, $get_message); }
		if($cur == "o"){ get_pm_table("outbox"); }
		if($cur == "t"){ get_pm_table("trash"); }
		if($cur == "a"){ get_pm_table("archive"); }
		if($cur == "c"){ pm_settings_table(); }
		if($cur == " "){ get_pm_table("inbox"); }
			
	echo "</div>";
}
?>