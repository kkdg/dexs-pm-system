	
	<script type="text/javascript">
		function visible(value){
			var field = value;
			if(document.getElementById('user_' + field).style.visibility == ''){
				document.getElementById('user_' + field).style.visibility = 'visible';
			} else {
				document.getElementById('user_' + field).style.visibility = '';
			}
		}	
	</script>
	
	<form method="post" action="">
	<input type="hidden" name="pm_id" value="<?php echo $get_pm->pm_id; ?>">
	<input type="hidden" name="send" value="true">
	<h3 class="pm_title"><?php _e('Sender', 'dexs-pm'); ?></h3>
	<table class="pm_sender_table">
		<tbody>
			<tr valign="top">
				<td width="550px;">
					<?php $sender_id = get_userdata($get_pm->pm_sender_id); ?>
					<?php echo get_avatar( $sender_id->ID, 40 ).$sender_id->display_name."<br>"; ?>
					<?php if($sender_id->ID == $current_user->ID){ ?>
						<input type='checkbox' name='send_pm_recipients[]' OnClick='visible(this.value);' value='<?php echo $sender_id->ID; ?>' disabled='disabled'>  | 
						<span class='write'><?php _e('You', 'dexs-pm'); ?></span>
					<?php } else { ?>
						<input type='checkbox' name='send_pm_recipients[]' OnClick='visible(this.value);' value='<?php echo $sender_id->ID; ?>'>  | 
						<span class='write'><a class='no-underline' href='<?php echo $url; ?>send_pm&action=write_to_users&id=<?php echo $get_pm->pm_sender_id; ?>'><?php _e('Write a PM', 'dexs-pm'); ?></a></span>
					<?php } ?>
				</td>
			</tr>
		</tbody>
	</table>
	
	<h3 class="pm_title"><?php _e('Recipients', 'dexs-pm'); ?></h3>
	<table class="pm_sender_table">
		<tbody>
			<tr valign="top">
				<?php $get_pm_recipients = explode(",", $get_pm->pm_recipient_ids); ?>
				<?php $i = 0; foreach($get_pm_recipients AS $get_pm_recipient){ $i++; ?>
					<?php $user = get_userdata($get_pm_recipient); ?>
					<?php if(strlen($user->display_name) > 20){
						echo "<td width='300px;' colspan='2'>"; $i++;
					} else {
						echo "<td width='150px;'>";
					} ?>
						<?php echo get_avatar( $get_pm_recipient, 40 ).$user->display_name."<br>"; ?>
						<div class="pm_table_actions" id="user_<?php echo $get_pm_recipient; ?>">
						<?php if($get_pm_recipient == $current_user->ID){ ?>
							<input type='checkbox' name='send_pm_recipients[]' OnClick='visible(this.value);' value='<?php echo $user->ID; ?>' disabled='disabled'>  | 
							<span class='write'><?php _e('You', 'dexs-pm'); ?></span>
						<?php } else { ?>
							<input type='checkbox' name='send_pm_recipients[]' OnClick='visible(this.value);' value='<?php echo $user->ID; ?>'>  | 
							<span class='write'><a class='no-underline' href='<?php echo $url; ?>send_pm&action=write_to_users&id=<?php echo $user->ID; ?>'><?php _e('Write a PM', 'dexs-pm'); ?></a></span>
						<?php } ?>
						</div>
					</td>
					
					<?php if($i == 3){ $i=0; ?>
							</tr>
							<tr valign="top">
					<?php } ?>
				<?php } ?>
			</tr>
		</tbody>
	</table>
	<input type='submit' name='write_to_users' class='pm_button' style="float:right;margin-top:2px;" value='<?php _e('Write to all selected users', 'dexs-pm')?>'>
					
	<h3 class="message_title" style="margin-top:30px;"><?php echo $get_pm->pm_subject; ?></h3>
	<div class="message_text">
		<?php echo stripslashes(nl2br($get_pm->pm_message)); ?>
		<br>
		<div style="text-align:right;width:100%;"><small style="font-size:11px;">
			<?php if(!isset($_GET['page']) || isset($_GET['page']) && $_GET['page'] != "pm_archive"){ echo "<a class='no-underline' href='".$url.$_GET['page']."&action=4&id=".$get_pm->pm_id."'>".__('Archive', 'dexs-pm')."</a> | "; } ?>
			<?php if(!isset($_GET['page']) || isset($_GET['page']) && $_GET['page'] != "pm_trash"){ echo "<a class='no-underline' href='".$url.$_GET['page']."&action=2&id=".$get_pm->pm_id."'>".__('Trash', 'dexs-pm')."</a>"; } ?>
			<?php if(isset($_GET['page']) && $_GET['page'] == "pm_trash"){ echo "<a class='no-underline' href='".$url.$_GET['page']."&action=1&id=".$get_pm->pm_id."'>".__('Restore', 'dexs-pm')."</a> | "; } ?>
			<?php if(isset($_GET['page']) && $_GET['page'] == "pm_trash"){ echo "<a class='no-underline' href='".$url.$_GET['page']."&action=3&id=".$get_pm->pm_id."'>".__('Delete Permanently', 'dexs-pm')."</a>"; } ?>
		</small></div>
	</div>
	<div style="width:100%;">
		<span style="min-width:50%;text-align:left;">
			<?php if($get_pm->pm_sender_id != $current_user->ID){ ?>
				<input type="button" class="pm_button" value="<?php _e('Answer', 'dexs-pm'); ?>" OnClick="window.location.href='<?php echo $url; ?>send_pm&action=answer&id=<?php echo $get_pm->pm_id; ?>'">
			<?php } ?>
			<input type='submit' name='answer_to_users' class='pm_button' value='<?php _e('Answer to all selected users', 'dexs-pm')?>'>
				<input type="button" class="pm_button" value="<?php _e('Forward', 'dexs-pm'); ?>" OnClick="window.location.href='<?php echo $url; ?>send_pm&action=forward&id=<?php echo $get_pm->pm_id; ?>'">
		</span>
	</div>
	</form>
	
	<hr class="pm_small">