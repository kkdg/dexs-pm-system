
<form action="" method="post">
									
	<h3 class="pm_title"><?php _e('Messages per Side', 'dexs-pm'); ?></h3>	
	<table class="pm_sender_table"><tr>
		<td style="padding: 0 20px 0 10px;"><label for="inbox"><?php _e('Inbox', 'dexs-pm'); ?></label></td>
		<td style="padding: 0 20px 0 10px;"><label for="outbox"><?php _e('Outbox', 'dexs-pm'); ?></label></td>
		<td style="padding: 0 20px 0 10px;"><label for="archive"><?php _e('Archive', 'dexs-pm'); ?></label></td>
		<td style="padding: 0 20px 0 10px;"><label for="trash"><?php _e('Trash', 'dexs-pm'); ?></label></td>
	</tr><tr>
		<td style="padding: 0 20px 0 10px;">
			<input type="number" step="1" min="-1" id="inbox" name="inbox_num" value="<?php if($user_settings['inbox_num'] != ""){ echo $user_settings['inbox_num']; } else { echo "20"; } ?>">
		</td>
		<td style="padding: 0 20px 0 10px;">
			<input type="number" step="1" min="-1" id="outbox" name="outbox_num" value="<?php if($user_settings['outbox_num'] != ""){ echo $user_settings['outbox_num']; } else { echo "20"; } ?>">
		</td>
		<td style="padding: 0 20px 0 10px;">
			<input type="number" step="1" min="-1" id="archive" name="archive_num" value="<?php if($user_settings['archive_num'] != ""){ echo $user_settings['archive_num']; } else { echo "20"; } ?>">
		</td>
		<td style="padding: 0 20px 0 10px;">
			<input type="number" step="1" min="-1" id="trash" name="trash_num" value="<?php if($user_settings['trash_num'] != ""){ echo $user_settings['trash_num']; } else { echo "20"; } ?>">
		</td>						
	</tr></table>

		
	<?php if($options['email_notice'] == "1"){ ?>
		<h3 class="pm_title"><?php _e('eMail Notification', 'dexs-pm'); ?></h3>
		<table class="pm_sender_table">
			<tbody>			
				<tr valign="top">
					<td style="padding: 0 20px 0 10px;">
						<label for="enable"><input type="radio" value="1" name="email_notice" id="enable" <?php if($check == "1"){ echo "checked='checked'"; } ?>>
							<span><?php _e('Activate', 'dexs-pm'); ?></span></label><br>
						<label for="disable"><input type="radio" value="0" name="email_notice" id="disable" <?php if($check == "0"){ echo "checked='checked'"; } ?>>
							<span><?php _e('Deactivate', 'dexs-pm'); ?></span></label>
					
					</td>
				</tr>
			</tbody>
		</table>
	<?php } ?>			
	
	<br><p class="submit"><input type="submit" name="submit_user" id="submit_user" value="<?php _e('Save Changes', 'dexs-pm'); ?>" class="pm_button"></p>

</form>