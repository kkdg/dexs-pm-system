	
	<form action="" method="post">
		<fieldset class="dexspm_fieldset">
			<legend class="dexspm_legend"><?php _e('General Settings', 'dexs-pm'); ?></legend>
			
			<table class="form-table">
				<tbody>	
					
					<tr valign="top">
						<th scope="row"><?php _e('Enable the eMail Notifications', 'dexs-pm'); ?></th>
						<td>
							<label for="email_note_yes"><input type="radio" name="email_note" value="1" id="email_note_yes"<?php if($option['email_note'] == "1"){ echo " checked='checked'"; } ?>> 
								<span><?php _e('Yes', 'dexs-pm'); ?></span></label>&nbsp;&nbsp;&nbsp;&nbsp;
							<label for="email_note_non"><input type="radio" name="email_note" value="0" id="email_note_non"<?php if($option['email_note'] == "0"){ echo " checked='checked'"; } ?>> 
								<span><?php _e('No', 'dexs-pm'); ?></span></label>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Show our Copyright Notice', 'dexs-pm'); ?></th>
						<td>
							<label for="show_copy_yes"><input type="radio" name="show_copy" value="1" id="show_copy_yes"<?php if($option['show_copy'] == "1"){ echo " checked='checked'"; } ?>> 
								<span><?php _e('Yes', 'dexs-pm'); ?></span></label>&nbsp;&nbsp;&nbsp;&nbsp;
							<label for="show_copy_non"><input type="radio" name="show_copy" value="0" id="show_copy_non"<?php if($option['show_copy'] == "0"){ echo " checked='checked'"; } ?>> 
								<span><?php _e('No', 'dexs-pm'); ?></span></label>
						</td>
					</tr>
				
					<tr valign="top">
						<th scope="row"><?php _e('Default Permission Settings', 'dexs-pm'); ?></th>
						<td>
							<select id="standard_role" name="standard_role">
								<?php foreach($wp_roles->roles AS $role_key => $role){
									if(array_key_exists($role_key, $dexsPM->load_pm_settings("permissions"))){
									?>								
										<option value="<?php echo $role_key; ?>" <?php if($role_key == $dexsPM->load_pm_settings("settings", "standard_role")){ echo "selected='selected'"; } ?>>
											<?php echo translate_user_role(ucfirst($role['name'])); ?> (<?php echo $role_key; ?>)
										</option>									
									<?php } ?>
								<?php } ?>
							</select>
							<p class="description"><?php _e("For undefined Costum User Roles", "dexs-pm"); ?></p>
						</td>
					</tr>
					
				</tbody>
			</table>
		</fieldset><br class="clear">
				
		<fieldset class="dexspm_fieldset">
			<legend class="dexspm_legend"><?php _e('Advanced Settings', 'dexs-pm'); ?></legend>
			
			<table class="form-table">
				<tbody>
					
					<tr valign="top">
						<th scope="row"><?php _e('Use Archive Folder', 'dexs-pm'); ?></th>
						<td><!--
							<label for="use_archive_yes"><input type="radio" name="use_archive" value="1" id="use_archive_yes"<?php #if($option['use_archive'] == "1"){ echo " checked='checked'"; } ?> disabled="disabled"> 
								<span><?php #_e('Yes', 'dexs-pm'); ?></span></label>&nbsp;&nbsp;&nbsp;&nbsp;
							<label for="use_archive_non"><input type="radio" name="use_archive" value="0" id="use_archive_non"<?php #if($option['use_archive'] == "0"){ echo " checked='checked'"; } ?> disabled="disabled"> 
								<span><?php #_e('No', 'dexs-pm'); ?></span></label>
							-->
							<p class="description">
								<?php _e("This feature comes with the next update (1.1.0).", "dexs-pm"); ?>
							</p>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Use Trash Folder', 'dexs-pm'); ?></th>
						<td><!--
							<label for="use_trash_yes"><input type="radio" name="use_trash" value="1" id="use_trash_yes"<?php #if($option['use_trash'] == "1"){ echo " checked='checked'"; } ?> disabled="disabled"> 
								<span><?php #_e('Yes', 'dexs-pm'); ?></span></label>&nbsp;&nbsp;&nbsp;&nbsp;
							<label for="use_trash_non"><input type="radio" name="use_trash" value="0" id="use_trash_non"<?php #if($option['use_trash'] == "0"){ echo " checked='checked'"; } ?> disabled="disabled"> 
								<span><?php #_e('No', 'dexs-pm'); ?></span></label>
							-->
							<p class="description">
								<?php _e("This feature comes with the next update (1.1.0).", "dexs-pm"); ?>
							</p>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Recipient List Type', 'dexs-pm'); ?></th>
						<td>
							<select name="list_style" id="list_style">
								<option value="1"<?php if($option['list_style'] == "1"){ echo " selected='selected'"; } ?>><?php _e('AutoComplete Input Field (Prototype)', 'dexs-pm'); ?></option>
								<option value="0"<?php if($option['list_style'] == "0"){ echo " selected='selected'"; } ?>><?php _e('DropDown Selection Field (JS)', 'dexs-pm'); ?></option>
								<option value="2"<?php if($option['list_style'] == "2"){ echo " selected='selected'"; } ?> disabled="disabled"><?php _e('Grouped DropDown Selection Field (JS)', 'dexs-pm'); ?></option>
							</select>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Use Attachment System', 'dexs-pm'); ?></th>
						<td><!--
							<label for="use_attachments_yes"><input type="radio" name="use_attachments" value="1" id="use_attachments_yes"<?php #if($option['use_attachments'] == "1"){ echo " checked='checked'"; } ?> disabled="disabled">
								<span><?php #_e('Yes', 'dexs-pm'); ?></span></label>&nbsp;&nbsp;&nbsp;&nbsp;
							<label for="use_attachments_non"><input type="radio" name="use_attachments" value="0" id="use_attachments_non"<?php #if($option['use_attachments'] == "0"){ echo " checked='checked'"; } ?> disabled="disabled"> 
								<span><?php #_e('No', 'dexs-pm'); ?></span></label>
							-->
							<p class="description">
								<?php _e("This feature comes with the next update (1.1.0).", "dexs-pm"); ?>
							</p>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Attachment Input Type', 'dexs-pm'); ?></th>
						<td><!--
							<select name="attachment_type" id="list_style" disabled="disabled">
								<option value="0"<?php #if($option['attachment_type'] == "0"){ echo " selected='selected'"; } ?>><?php #_e('One File Upload', 'dexs-pm'); ?></option>
								<option value="1"<?php #if($option['attachment_type'] == "1"){ echo " selected='selected'"; } ?>><?php #_e('Multiple File Upload (JS)', 'dexs-pm'); ?></option>
							</select>
							-->
							<p class="description">
								<?php _e("This feature comes with the next update (1.1.0).", "dexs-pm"); ?>
							</p>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Max. Attachment Size (in kB)', 'dexs-pm'); ?></th>
						<td>
							<input type="number" min="0" step="1" name="attachment_size" value="<?php echo $option['attachment_size']; ?>">
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Allowed Attachment formats', 'dexs-pm'); ?></th>
						<td>
							<input type="text" name="attachment_formats" value="<?php echo $option['attachment_formats']; ?>" style="width:250px;">
							<p class="description">
								<?php _e("Seperate the extensions with a comma.", "dexs-pm"); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset><br class="clear">
		
		<fieldset class="dexspm_fieldset">
			<legend class="dexspm_legend"><?php _e('Backend Settings', 'dexs-pm'); ?></legend>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e('Enable the Backend', 'dexs-pm'); ?></th>
						<td><!--
							<label for="use_backend_yes"><input type="radio" name="use_backend" value="1" id="use_backend_yes"<?php #if($option['use_backend'] == "1"){ echo " checked='checked'"; } ?> disabled="disabled"> 
								<span><?php #_e('Yes', 'dexs-pm'); ?></span></label>&nbsp;&nbsp;&nbsp;&nbsp;
							<label for="use_backend_non"><input type="radio" name="use_backend" value="0" id="use_backend_non"<?php #if($option['use_backend'] == "0"){ echo " checked='checked'"; } ?> disabled="disabled"> 
								<span><?php #_e('No', 'dexs-pm'); ?></span></label>
							-->
							<p class="description">
								<?php _e("This feature comes with the next update (1.1.0).", "dexs-pm"); ?>
							</p>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Display PM Interface (Toolbar)', 'dexs-pm'); ?></th>
						<td>
							<label for="backend_toolbar_yes"><input type="radio" name="backend_toolbar" value="1" id="backend_toolbar_yes"<?php if($option['backend_toolbar'] == "1"){ echo " checked='checked'"; } ?>> 
								<span><?php _e('Yes', 'dexs-pm'); ?></span></label>&nbsp;&nbsp;&nbsp;&nbsp;
							<label for="backend_toolbar_non"><input type="radio" name="backend_toolbar" value="0" id="backend_toolbar_non"<?php if($option['backend_toolbar'] == "0"){ echo " checked='checked'"; } ?>> 
								<span><?php _e('No', 'dexs-pm'); ?></span></label>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Backend Navigation Type', 'dexs-pm'); ?></th>
						<td>
							<select name="backend_navi" id="backend_navi">
								<option value="1"<?php if($option['backend_navi'] == "1"){ echo " selected='selected'"; } ?>><?php _e('Own Menu Item', 'dexs-pm'); ?></option>
								<option value="0"<?php if($option['backend_navi'] == "0"){ echo " selected='selected'"; } ?>><?php _e('Subordinate: Users', 'dexs-pm'); ?></option>
								<option value="2"<?php if($option['backend_navi'] == "2"){ echo " selected='selected'"; } ?> disabled="disabled"><?php _e('Subordinate: Dashboard', 'dexs-pm'); ?></option>
							</select>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Backend Style', 'dexs-pm'); ?></th>
						<td>
							<select name="backend_style" id="backend_style">
								<option value="0"<?php if($option['backend_style'] == "0"){ echo " selected='selected'"; } ?>><?php _e('Tab Style - One Menu', 'dexs-pm'); ?></option>
								<option value="1"<?php if($option['backend_style'] == "1"){ echo " selected='selected'"; } ?>><?php _e('Link Style - One Menu', 'dexs-pm'); ?></option>
								<option value="2"<?php if($option['backend_style'] == "2"){ echo " selected='selected'"; } ?>><?php _e('Menu Items Style', 'dexs-pm'); ?></option>
							</select>
							<p class="description"><?php _e('The "Menu Items Style" is not available, when you subordinate the Menu.', 'dexs-pm'); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset><br class="clear">
		
		<fieldset class="dexspm_fieldset">
			<legend class="dexspm_legend"><?php _e('Frontend Settings', 'dexs-pm'); ?></legend>
		
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e('Enable the Frontent', 'dexs-pm'); ?></th>
						<td><!--
							<label for="use_frontend_yes"><input type="radio" name="use_frontend" value="1" id="use_frontend_yes"<?php #if($option['use_frontend'] == "1"){ echo " checked='checked'"; } ?> disabled="disabled"> 
								<span><?php #_e('Yes', 'dexs-pm'); ?></span></label>&nbsp;&nbsp;&nbsp;&nbsp;
							<label for="use_frontend_non"><input type="radio" name="use_frontend" value="0" id="use_frontend_non"<?php #if($option['use_frontend'] == "0"){ echo " checked='checked'"; } ?> disabled="disabled"> 
								<span><?php #_e('No', 'dexs-pm'); ?></span></label>
							-->
							<p class="description">
								<?php _e("This feature comes with the next update (1.1.0).", "dexs-pm"); ?>
							</p>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e('Frontend Template', 'dexs-pm'); ?></th>
						<td>
							<select name="frontend_theme" id="frontend_theme">
								<?php $dexsPM->load_pm_templates(True); ?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Frontend Table Settings', 'dexs-pm'); ?></th>
						<td>
							<select name="frontend_style" id="frontend_style">
								<option value="0"<?php if($option['frontend_style'] == "0"){ echo " selected='selected'"; } ?>><?php _e('Hide the excerpt row', 'dexs-pm'); ?></option>
								<option value="1"<?php if($option['frontend_style'] == "1"){ echo " selected='selected'"; } ?>><?php _e('Show the excerpt row', 'dexs-pm'); ?></option>
							</select>
							<p class="description"><?php _e("Show excerpt: The sidebar should be disabled on the PM page!", "dexs-pm"); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<br class="clear">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<td colspan="2">
						<input type="hidden" value="settings" name="dexs_pm" id="auth">
						<input type="hidden" value="6" name="action" id="settings_action">
						<input type="hidden" value="0" name="tab" id="settings_tab">
						<input type="submit" value="<?php _e('Save Changes', 'dexs-pm'); ?>" name="save" id="save" class="button-primary">&nbsp;&nbsp;&nbsp;
						<input type="submit" value="<?php _e('Reset to Default', 'dexs-pm'); ?>" name="reset" id="reset" class="button-secondary">
					</td>
				</tr>
			</tbody>
		</table>
	</form>