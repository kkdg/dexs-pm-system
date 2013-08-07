
	<?php $set = $dexsPM->user_settings("load", $this->cur_user); ?>		
	<form action="" method="post">
		<table class="pm_settings_table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e("Messages per Side", "dexs-pm"); ?> (<?php _e("Inbox", "dexs-pm"); ?>)</th>
					<td>
						<input type="number" min="5" max="100" value="<?php echo $set["inbox_num"]; ?>" name="inbox_num">
						<p class="description">
							<?php _e("Choose a number between 5 and 100", "dexs-pm"); ?>
						</p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><?php _e("Messages per Side", "dexs-pm"); ?> (<?php _e("Outbox", "dexs-pm"); ?>)</th>
					<td>
						<input type="number" min="5" max="100" value="<?php echo $set["outbox_num"]; ?>" name="outbox_num">
						<p class="description">
							<?php _e("Choose a number between 5 and 100", "dexs-pm"); ?>
						</p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><?php _e("Messages per Side", "dexs-pm"); ?> (<?php _e("Trash", "dexs-pm"); ?>)</th>
					<td>
						<input type="number" min="5" max="100" value="<?php echo $set["trash_num"]; ?>" name="trash_num">
						<p class="description">
							<?php _e("Choose a number between 5 and 100", "dexs-pm"); ?>
						</p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><?php _e("Messages per Side", "dexs-pm"); ?> (<?php _e("Archive", "dexs-pm"); ?>)</th>
					<td>
						<input type="number" min="5" max="100" value="<?php echo $set["archive_num"]; ?>" name="archive_num">
						<p class="description">
							<?php _e("Choose a number between 5 and 100", "dexs-pm"); ?>
						</p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><?php _e("Enable eMail Notifications", "dexs-pm"); ?></th>
					<td>
						<?php if($dexsPM->load_pm_settings("settings", "email_note")){ ?>
							<label>
								<input type="radio" value="0" name="user_email_note" <?php if($set['user_email_note'] == 0){ echo "checked='checked'"; } ?>> 
								<?php _e("No", "dexs-pm"); ?>
							</label> &nbsp;&nbsp;
							
							<label>
								<input type="radio" value="1" name="user_email_note" <?php if($set['user_email_note'] == 1){ echo "checked='checked'"; } ?>> 
							<?php _e("Yes", "dexs-pm"); ?>
						</label>
							<p class="description"><?php _e("Receive a email notification on new PMs!", "dexs-pm"); ?></p>
						<?php } else { ?>
							<label style="color:#666;"><input type="radio" value="0" disabled="disabled"> <?php _e("No", "dexs-pm"); ?></label> &nbsp;&nbsp;
							<label style="color:#666;"><input type="radio" value="1" disabled="disabled"> <?php _e("Yes", "dexs-pm"); ?></label>
							<p class="description"><?php _e("This feature has been disabled by an administrator!", "dexs-pm"); ?></p>
						<?php } ?>
					</td>
				</tr>
			</tbody>
		</table>
		
		<?php $stats = $dexsPMA->count_messages("6"); ?>
		<h3 class="title"><?php _e("Statistics", "dexs-pm"); ?></h3>
		<table class="pm_settings_table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e("Private Messages", "dexs-pm"); ?></th>
					<td>
						<code><?php echo $stats["all"]; ?></code>
						/ <code><?php if($dexsPM->check_permissions("max_messages") == "-1"){ echo "&infin;"; } else { echo $dexsPM->check_permissions("max_messages"); } ?></code>&nbsp;&nbsp;&nbsp;
						<i><p class="description" style="display:inline-block;">(Number / Maximum)</p></i>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><?php _e("Private Messages by folder", "dexs-pm"); ?></th>
					<td>
						<i style="color:#666;">Inbox:</i> <code><?php echo $stats["inbox"]; ?></code>&nbsp;&nbsp;
						<i style="color:#666;">Outbox:</i> <code><?php echo $stats["outbox"]; ?></code>&nbsp;&nbsp;
						<i style="color:#666;">Trash:</i> <code><?php echo $stats["trash"]; ?></code>&nbsp;&nbsp;
						<i style="color:#666;">Archive:</i> <code><?php echo $stats["archive"]; ?></code>
					</td>
				</tr>
			</tbody>
		</table>
		
		<table class="pm_settings_table">
			<tbody>
				<tr valign="top">
					<td colspan="2">
						<input type="hidden" name="dexs_pm" value="user_settings">
						<input type="hidden" value="7" name="action" id="user_settings_action">
						<input type="submit" value="<?php _e("Save Changes", "dexs-pm"); ?>" name="save" id="save" class="button-primary">&nbsp;&nbsp;&nbsp;
						<input type="submit" value="<?php _e("Reset to Default", "dexs-pm"); ?>" name="reset" id="reset" class="button-secondary">&nbsp;&nbsp;&nbsp;
					</td>
				</tr>
			</tbody>
		</table>
	</form>