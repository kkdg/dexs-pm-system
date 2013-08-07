	<?php			
		if(isset($_GET['tab'])){
			$tab = $_GET['tab'];
		} else {
			$tab = 0;
		}	
	?>	
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2 class="nav-tab-wrapper">
			<a href="options-general.php?page=pm_config&tab=0" class="nav-tab <?php if($tab == 0){ echo "nav-tab-active"; } ?>"><?php _e('General Settings', 'dexs-pm'); ?></a>
			<a href="options-general.php?page=pm_config&tab=1" class="nav-tab <?php if($tab == 1){ echo "nav-tab-active"; } ?>"><?php _e('Permission Settings', 'dexs-pm'); ?></a>
			<a href="options-general.php?page=pm_config&tab=2" class="nav-tab <?php if($tab == 2){ echo "nav-tab-active"; } ?>"><?php _e('eMail Notification', 'dexs-pm'); ?></a>
		</h2>
		<br class="clear">	
			
		<style type="text/css">
			<!--
				fieldset.dexspm_fieldset{
					border:1px solid #ccc;
				}
				fieldset.dexspm_fieldset.error{
					border-color: #c30000;
					background: #FFF0F5;
				}
				fieldset.dexspm_fieldset.success{
					border-color: #32CD32;
					background: #B0FFB0;
				}
				fieldset.dexspm_fieldset.info{
					border-color: #40E0D0;
					background: #F5FFFA;
				}
				legend.dexspm_legend{
					color:#999;
					margin-left: 20px;
					font-family: Georgia, 'Times New Roman', 'Bitstream Charter', Times, serif;
					font-style:italic;
				}
				fieldset.dexspm_fieldset table.form-table tbody tr th{
					color: #555;
					font-style:italic;
				}
				p.dexspm_message{
					margin: 0;
					padding: 8px 20px 10px 20px;
					font-style: italic;
				}
				p.dexspm_message.error, legend.dexspm_legend.error{
					color: #CD5C5C;
				}
				p.dexspm_message.success, legend.dexspm_legend.success{
					color: #228B22;
				}
				p.dexspm_message.info, legend.dexspm_legend.info{
					color: #008B8B;
				}
			-->
		</style>
		
		<?php 
		if($dexsPM->load_pm_settings("settings", "use_frontend") && !$dexsPM->get_frontend_id()){
			$GLOBALS['dpm_message'] = array(
				"type"		=>	"info",
				"message"	=>	__("You haven't create a (published) page with the shortcode <code>[pm_system]</code> and that means you cannot use the front-End PM Interface.", "dexs-pm") 
			);
		}
		
		if(isset($_GET['tab']) && $_GET['tab'] == 2 && !$dexsPM->load_pm_settings("settings", "email_note")){
			$GLOBALS['dpm_message'] = array(
				"type"		=>	"info",
				"message"	=>	__("The eMail Notification System is disabled! You can activate the System under the \"General Settings\" Tab.", "dexs-pm") 
			);		
		}
		
		if(isset($GLOBALS['pm_error'])){
			$GLOBALS['dpm_message'] = array(
				"type"		=>	"error",
				"message"	=> 	$GLOBALS['pm_error']
			);
		}
		
		if(isset($_GET['success'])){
			$GLOBALS['dpm_message'] = array(
				"type"		=>	"success",
				"message"	=> 	""
			);
			
			if($_GET['success'] == 1){ $GLOBALS['dpm_message']["message"] = __('The settings have been successfully saved!', 'dexs-pm'); }
			if($_GET['success'] == 2){ $GLOBALS['dpm_message']["message"] = __('The settings have been successfully reset!', 'dexs-pm'); }
			if($_GET['success'] == 3){ $GLOBALS['dpm_message']["message"] = __('The Demo Notification eMail was successfully sent!', 'dexs-pm'); }
			if($_GET['success'] == 4){ $GLOBALS['dpm_message']["message"] = __('The Access for this role was successfully removed!', 'dexs-pm'); }
		}

		if(isset($GLOBALS['dpm_message'])){
			echo "<fieldset class='dexspm_fieldset ".$GLOBALS['dpm_message']['type']."'>";
				echo "<legend class='dexspm_legend ".$GLOBALS['dpm_message']['type']."'>".__("Info: Dexs PM System", "dexs-pm")."</legend>";
				echo "<p class='dexspm_message ".$GLOBALS['dpm_message']['type']."'>".$GLOBALS['dpm_message']["message"]."</p>";
			echo "</fieldset><br class='clear'>";
		}

		switch($tab){
			# PERMISSION SETTINGS
			case '1':
				$option = $dexsPM->load_pm_settings("permissions");
				include("include/admin.permissions.php");
				break;				
			
			# eMAIL NOTIFICATIONS
			case '2':
				$option = $dexsPM->load_pm_settings("email");
				include("include/admin.notifications.php");			
				break;				
			
			# GENERAL SETTINGS
			default:
				$option = $dexsPM->load_pm_settings("settings");
				include("include/admin.general.php");		
				break;
		}
		?>
	</div>