<?php

	/* GET THEMES */
	function get_pm_themes($current = ""){
		$dir = ADMIN_A_URL.'/frontend/themes/';
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
		
		$i = 0; foreach($themes as $theme){
			if(file_exists(ADMIN_A_URL."/frontend/themes/".$theme."/theme.ini")){ $i++;
				$load_theme = parse_ini_file(ADMIN_A_URL."/frontend/themes/".$theme."/theme.ini");
				echo "<option value='".$load_theme['id']."'"; if($current == $load_theme['id']){ echo " selected='selected'"; } echo ">".$load_theme['name']." (".$load_theme['version'].")</option>";
			}
		}
			
		if($i == 0){
			echo "<option value='-1'>No Theme installed!</option>";
		}
	}
	
	/* SAVE OPTIONS */	
	if(isset($_POST['save']) && isset($_POST['type']) || isset($_POST['reset']) && isset($_POST['type'])){
		$action = "";
		if($_POST['type'] == "general"){
			if(isset($_POST['save'])){
				$value['recipient_listing'] = $_POST['recipient_listing'];
				$value['email_notice'] = $_POST['email_notice'];
				$value['backend_style'] = $_POST['backend_style'];
				$value['showin_toolbar'] = $_POST['showin_toolbar'];
				$value['showin_navigation'] = $_POST['showin_navigation'];
				$value['backend_copyright'] = $_POST['backend_copyright'];
				$value['frontend_style'] = $_POST['frontend_style'];
				$value['frontend_theme'] = $_POST['frontend_theme'];
				$value['frontend_tcopy'] = $_POST['frontend_tcopy'];
				$value['frontend_copy'] = $_POST['frontend_copy'];
			}
			
			if(isset($_POST['reset'])){
				$value['recipient_listing'] = '1';
				$value['email_notice'] = '0';
				$value['backend_style'] = '1';
				$value['showin_toolbar'] = '1';
				$value['showin_navigation'] = '1';
				$value['backend_copyright'] = '1';
				$value['frontend_style'] = '0';
				$value['frontend_theme'] = 'the_system';
				$value['frontend_tcopy'] = '1';
				$value['frontend_copy'] = '1';
			}
			
			if (get_option('dexs_pm_system') != $value) {
				update_option('dexs_pm_system', $value);
			} else {
				add_option('dexs_pm_system', $value, '', 'no');	
			}
			
		}
		
		if($_POST['type'] == "permissions"){
			$action = "&act=permission";
			if(isset($_POST['save'])){
				foreach ( $wp_roles->role_names as $role => $name ){
					$deactivate = ""; $attachment = ""; $backend =""; $frontend = ""; $emailnote = "";
					
					if(array_key_exists("deactivate_$role", $_POST)){
						$deactivate = $_POST["deactivate_$role"];
					} else {
						$deactivate = 0;
					}
					if(array_key_exists("attachment_$role", $_POST)){
						$attachment = $_POST["attachment_$role"];
					} else {
						$attachment = 0;
					}
					if(array_key_exists("backend_$role", $_POST)){
						$backend = $_POST["backend_$role"];
					} else {
						$backend = 0;
					}
					if(array_key_exists("frontend_$role", $_POST)){
						$frontend = $_POST["frontend_$role"];
					} else {
						$frontend = 0;
					}
					if(array_key_exists("emailnote_$role", $_POST)){
						$emailnote = $_POST["emailnote_$role"];
					} else {
						$emailnote = 0;
					}
					$value[$role] .= $deactivate.",".$_POST["number_$role"].",".$attachment.",".$backend.",".$frontend.",".$emailnote;
				}
				$value['exclude_users'] = $_POST["exclude_users"];
				$value['include_users'] = $_POST["include_users"];
			}		
			
			if(isset($_POST['reset']) && $_POST['type'] == "permissions"){
				$value['administrator'] = '0,-1,1,1,1,0';
				$value['editor'] = '0,-1,0,1,1,0';
				$value['author'] = '0,100,0,1,1,0';
				$value['contributor'] = '0,50,0,1,1,0';
				$value['subscriber'] = '0,10,0,1,1,0';
				$value['exclude_users'] = '';
				$value['include_users'] = '';
			}	
				
			if (get_option('dexs_pm_permissions') != $value) {
				update_option('dexs_pm_permissions', $value);
			} else {
				add_option('dexs_pm_permissions', $value, '', 'no');	
			}
		}

		echo "<script type='text/javascript'>
			window.location.href='options-general.php?page=pm_config".$action."&status=updated'
		</script>";
	}
	
	/* SEND TEST MAIL */
	if(isset($_POST['test_mail']) && isset($_POST['test_mail_recipient']) && $_POST['test_mail_recipient'] != ""){
		$mail = get_pm_email();
$test_message = "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.";
		$search = array("%HOME_TIL%", "%HOME_URL%", "%ADM_MAIL%", "%RECI_NAME%", "%SEND_NAME%", "%PM_SUB%", "%PM_TXT%", "%PM_DATE%", "%PM_TIME%", "%PM_F_LINK%", "%PM_B_LINK%");
		$replace = array(
			get_bloginfo("name"),
			get_bloginfo("url"),
			get_bloginfo("admin_email"),
			"Name of Recipient",
			"Name of Sender",
			"The Test Subject",
			$test_message,
			date(get_option('date_format')),
			date(get_option('time_format')),
			get_bloginfo("url"),
			get_bloginfo("url")
		);
		
		$the_message = str_replace($search, $replace, stripslashes(nl2br($mail['email_message'])));
		
		$recipient = $_POST['test_mail_recipient'];
		$subject = str_replace($search, $replace, $mail['email_subject']);
		
		if(preg_match("/\%PM_EXC_(.*)\%/", $the_message, $length)){
		
			$message_size = preg_split("#\%PM_EXC_(.*)\%#Uis", $the_message);
			$excerpt = substr($test_message, 0, $length[1]);
			
			$message = "<html><body>".$message_size[0].$excerpt.$message_size[1]."</body></html>";
			
		} else {
			$message = "<html><body>".$the_message."</body></html>";
		}	
		
		$header .= 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
		$header .= 'To: '.$_POST['test_mail_recipient']."\r\n";
		$header .= 'From: '.$mail['email_sender']."\r\n";		
		$header .= 'Reply-To: '.$mail['email_reply_to']."\r\n";		
		$header .= 'X-Mailer: PHP/'.phpversion();

		mail($recipient, $subject, $message, $header);	
	}
	
	/* SAVE MAIL DATA */
	if(isset($_POST['save_mail']) || isset($_POST['reset_mail'])){
		$action = "&act=mail";
		
		if(isset($_POST['save_mail'])){
			if($_POST['email_sender'] != ""){
				$sender = $_POST['email_sender'];
			} else {
				$error['email_sender_blank'] = __('You must enter a eMail address!', 'dexs-pm');
			}
			
			if($_POST['email_reply_to'] != ""){
				$reply_to = $_POST['email_reply_to'];
			} else {
				$reply_to = $_POST['email_sender'];
			}
			
			if($_POST['email_subject'] != ""){
				$subject = $_POST['email_subject'];
			} else {
				$error['email_subject_blank'] = __('You must enter a eMail subject!', 'dexs-pm');
			}
			
			if($_POST['email_message'] != ""){
				$message = $_POST['email_message'];
			} else {
				$error['email_message_blank'] = __('You must enter a eMail Message!', 'dexs-pm');
			}
		}
		
		if(isset($_POST['reset_mail'])){
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
		}

		if(!isset($error)){
				
$content = '<?php
/* MAIL INFORMATIONS */
$email_sender = "'.$sender.'";
$email_reply_to = "'.$reply_to.'";
$email_subject = "'.$subject.'";

$email_message = "'.$message.'";
?>';
			file_put_contents(ADMIN_A_URL."/mail.php", $content);

			echo "<script type='text/javascript'>
				window.location.href='options-general.php?page=pm_config".$action."&status=updated'
			</script>";
		}
	}

?>