	<?php
	$option = $dexsPM->load_pm_settings("settings");
	
	if(isset($_GET['folder']) && $_GET['page'] == "pm"){
		$folder = $_GET['folder'];
		if($folder == 3){ $folder = 2; }
	} else {
		$folder = "0";
	}
	
	if($dexsPM->load_pm_settings("settings", "backend_navi")){
		$spage = "admin.php?page=pm";
	} else {
		$spage = "users.php?page=pm";
	}
	?>
	<div class="wrap">
		<div id="icon" class="icon32"><a href="<?php echo $spage; ?>"><img src="<?php echo plugins_url('images/adminpm.png' , __FILE__); ?>"></a><br></div>				
	
		<?php if($option['backend_navi'] && $option['backend_style'] == 0 || !$option['backend_navi'] && $option['backend_style'] == 0){ ?>
		
			<!-- TAB STYLE (Like THEMES) -->
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo $spage; ?>&folder=5" class="nav-tab <?php if($folder == 5){ echo "nav-tab-active"; } ?>"><?php _e("Send PM", "dexs-pm"); ?></a>
				<a href="<?php echo $spage; ?>&folder=0" class="nav-tab <?php if($folder == 0){ echo "nav-tab-active"; } ?>"><?php _e("Inbox", "dexs-pm"); ?></a>
				<a href="<?php echo $spage; ?>&folder=1" class="nav-tab <?php if($folder == 1){ echo "nav-tab-active"; } ?>"><?php _e("Outbox", "dexs-pm"); ?></a>
				<a href="<?php echo $spage; ?>&folder=2" class="nav-tab <?php if($folder == 2){ echo "nav-tab-active"; } ?>"><?php _e("Trash", "dexs-pm"); ?></a>
				<a href="<?php echo $spage; ?>&folder=4" class="nav-tab <?php if($folder == 4){ echo "nav-tab-active"; } ?>"><?php _e("Archive", "dexs-pm"); ?></a>
				<a href="<?php echo $spage; ?>&folder=6" class="nav-tab <?php if($folder == 6){ echo "nav-tab-active"; } ?>"><?php _e("Settings", "dexs-pm"); ?></a>
			</h2>
			<br class="clear">
			
		<?php } elseif($option['backend_navi'] && $option['backend_style'] == 1 || !$option['backend_navi'] && ($option['backend_style'] == 1 || $option['backend_style'] == 2)){ ?>
			
			<?php $countPM = $dexsPMA->count_messages("all"); ?>
			
			<!-- TAB STYLE (Like POSTS) -->
			<h2><?php _e('Private Messages', 'dexs-pm'); ?> <a href="<?php echo $spage; ?>&folder=5" class="add-new-h2"><?php _e('Write a new PM', 'dexs-pm'); ?></a></h2>
			<ul class="subsubsub">
				<li class="folder_0">
					<a href="<?php echo $spage; ?>&folder=0" class="nav-link <?php if($folder == 0){ echo "current"; } ?>"><?php _e("Inbox", "dexs-pm"); ?>
						<span class="count">(<?php echo $countPM['new']; ?> / <?php echo $countPM['inbox']; ?>)</span>
					</a>
				</li>
				
				<li class="folder_1">
					<a href="<?php echo $spage; ?>&folder=1" class="nav-link <?php if($folder == 1){ echo "current"; } ?>"><?php _e("Outbox", "dexs-pm"); ?>
						<span class="count">(<?php echo $countPM['outbox']; ?>)</span>
					</a>
				</li>
				
				<li class="folder_2">
					<a href="<?php echo $spage; ?>&folder=2" class="nav-link <?php if($folder == 2){ echo "current"; } ?>"><?php _e("Trash", "dexs-pm"); ?>
						<span class="count">(<?php echo $countPM['trash']; ?>)</span>
					</a>
				</li>
				
				<li class="folder_4">
					<a href="<?php echo $spage; ?>&folder=4" class="nav-link <?php if($folder == 4){ echo "current"; } ?>"><?php _e("Archive", "dexs-pm"); ?>
						<span class="count">(<?php echo $countPM['archive'];?>)</span>
					</a>
				</li>
				
				<li class="folder_6">
					<a href="<?php echo $spage; ?>&folder=6" class="nav-link <?php if($folder == 6){ echo "current"; } ?>"><?php _e("Settings", "dexs-pm"); ?>
					</a>
				</li>
			</ul>
		
		<?php } elseif($option['backend_navi'] && $option['backend_style'] == 2){ ?>
			
			<!-- MENU STYLE (Like a MENU) -->
			<?php switch($folder){
				
				case "0":
					echo "<h2>".__('Private Messages', 'dexs-pm')." &rsaquo; ".__('Inbox', 'dexs-pm')."</h2>";
					break;	
					
				case "1":
					echo "<h2>".__('Private Messages', 'dexs-pm')." &rsaquo; ".__('Outbox', 'dexs-pm')."</h2>";
					break;
					
				case "2":
					echo "<h2>".__('Private Messages', 'dexs-pm')." &rsaquo; ".__('Trash', 'dexs-pm')."</h2>";
					break;
				
				case "4":
					echo "<h2>".__('Private Messages', 'dexs-pm')." &rsaquo; ".__('Archive', 'dexs-pm')."</h2>";
					break;
				
				case "5":
					echo "<h2>".__('Private Messages', 'dexs-pm')." &rsaquo; ".__('Send', 'dexs-pm')."</h2>";
					break;
				
				case "6":
					echo "<h2>".__('Private Messages', 'dexs-pm')." &rsaquo; ".__('Settings', 'dexs-pm')."</h2>";
					break;
				
				default:
					echo "<h2>".__('Private Messages', 'dexs-pm')." &rsaquo; ".__('Inbox', 'dexs-pm')."</h2>";
					break;
				
			} ?>
			
		<?php } ?>
			
		<br class="clear">
		<link rel='stylesheet' type='text/css' href='<?php echo plugins_url('include/css/admin_form.css', __FILE__); ?>'>
		<?php if($folder == "5"){ ?>
			<?php $dexsPMT->load_send_js("top"); ?>
		<?php } ?>

		<?php if(isset($_GET['success']) && $_GET['success']){ ?>
			<div id="settings-error-settings_updated" class="updated settings-error">
				<p><?php _e('<b>Congratulation:</b> The settings have been successfully saved!', 'dexs-pm'); ?></p>
			</div>
		<?php } else ?>
		
		<?php if(isset($_GET['send']) && $_GET['send']){ ?>
			<div id="settings-error-settings_updated" class="updated settings-error">
				<p><?php _e('<b>Congratulation:</b> The private message was sent successfully!', 'dexs-pm'); ?></p>
			</div>
		<?php } else ?>
		
		<?php if(isset($GLOBALS['pm_error'])){ ?>
			<div id="settings-error-settings_updated" class="error settings-error">
				<p><b><?php _e("Error", "dexs-pm"); ?></b> <?php echo $GLOBALS['pm_error']; ?>
			</div>
		<?php } ?>
		
		<?php $dexsPMT->load_backend_table($folder); ?>
		
		<?php if($folder == "5"){ ?>
			<?php $dexsPMT->load_send_js("bottom"); ?>
		<?php } ?>

	</div>