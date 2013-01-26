<!-------------------------------------------------
|	DEXS PM SYSTEM
|	GENERAL CONFIGURATION
---------------------------------------------------
|	Coypright 2012 - 2013
|	SamBrishesWeb WordPress
|	http://www.sambrishes.net/wordpress
-------------------------------------------------->

<?php if(isset($_GET['status'])){ ?>
	<?php if($_GET['status'] == "updated"){ ?>
		<div id="settings-error-settings_updated" class="updated settings-error">
			<p><b><?php _e('Settings saved.', 'dexs-pm'); ?></b></p>
		</div>
	<?php } ?>
<?php } ?>

<form action="" method="post">	
	<h3 class="title"><?php _e('Send Form', 'dexs-pm'); ?></h3>	
	<table class="form-table">
		<tbody>			
			<tr valign="top">
				<th scope="row"><?php _e('Recipient listing', 'dexs-pm'); ?></th>
				<td>
					<select name="recipient_listing" id="recipient_listing">
						<option value="0"<?php if($option['recipient_listing'] == "0"){ echo " selected='selected'"; } ?>><?php _e('DropDown Input Field', 'dexs-pm'); ?></option>
						<option value="1"<?php if($option['recipient_listing'] == "1"){ echo " selected='selected'"; } ?>><?php _e('AutoComplete Input Field', 'dexs-pm'); ?></option>
					</select>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('eMail Notification', 'dexs-pm'); ?></th>
				<td>
					<label for="email_notice_yes"><input type="radio" name="email_notice" value="1" id="email_notice_yes"<?php if($option['email_notice'] == "1"){ echo " checked='checked'"; } ?>> 
						<span><?php _e('Activate', 'dexs-pm'); ?></span></label><br>
					<label for="email_notice_non"><input type="radio" name="email_notice" value="0" id="email_notice_non"<?php if($option['email_notice'] == "0"){ echo " checked='checked'"; } ?>> 
						<span><?php _e('Deactivate', 'dexs-pm'); ?></span></label>
				</td>
			</tr>
		</tbody>
	</table>
	
	<h3 class="title"><?php _e('Backend', 'dexs-pm'); ?></h3>		
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e('Backend Style', 'dexs-pm'); ?></th>
				<td>
					<select name="backend_style" id="backend_style">
						<option value="0"<?php if($option['backend_style'] == "0"){ echo " selected='selected'"; } ?>><?php _e('One Page - Tab Style', 'dexs-pm'); ?></option>
						<option value="1"<?php if($option['backend_style'] == "1"){ echo " selected='selected'"; } ?>><?php _e('One Page - Link Style', 'dexs-pm'); ?></option>
						<option value="2"<?php if($option['backend_style'] == "2"){ echo " selected='selected'"; } ?>><?php _e('Five Pages - Menu Style', 'dexs-pm'); ?></option>
					</select>
					<p class="description"><?php _e('"Five-Pages - Menu Style" is not available, when you Subordinate the Menu under "Users".', 'dexs-pm'); ?></p>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('Show in Toolbar', 'dexs-pm'); ?></th>
				<td>
					<label for="showin_toolbar_yes"><input type="radio" name="showin_toolbar" value="1" id="showin_toolbar_yes"<?php if($option['showin_toolbar'] == "1"){ echo " checked='checked'"; } ?>> 
						<span><?php _e('Yes', 'dexs-pm'); ?></span></label><br>
					<label for="showin_toolbar_non"><input type="radio" name="showin_toolbar" value="0" id="showin_toolbar_non"<?php if($option['showin_toolbar'] == "0"){ echo " checked='checked'"; } ?>> 
						<span><?php _e('No', 'dexs-pm'); ?></span></label>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('Show in Navigation', 'dexs-pm'); ?></th>
				<td>
					<label for="showin_navigation_1"><input type="radio" name="showin_navigation" value="1" id="showin_navigation_1"<?php if($option['showin_navigation'] == "1"){ echo " checked='checked'"; } ?>> 
						<span><?php _e('Extra menu entry', 'dexs-pm'); ?></span></label><br>
					<label for="showin_navigation_0"><input type="radio" name="showin_navigation" value="0" id="showin_navigation_0"<?php if($option['showin_navigation'] == "0"){ echo " checked='checked'"; } ?>> 
						<span><?php _e('Subordinate under Users', 'dexs-pm'); ?></span></label>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('Show Our Copyright', 'dexs-pm'); ?></th>
				<td>
					<label for="backend_copyright_yes"><input type="radio" name="backend_copyright" value="1" id="backend_copyright_yes"<?php if($option['backend_copyright'] == "1"){ echo " checked='checked'"; } ?>> 
						<span><?php _e('Yes', 'dexs-pm'); ?></span></label><br>
					<label for="backend_copyright_non"><input type="radio" name="backend_copyright" value="0" id="backend_copyright_non"<?php if($option['backend_copyright'] == "0"){ echo " checked='checked'"; } ?>> 
						<span><?php _e('No', 'dexs-pm'); ?></span></label>
				</td>
			</tr>
		</tbody>
	</table>
	
	<h3 class="title"><?php _e('Frontend', 'dexs-pm'); ?></h3>		
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e('Page Type', 'dexs-pm'); ?></th>
				<td>
					<select name="frontend_style" id="frontend_style">
						<option value="0"<?php if($option['frontend_style'] == "0"){ echo " selected='selected'"; } ?>><?php _e('Normal Page with Sidebar', 'dexs-pm'); ?></option>
						<option value="1"<?php if($option['frontend_style'] == "1"){ echo " selected='selected'"; } ?>><?php _e('Fullwide Page without Sidebar', 'dexs-pm'); ?></option>
					</select>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('PM Theme', 'dexs-pm'); ?></th>
				<td>
					<select name="frontend_theme" id="frontend_theme">
						<?php get_pm_themes($option['frontend_theme']); ?>
					</select>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('Show Template Copyright', 'dexs-pm'); ?></th>
				<td>
					<label for="frontend_tcopy_yes"><input type="radio" name="frontend_tcopy" value="1" id="frontend_tcopy_yes"<?php if($option['frontend_tcopy'] == "1"){ echo " checked='checked'"; } ?>> 
						<span><?php _e('Yes', 'dexs-pm'); ?></span></label><br>
					<label for="frontend_tcopy_non"><input type="radio" name="frontend_tcopy" value="0" id="frontend_tcopy_non"<?php if($option['frontend_tcopy'] == "0"){ echo " checked='checked'"; } ?>> 
						<span><?php _e('No', 'dexs-pm'); ?></span></label>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('Show Our Copyright', 'dexs-pm'); ?></th>
				<td>
					<label for="frontend_copy_yes"><input type="radio" name="frontend_copy" value="1" id="frontend_copy_yes"<?php if($option['frontend_copy'] == "1"){ echo " checked='checked'"; } ?>> 
						<span><?php _e('Yes', 'dexs-pm'); ?></span></label><br>
					<label for="frontend_copy_non"><input type="radio" name="frontend_copy" value="0" id="frontend_copy_non"<?php if($option['frontend_copy'] == "0"){ echo " checked='checked'"; } ?>> 
						<span><?php _e('No', 'dexs-pm'); ?></span></label>
				</td>
			</tr>
		</tbody>
	</table>
	
	<br class="clear">
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<td colspan="2">
					<input type="hidden" value="general" name="type" id="general">
					<input type="submit" value="<?php _e('Save Changes', 'dexs-pm'); ?>" name="save" id="save" class="button-primary">&nbsp;&nbsp;&nbsp;
					<input type="submit" value="<?php _e('Reset to Default', 'dexs-pm'); ?>" name="reset" id="reset" class="button-secondary">
				</td>
			</tr>
		</tbody>
	</table>
</form>