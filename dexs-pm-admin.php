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

		<?php if(isset($_GET['success']) && $_GET['success'] < 4){ ?>
			<div id="settings-error-settings_updated" class="updated settings-error">
				<?php if($_GET['success'] == 1){ ?>
					<p><?php _e('<b>Congratulation:</b> The settings have been successfully saved!', 'dexs-pm'); ?></p>
				<?php } else ?>
				
				<?php if($_GET['success'] == 2){ ?>
					<p><?php _e('<b>Congratulation:</b> The settings have been successfully reset!', 'dexs-pm'); ?></p>
				<?php } else ?>
				
				<?php if($_GET['success'] == 3){ ?>
					<p><?php _e('<b>Congratulation:</b> The test notification eMail was successfully sent!', 'dexs-pm'); ?></p>
				<?php } ?>
			</div>
		<?php } else ?>
		
		<?php if(isset($GLOBALS['pm_error'])){ ?>
			<div id="settings-error-settings_updated" class="error settings-error">
				<p><b><?php _e("Error", "dexs-pm"); ?></b>: <?php echo $GLOBALS['pm_error']; ?>
			</div>
		<?php } ?>
		
		<?php if(!$dexsPM->get_frontend_id()){ ?>
			<div id="settings-error-settings_updated" class="error settings-error">
				<p><b><?php _e("Error", "dexs-pm"); ?></b>: <?php echo _e("You haven't create a (published) page with the shortcode <code>[pm_system]</code> and that means you cannot use the front-End PM Interface.", "dexs-pm"); ?>
			</div>
		<?php } ?>
		
		<?php
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