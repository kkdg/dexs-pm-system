<!-------------------------------------------------
|	DEXS PM SYSTEM
|	EMAIL NOTIFICATION SETTINGS
---------------------------------------------------
|	Coypright 2012 - 2013
|	SamBrishesWeb WordPress
|	http://www.sambrishes.net/wordpress
-------------------------------------------------->
<?php $mail = get_pm_email(); ?>

<?php if(isset($_GET['status'])){ ?>
	<?php if($_GET['status'] == "updated"){ ?>
		<div id="settings-error-settings_updated" class="updated settings-error">
			<p><b><?php _e('Settings saved.', 'dexs-pm'); ?></b></p>
		</div>
	<?php } ?>
<?php } else if(isset($error)){ ?>
		<div id="settings-error-settings_updated" class="error settings-error">
			<?php foreach($error AS $e){ ?>
			<p><b><?php echo $e; ?></b></p>
			<?php } ?>
		</div>
<?php } ?>

<form action="" method="post">	
	<h3><?php _e('eMail Notification Configruation', 'dexs-pm'); ?></h3>
	<p class="description"><?php _e('This Informations will be stored in the <code>mail.php</code> file, in the plugin directory.', 'dexs-pm'); ?></p><br>
	<table class="form-table">
		<tbody>			
			<tr valign="top">
				<th scope="row"><?php _e('eMail Address (Sender)', 'dexs-pm'); ?></th>
				<td>
					<input type="text" name="email_sender" value="<?php echo $mail['email_sender']; ?>" class="regular-text">
				</td>
			</tr>		
			
			<tr valign="top">
				<th scope="row"><?php _e('eMail Address (Reply-To)', 'dexs-pm'); ?></th>
				<td>
					<input type="text" name="email_reply_to" value="<?php echo $mail['email_reply_to']; ?>" class="regular-text">
					<p class="description"><?php _e('Specify an exist(!) eMail address, when you want that the recipients can answer to a notification!', 'dexs-pm'); ?></p>
				</td>
			</tr>		
			
			<tr valign="top">
				<th scope="row"><?php _e('eMail Subject', 'dexs-pm'); ?></th>
				<td>
					<input type="text" name="email_subject" value="<?php echo $mail['email_subject']; ?>" class="regular-text">
				</td>
			</tr>		
			
			<tr valign="top">
				<th scope="row"><?php _e('eMail Message', 'dexs-pm'); ?></th>
				<td>
					<textarea name="email_message" rows="20" cols="100"><?php echo stripslashes($mail['email_message']); ?></textarea><br>
					
					<small><b><?php _e('Blog Shortcodes:', 'dexs-pm'); ?></b><br>
						<code>%HOME_TIL%</code> = (<i><?php _e('Site Title', 'dexs-pm'); ?></i>) = <i><?php echo "<b>".__('Example:', 'dexs-pm')."</b> ".get_bloginfo("name"); ?><br>
						<code>%HOME_URL%</code> = (<i><?php _e('Home Url', 'dexs-pm'); ?></i>) = <?php echo "<b>".__('Example:', 'dexs-pm')."</b> ".get_bloginfo("home"); ?><br>
						<code>%ADM_MAIL%</code> = (<i><?php _e('eMail Adress', 'dexs-pm'); ?></i>) =  <?php echo "<b>".__('Example:', 'dexs-pm')."</b> ".get_bloginfo("admin_email"); ?><br>
					<b><?php _e('PM Shortcodes:', 'dexs-pm'); ?></b></small><br>
					<table style="background:none;border:none;padding:0;margin:0;font-size:10px;"><tr>
						<td style="background:none;border:none;padding:0;margin:0;font-size:10px;"><code>%RECI_NAME%</code> = <i><?php _e('PM Recipient Name', 'dexs-pm'); ?></i></td>
						<td style="background:none;border:none;padding:0;margin:0;font-size:10px;"><code>%SEND_NAME%</code> = <i><?php _e('PM Sender Name', 'dexs-pm'); ?></i></td>
					</tr><tr>
						<td style="background:none;border:none;padding:0;margin:0;font-size:10px;"><code>%PM_SUB%</code> = <i><?php _e('PM Subject', 'dexs-pm'); ?></i></td>
						<td style="background:none;border:none;padding:0;margin:0;font-size:10px;"><code>%PM_TXT%</code> = <i><?php _e('PM Text (Full)', 'dexs-pm'); ?></i></td>
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
	
	<br class="clear">
	<table class="form-table" style="width:auto;">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<?php _e('Send a Test eMail to:', 'dexs-pm'); ?>
				</th>
				<td>
					<input type="text" value="" name="test_mail_recipient" class="regular-text">
					<input type="submit" value="<?php _e('Send Now', 'dexs-pm'); ?>" name="test_mail" id="save" class="button-secondary">&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
			<tr valign="top">
				<td colspan="2"><br clasS="clear">
					<input type="submit" value="<?php _e('Save Changes', 'dexs-pm'); ?>" name="save_mail" id="save" class="button-primary">&nbsp;&nbsp;&nbsp;
					<input type="submit" value="<?php _e('Reset to Default', 'dexs-pm'); ?>" name="reset_mail" id="reset" class="button-secondary">
				</td>
			</tr>
		</tbody>
	</table>
</form>