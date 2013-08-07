	
	<form action="" method="post">	
		<fieldset class="dexspm_fieldset">
			<legend class="dexspm_legend"><?php _e('eMail Notification Configuration', 'dexs-pm'); ?></legend>
			
			<table class="form-table">
				<tbody>			
					<tr valign="top">
						<th scope="row"><?php _e('eMail Address (Sender)', 'dexs-pm'); ?></th>
						<td>
							<input type="text" name="mail_address" value="<?php echo $option['mail_address']; ?>" class="regular-text">
						</td>
					</tr>		
					
					<tr valign="top">
						<th scope="row"><?php _e('eMail Address (Reply-To)', 'dexs-pm'); ?></th>
						<td>
							<input type="text" name="reply_address" value="<?php echo $option['reply_address']; ?>" class="regular-text">
							<p class="description"><?php _e('Specify an exist(!) eMail address, when you want that the recipients can answer to a notification!', 'dexs-pm'); ?></p>
						</td>
					</tr>		
					
					<tr valign="top">
						<th scope="row"><?php _e('eMail Subject', 'dexs-pm'); ?></th>
						<td>
							<input type="text" name="mail_subject" value="<?php echo $option['mail_subject']; ?>" class="regular-text">
						</td>
					</tr>		
					
					<tr valign="top">
						<th scope="row"><?php _e('eMail Message', 'dexs-pm'); ?></th>
						<td>
							<textarea name="mail_message" rows="20" class="widefat"><?php echo stripslashes($option['mail_message']); ?></textarea><br>
							
							<small><b><?php _e('Blog Shortcodes:', 'dexs-pm'); ?></b><br>
								<code>%HOME_TIL%</code> = (<i><?php _e('Site Title', 'dexs-pm'); ?></i>) = <i><?php echo "<b>".__('Example:', 'dexs-pm')."</b> ".get_bloginfo("name"); ?></i><br>
								<code>%HOME_URL%</code> = (<i><?php _e('Home Url', 'dexs-pm'); ?></i>) = <i><?php echo "<b>".__('Example:', 'dexs-pm')."</b> ".get_bloginfo("url"); ?></i><br>
								<code>%ADM_MAIL%</code> = (<i><?php _e('eMail Adress', 'dexs-pm'); ?></i>) = <i><?php echo "<b>".__('Example:', 'dexs-pm')."</b> ".get_bloginfo("admin_email"); ?></i><br>
							<b><?php _e('PM Shortcodes:', 'dexs-pm'); ?></b></small><br>
							<table style="background:none;border:none;padding:0;margin:0;font-size:10px;"><tr>
								<td style="background:none;border:none;padding:0;margin:0;font-size:10px;"><code>%RECI_NAME%</code> = <i><?php _e('PM Recipient Name', 'dexs-pm'); ?></i></td>
								<td style="background:none;border:none;padding:0;margin:0;font-size:10px;"><code>%SEND_NAME%</code> = <i><?php _e('PM Sender Name', 'dexs-pm'); ?></i></td>
							</tr><tr>
								<td style="background:none;border:none;padding:0;margin:0;font-size:10px;"><code>%PM_SUB%</code> = <i><?php _e('PM Subject', 'dexs-pm'); ?></i></td>
								<td style="background:none;border:none;padding:0;margin:0;font-size:10px;"><code>%PM_TEXT%</code> = <i><?php _e('PM Text (Full)', 'dexs-pm'); ?></i></td>
							</tr><tr>
								<td style="background:none;border:none;padding:0;margin:0;font-size:10px;" colspan="2"><code>%PM_EXC_</code><i><?php _e('Number', 'dexs-pm'); ?></i><code>%</code> = <i><?php _e('PM Text Excerpt: Replace <b>Number</b> with any number of letters!', 'dexs-pm'); ?></i></td>
							</tr><tr>
								<td style="background:none;border:none;padding:0;margin:0;font-size:10px;"><code>%PM_DATE%</code> = <i><?php _e('PM Send Date', 'dexs-pm'); ?></i></td>
								<td style="background:none;border:none;padding:0;margin:0;font-size:10px;"><code>%PM_TIME%</code> = <i>P<?php _e('M Send Time', 'dexs-pm'); ?></i></td>
							</tr><tr>
								<td style="background:none;border:none;padding:0;margin:0;font-size:10px;"><code>%PM_F_LINK%</code> = <i><?php _e('Link to the PM (Frontend View)', 'dexs-pm'); ?></i></td>
								<td style="background:none;border:none;padding:0;margin:0;font-size:10px;"><code>%PM_B_LINK%</code> = <i><?php _e('Link to the PM (Backend View)', 'dexs-pm'); ?></i></td>
							</tr></table>
						</td>
					</tr>	
				</tbody>
			</table>
		</fieldset>	
		<br class="clear">
		
		<fieldset class="dexspm_fieldset">
			<legend class="dexspm_legend"><?php _e('Send a Demo Notification eMail', 'dexs-pm'); ?></legend>
			
			<table class="form-table" style="width:auto;">
				<tbody>
					<tr valign="top">
						<th scope="row">
							<?php _e('Send a Demo eMail to:', 'dexs-pm'); ?>
						</th>
						<td>
							<input type="text" value="" name="test_mail_recipient" class="regular-text">
							<input type="submit" value="<?php _e('Send Now', 'dexs-pm'); ?>" name="send_test_mail" id="save" class="button-secondary">&nbsp;&nbsp;&nbsp;
							<p class="description">
								<?php _e("The settings are saved, before the Demo eMail will be sent.", "dexs-pm"); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>		
			
		<table class="form-table" style="width:auto;">
			<tbody>
				<tr valign="top">
					<td colspan="2">
						<input type="hidden" value="settings" name="dexs_pm" id="auth">
						<input type="hidden" value="6" name="action" id="settings_action">
						<input type="hidden" value="2" name="tab" id="settings_tab">
						<input type="submit" value="<?php _e('Save Changes', 'dexs-pm'); ?>" name="save" id="save" class="button-primary">&nbsp;&nbsp;&nbsp;
						<input type="submit" value="<?php _e('Reset to Default', 'dexs-pm'); ?>" name="reset" id="reset" class="button-secondary">
					</td>
				</tr>
			</tbody>
		</table>
	</form>