	<?php
		$o = ""; $a = ""; $t = ""; $i = "";
		if($table == "inbox"){
			$i = "active";
			$name = __('Inbox', 'dexs-pm');
			$first_td = __('Sender', 'dexs-pm');
		}
		if($table == "outbox"){
			$o = "active";
			$name = __('Outbox', 'dexs-pm');
			$first_td = __('Recipients', 'dexs-pm');
		}
		if($table == "archive"){		
			$a = "active";
			$name = __('Archive', 'dexs-pm');
			$first_td = __('Users', 'dexs-pm');	
		}
		if($table == "trash"){
			$t = "active";
			$name = __('Trash', 'dexs-pm');
			$first_td = __('Users', 'dexs-pm');
		}
	?>
	
	<div class="pm_new"><input type="submit" value="<?php _e('Write a new PM', 'dexs-pm'); ?>" class="pm_button" OnClick="window.location.href='<?php echo $url ?>send_pm'"></div>
	
	<div class="pm_navigation">
		<a href="<?php echo $url."pm"; ?>" title="<?php _e('Go to Inbox', 'dexs-pm'); ?>" class="<?php echo $i ?>"><?php _e('Inbox', 'dexs-pm'); ?></a> 
			<span class="counter">(<?php echo count_pm('', 'inbox'); ?>)</span> | 
			
		<a href="<?php echo $url."pm_outbox"; ?>" title="<?php _e('Go to Outbox', 'dexs-pm'); ?>" class="<?php echo $o ?>"><?php _e('Outbox', 'dexs-pm'); ?></a> 
			<span class="counter">(<?php echo count_pm('', 'outbox'); ?>)</span> | 
			
		<a href="<?php echo $url."pm_archive"; ?>" title="<?php _e('Go to Archive', 'dexs-pm'); ?>" class="<?php echo $a ?>"><?php _e('Archive', 'dexs-pm'); ?></a> 
			<span class="counter">(<?php echo count_pm('', 'archive'); ?>)</span> | 
			
		<a href="<?php echo $url."pm_trash"; ?>" title="<?php _e('Go to Trash', 'dexs-pm'); ?>" class="<?php echo $t ?>"><?php _e('Trash', 'dexs-pm'); ?></a> 
			<span class="counter">(<?php echo count_pm('', 'trash'); ?>)</span>
		
		<span><a href="<?php echo $url ?>pm_settings"><div class="pm_settings" title="<?php _e('Settings', 'dexs-pm'); ?>"></div></a></span>
	</div>