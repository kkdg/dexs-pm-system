<?php
function help_config_tab(){
    global $help_config;
    $screen = get_current_screen();

	if ($screen->id != $help_config)
		return;

	$help = "<p><b>".__('So you can use our pm system also on the frontend!', 'dexs-pm')."</b></p>";
	$help .= "<ol>";
		$help .= "<li style='list-style-type:decimal;margin:0 auto;'>".__('Create a new Page or, if you want or need it, create a new Post!', 'dexs-pm')."</li>";
		$help .= "<li style='list-style-type:decimal;margin:0 auto;'>".__('Enter any title, for example: Private Messages!', 'dexs-pm')."</li>";
		$help .= "<li style='list-style-type:decimal;margin:0 auto;'>".__('Write no Text, but only the shortcode <code>[pm_system]</code>! <small>(Together with the symbols "[" and "]"!)</small>.', 'dexs-pm')."</li>";
		$help .= "<li style='list-style-type:decimal;margin:0 auto;'>".__('Configure the access to the frontend, unter "Settings" > "Dexs PM System".', 'dexs-pm')."</li>";
	$help .= "</ol>";
		
	$screen->add_help_tab( array(
		'id'		=>	'help_pm_system',
		'title'		=>	__('Dexs PM System', 'dexs-pm'),
		'content'	=> $help,
	));
		
	$help = "<p>".__('<b>Autocomplete Input Field:</b> The Autocomplete Input field Style will integrate the "Prototype Javascript Framework" in the version: 1.6.0.2! If you have script problems with this framework, please use the Drop Down Input Field Style, this style will only include one self-written Javascript file and works completely without framework!', 'dexs-pm')."</p>";
	$help .= "<p>".__('<b>eMail Notification:</b> A user, who receives a private message, can be informed by email about that!', 'dexs-pm')."</p>";
	$help .= "<p>".__('<b>Five Pages - Menu Style:</b> This backend style can only be used, if the message menu has his own entry and is not subordinate under "Users"!', 'dexs-pm')."</p>";
	$help .= "<p>".__('<b>Fullwide Page</b> This option is only useful, if your design has a "fullwide page" support! (A Page withour sidebar!). If you activate this option, will be one more column and also more options visible on the front view of our pm system.', 'dexs-pm')."</p>";
		
	$screen->add_help_tab( array(
		'id'		=>	'help_settings',
		'title'		=>	__('General Settings', 'dexs-pm'),
		'content'	=> $help,
	));
		
	$help = "<p>".__('<b>Permissions:</b> You can configure the permissions for the 5 given roles. Custom roles, about a plugin, will currently no support!', 'dexs-pm')."</p>";
	$help .= "<p>".__('<b>Exclude/Include Users:</b> You can exclude and include Users, regardless of their role.', 'dexs-pm')."</p>";
		
	$screen->add_help_tab( array(
		'id'		=>	'help_permission',
		'title'		=>	__('Permissions Settings', 'dexs-pm'),
		'content'	=> $help,
	));
		
	$help = "<p>".__('<b>General:</b> The information cannot be stored in the database, because of the character limitation of the wordpress options fields. Therefore will be all informations about the email notification stored in a file, called <code>mail.php</code>, in the Plugin directory!', 'dexs-pm')."</p>";
	$help .= "<p>".__('<b>Sender Address:</b> The sender email address can be a not exist address! Like: example@your-website.net', 'dexs-pm')."</p>";
	$help .= "<p>".__('<b>Reply-To Address:</b> The Reply-To address must be only set, if you want to receive answers from the Recipients of the email notification! You can also leave this field blank and set an exist email address in the "sender address" field, when you want to receive answers from the Recipients!', 'dexs-pm')."</p>";
	$help .= "<p>".__('<b>Send Test Mail:</b> Remember to configure and activate "Mercury", if you want to test the email notification on XAMPP!', 'dexs-pm')."</p>";
		
	$screen->add_help_tab( array(
		'id'		=>	'help_notification',
		'title'		=>	__('eMail Notification', 'dexs-pm'),
		'content'	=> $help,
	));

	$screen->set_help_sidebar(
		'<p><strong>'.__('For more information:').'</strong></p>' .
		'<p>'.__('<a href="http://wordpress.org/extend/plugins/dexs-pm-system/" target="_blank">Plugin & Support</a>', 'dexs-pm').'</p>' .
		'<p>'.__('<a href="http://sambrishes.net/wordpress/en/dexs-pm-system/" target="_blank">The Plugin Webside</a>', 'dexs-pm').'</p>'
	);
}
?>