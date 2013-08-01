<?php
	$dexsPMT->load_pm_template_header();
?>
	<script type="text/javascript">
		function markAll(){
			if(document.getElementById('all').checked){
				for(i = 0; i < document.messages.length; i++){
					document.messages.elements["pm_id[]"][i].checked = true;
				}			
			} else {
				for(i = 0; i < document.messages.length; i++){
					document.messages.elements["pm_id[]"][i].checked = false;
				}
			}
		}
		
		function markMe(){
			if(document.getElementById('all').checked){
				document.getElementById('all').checked = false;
			}
		}
	</script>
	<link rel="stylesheet" type="text/css" href="<?php echo $dexsPMT->get_template_css(); ?>" />
	
		
	<div class="pm_new"><a href="<?php $this->get_folder_url(5, true); ?>"><input type="submit" value="<?php _e('Write a new PM', 'dexs-pm'); ?>" class="pm_button"></a></div>
	<div class="pm_navigation">
		<a href="<?php $dexsPMT->get_folder_url("0", true); ?>" title="<?php _e('Inbox', 'dexs-pm'); ?>" class="<?php if($dexsPMT->current_folder(0) == 0){ echo "active";  } ?>"><?php _e('Inbox', 'dexs-pm'); ?></a> 
			<span class="counter">(<?php $dexsPMA->count_messages('new', true); ?> / <?php $dexsPMA->count_messages(0, true); ?>) | </span>
			
		<a href="<?php $dexsPMT->get_folder_url(1, true); ?>" title="<?php _e('Outbox', 'dexs-pm'); ?>" class="<?php if($dexsPMT->current_folder(1)){ echo "active";  } ?>"><?php _e('Outbox', 'dexs-pm'); ?></a> 
			<span class="counter">(<?php $dexsPMA->count_messages('outbox', true); ?>) | </span>
			
		<a href="<?php $dexsPMT->get_folder_url(4, true); ?>" title="<?php _e('Archive', 'dexs-pm'); ?>" class="<?php if($dexsPMT->current_folder(4)){ echo "active";  } ?>"><?php _e('Archive', 'dexs-pm'); ?></a> 
			<span class="counter">(<?php $dexsPMA->count_messages('archive', true); ?>) | </span>
			
		<a href="<?php $dexsPMT->get_folder_url(2, true); ?>" title="<?php _e('Trash', 'dexs-pm'); ?>" class="<?php if($dexsPMT->current_folder(2)){ echo "active";  } ?>"><?php _e('Trash', 'dexs-pm'); ?></a> 
			<span class="counter">(<?php $dexsPMA->count_messages('trash', true); ?>)</span>
		
		<p style="text-align:right;display:inline-block;float:right;padding:0;margin:0"><small>
			<a href="<?php $dexsPMT->get_folder_url(6, true); ?>" title="<?php _e('Settings', 'dexs-pm'); ?>" class="<?php if($dexsPMT->current_folder(6)){ echo "active";  } ?>"><?php _e('Settings', 'dexs-pm'); ?></a> 
		</small></p>
	</div>
	<br class="clear">