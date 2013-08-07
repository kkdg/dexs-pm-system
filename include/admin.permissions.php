
	<form action="" method="post">
		<fieldset class="dexspm_fieldset">
			<legend class="dexspm_legend"><?php _e('Role Permission Settings', 'dexs-pm'); ?></legend>
			<br class="clear">
			
			<table class="wp-list-table widefat" style="width:auto;margin-left: 10px;">
				<thead>
					<tr valign="top">
						<th style="width:20%;" scope="col" class="manage-column"><?php _e('(Custom) Role', 'dexs-pm'); ?></th>
						<th style="text-align:center;width:20%;" scope="col" class="manage-column"><?php _e('Activate PM System', 'dexs-pm'); ?></th>
						<th style="text-align:center;width:20%;" scope="col" class="manage-column"><?php _e('Max Number of PMs', 'dexs-pm'); ?></th>
						<th style="text-align:center;width:20%;" scope="col" class="manage-column"><?php _e('Enable ...', 'dexs-pm'); ?></th>
						<th style="text-align:center;width:20%;" scope="col" class="manage-column"><?php _e('Enable Access from...', 'dexs-pm'); ?></th>
					</tr>
				</thead>
				
				<tfoot>
					<tr valign="top">
						<th style="width:20%;" scope="col" class="manage-column"><?php _e('(Custom) Role', 'dexs-pm'); ?></th>
						<th style="text-align:center;width:20%;" scope="col" class="manage-column"><?php _e('Activate PM System', 'dexs-pm'); ?></th>
						<th style="text-align:center;width:20%;" scope="col" class="manage-column"><?php _e('Max Number of PMs', 'dexs-pm'); ?></th>
						<th style="text-align:center;width:20%;" scope="col" class="manage-column"><?php _e('Enable ...', 'dexs-pm'); ?></th>
						<th style="text-align:center;width:20%;" scope="col" class="manage-column"><?php _e('Enable Access from...', 'dexs-pm'); ?></th>
					</tr>
				</tfoot>
					
				<tbody>
				<?php
					foreach($wp_roles->roles AS $role_key => $role){
						$c = array("img" => "");
						
						if(isset($option[$role_key])){
							$role_set = $option[$role_key];
						} else {
							$role_set = $option[$dexsPM->load_pm_settings("settings", "standard_role")];				
						}
						
						($role_key == "administrator")? $disabled = "disabled='disabled'" : $disabled = "";
						(in_array("images", $role_set['enable']))? $c["img"] = "checked='checked'" : $c["img"] = "";
						
						if($dexsPM->load_pm_settings("settings", "use_attachments")){
							(in_array("attachments", $role_set['enable']))? $c["att"] = "checked='checked'" : $c["att"] = "";
						} else {
							$c["att"] = "disabled='disabled'";
						}
						
						if($dexsPM->load_pm_settings("settings", "use_backend")){
							(in_array("backend", $role_set['access']))? $c["back"] = "checked='checked'" : $c["back"] = "";
						} else {
							$c["back"] = "disabled='disabled'";
						}
						
						if($dexsPM->load_pm_settings("settings", "use_frontend")){
							(in_array("frontend", $role_set['access']))? $c["fron"] = "checked='checked'" : $c["fron"] = "";
						} else {
							$c["fron"] = "disabled='disabled'";
						}
				?>
					<tr valign="top">
						<td style="vertical-align:middle;padding: 6px auto;">
							<b><?php echo translate_user_role(ucfirst($role['name'])); ?></b>
							<p class="description">(<?php echo $role_key; ?>)</p>
						</td>
						
						<td style='text-align:center;vertical-align:middle;'>							
							<label>
								<input type="checkbox" name="<?php echo $role_key; ?>[activate]" value="1" <?php if($role_set['activate']){ echo "checked='checked'"; } ?> <?php echo $disabled; ?>>
								<p class="description" style="color:#888"><?php _e("Included Users can still<br> use the PM system!", "dexs-pm"); ?></p>
							</label>					
						</td>
						
						<td style='text-align:center;vertical-align:middle;'>							
							<input type="number" name="<?php echo $role_key; ?>[max_number]" min="-1" step="1" value="<?php echo $role_set['max_number']; ?>" style="width:60px;"><br>
							<p class="description" style="color:#888"><?php _e("(-1 = Unlimited)", "dexs-pm"); ?></p>							
						</td>
						
						<td style='text-align:left;vertical-align:middle;'>
							<input type="hidden" name="<?php echo $role_key; ?>[enable][]" value="">
								
							<p class="description" style="padding-left: 25px;">
								<label>
									<input type="checkbox" name="<?php echo $role_key; ?>[enable][]" value="images" <?php echo $c["img"]; ?>>
									<?php _e("...Images in PMs", "dexs-pm"); ?>
								</label>
							</p>
							
							<p class="description" style="padding-left: 25px;<?php if($c["att"] == "disabled='disabled'"){ echo "color:#888;"; } ?>">
								<label>
									<input type="checkbox" name="<?php echo $role_key; ?>[enable][]" value="attachments" <?php echo $c["att"]; ?>> 
									<?php _e("...Attachments in PMs", "dexs-pm"); ?>
								</label>
							</p>
						</td>
						
						<td style='text-align:center;vertical-align:middle;'>
							<p class="description" <?php if($role_key == "administrator" || $c["back"] == "disabled='disabled'"){ echo "style='color:#888'"; } ?>>
								<label>
									<input type="checkbox" name="<?php echo $role_key; ?>[access][]" value="backend" <?php echo $c["back"] ?> <?php echo $disabled ?>> 
									<?php _e("...Backend", "dexs-pm"); ?>
								</label>
							</p>
							
							<p class="description" <?php if($role_key == "administrator" || $c["fron"] == "disabled='disabled'"){ echo "style='color:#888'"; } ?>>
								<label>
									<input type="checkbox" name="<?php echo $role_key; ?>[access][]" value="frontend" <?php echo $c["fron"] ?> <?php echo $disabled; ?>> 
									<?php _e("...Frontend", "dexs-pm"); ?>
								</label>
							</p>
						</td>
					</tr>
				<?php
					}
				?>
			</table>
			
			<table class="form-table"><tbody><tr><th scope="row" style="vertical-align:middle;width:110px;">
				<input type="submit" value="<?php _e('Reset to Default', 'dexs-pm'); ?>" name="reset" id="reset" class="button-secondary">
			</th>
			
			<td>
				<p class="description">
					<?php _e("If you enable the \"Enable images in PMs\" option for subscribers and contributors, will these roles have also access to the Media Menu.", "dexs-pm"); ?><br>
					<b><?php _e("They have therefore access to ALL uploaded media files (except the pm attachments)!", "dexs-pm"); ?></b>
				</p>
			</td></tr></tbody></table>
		</fieldset>
		<br class='clear'>
		
		<!-- CHECK DEFAULT ROLE -->
		<?php $get_role_sub = get_role("subscriber")->capabilities; ?>
		<?php $get_role_con = get_role("contributor")->capabilities; ?>
		
		<?php if((isset($get_role_sub["upload_files"]) && !in_array("images", $option["subscriber"]["enable"])) || 
			(isset($get_role_con["upload_files"]) && !in_array("images", $option["contributor"]["enable"]))){ ?>
			
			<fieldset class="dexspm_fieldset error" style="background-color:transparent;">
				<legend class="dexspm_legend error"><?php _e("Warning", "dexs-pm"); ?></legend>
				
				<?php if(isset($get_role_sub["upload_files"]) && !in_array("images", $option["subscriber"]["enable"])){ ?>
				<table class="form-table">
					<tbody>			
						<tr valign="top">
							<th scope="row"><?php _e('Subscriber', 'dexs-pm'); ?></th>
							<td>
								<input type="submit" value="<?php _e('Delete this access', 'dexs-pm'); ?>" name="delete[subscriber]" id="reset" class="button-secondary">
								<p class="description" style="margin-top:0;"><?php _e("The subscriber role can't upload images in PMs, but have still access to the Media Menu.", "dexs-pm"); ?></p>
							</td>
						</tr>				
					</tbody>
				</table>
				<?php } ?>
				
				<?php if(isset($get_role_con["upload_files"]) && !in_array("images", $option["contributor"]["enable"])){ ?>
				<table class="form-table">
					<tbody>			
						<tr valign="top">
							<th scope="row"><?php _e('Contributor', 'dexs-pm'); ?></th>
							<td>
								<input type="submit" value="<?php _e('Delete this access', 'dexs-pm'); ?>" name="delete[contributor]" id="reset" class="button-secondary">
								<p class="description" style="margin-top:0;"><?php _e("The contributor role can't upload images in PMs, but have still access to the Media Menu.", "dexs-pm"); ?></p>
							</td>
						</tr>				
					</tbody>
				</table>
				<?php } ?>
			</fieldset>
			<br class="clear">
		<?php } ?>

		<fieldset class="dexspm_fieldset">
			<legend class="dexspm_legend"><?php _e('Individual User Settings', 'dexs-pm'); ?></legend>
			
			<table class="form-table">
				<tbody>			
					<tr valign="top">
						<th scope="row"><?php _e('Exclude Users by ID', 'dexs-pm'); ?></th>
						<td>
							<input type="text" name="exclude_users" value="<?php echo $option['exclude_users']; ?>" class="regular-text">
							<p class="description"><?php _e('Separate the user ids with a comma!', 'dexs-pm'); ?></p>
						</td>
					</tr>		
					
					<tr valign="top">
						<th scope="row"><?php _e('Include Users by ID', 'dexs-pm'); ?></th>
						<td>
							<input type="text" name="include_users" value="<?php echo $option['include_users']; ?>" class="regular-text">
							<p class="description"><?php _e('Separate the user ids with a comma!', 'dexs-pm'); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<td colspan="2">
						<input type='hidden' name='administrator[activate]' value='1'>
						<input type='hidden' name='administrator[access][]' value='backend'>
						<input type='hidden' name='administrator[access][]' value='frontend'>
							
						<input type="hidden" value="settings" name="dexs_pm" id="auth">
						<input type="hidden" value="6" name="action" id="settings_action">
						<input type="hidden" value="1" name="tab" id="settings_tab">
						<input type="submit" value="<?php _e('Save Changes', 'dexs-pm'); ?>" name="save" id="save" class="button-primary">
					</td>
				</tr>
			</tbody>
		</table>
	</form>