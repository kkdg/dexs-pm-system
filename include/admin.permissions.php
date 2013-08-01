	
	<form action="" method="post">
		<h3 class="title"><?php _e('Role Permission Settings', 'dexs-pm'); ?></h3>
		
		<p class="description">
			<b><?php _e("IMPORTANT NOTICE (Applies only to Enable Images in PMs):", "dexs-pm"); ?></b><br>
			<?php _e("The subscriber and the contributor role can't upload files (images) by WordPress default!", "dexs-pm"); ?><br>
			<?php _e("If you are now admitting these roles attest to upload images, they will then also have access to the Media menu!", "dexs-pm"); ?><br><br>
			<b><?php _e("They have therefore access to ALL uploaded media files (except the pm attachments)!", "dexs-pm"); ?></b>
		</p>
		<br class="clear">
		
		<table class="wp-list-table widefat" style="width:auto;">
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
				
				if(isset($option[$role_key])){
					$role_set = $option[$role_key];
				} else {
					$role_set = $option[$dexsPM->load_pm_settings("settings", "standard_role")];				
				}
			?>
				<tr valign="top">
					<tr valign="top">
						<td style="vertical-align:middle;padding: 6px auto;">
							<b><?php echo translate_user_role(ucfirst($role['name'])); ?></b>
							<p class="description">(<?php echo $role_key; ?>)</p>
						</td>
						
						<td style='text-align:center;vertical-align:middle;'>							
							<input type="hidden" name="<?php echo $role_key; ?>[activate]" value="<?php echo ($role_key != "administrator") ? "0" : "1"; ?>">
							
							<label>
							<input type="checkbox" name="<?php echo $role_key; ?>[activate]" value="1" <?php if($role_set['activate']){ echo "checked='checked'"; } ?> <?php if($role_key == "administrator"){ echo "disabled='disabled'"; } ?>>
							<br><p class="description" style="color:#888"><?php _e("Included Users can still<br> use the PM system!", "dexs-pm"); ?></p>
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
									<input type="checkbox" name="<?php echo $role_key; ?>[enable][]" value="images" <?php if(in_array("images", $role_set['enable'])){ echo "checked='checked'"; } ?>>
									<?php _e("...Images in PMs", "dexs-pm"); ?>
								</label>
							</p>
							
							<p class="description" style="padding-left: 25px;">
								<label>
									<input type="checkbox" name="<?php echo $role_key; ?>[enable][]" value="attachments" <?php if(in_array("attachments", $role_set['enable'])){ echo "checked='checked'"; } ?>> 
									<?php _e("...Attachments in PMs", "dexs-pm"); ?>
								</label>
							</p>
						</td>
						
						<td style='text-align:center;vertical-align:middle;'>	
							<?php if($role_key == "administrator"){ ?>
								<input type="hidden" name="<?php echo $role_key; ?>[access][]" value="backend">
								<input type="hidden" name="<?php echo $role_key; ?>[access][]" value="frontend">
							<?php } else { ?>
								<input type="hidden" name="<?php echo $role_key; ?>[access][]" value="">
							<?php } ?>
							
							<p class="description" <?php if($role_key == "administrator"){ echo "style='color:#888'"; } ?>>
								<label>
									<input type="checkbox" name="<?php echo $role_key; ?>[access][]" value="backend" <?php if(in_array("backend", $role_set['access'])){ echo "checked='checked'"; } ?> <?php if($role_key == "administrator"){ echo "disabled='disabled'"; } ?>> 
									<?php _e("...Backend", "dexs-pm"); ?>
								</label>
							</p>
							
							<p class="description" <?php if($role_key == "administrator"){ echo "style='color:#888'"; } ?>>
								<label>
									<input type="checkbox" name="<?php echo $role_key; ?>[access][]" value="frontend" <?php if(in_array("frontend", $role_set['access'])){ echo "checked='checked'"; } ?> <?php if($role_key == "administrator"){ echo "disabled='disabled'"; } ?>> 
									<?php _e("...Frontend", "dexs-pm"); ?>
								</label>
							</p>
						</td>
					</tr>
				</tr>				
			<?php
				}
			?>
		</table>
		<br class='clear'>
		<input type="submit" value="<?php _e('Reset to Default', 'dexs-pm'); ?>" name="reset" id="reset" class="button-secondary">
		<br class='clear'><br class='clear'>
		
		<!-- CHECK DEFAULT ROLE -->
		<?php $get_role_sub = get_role("subscriber")->capabilities; ?>
		<?php $get_role_con = get_role("contributor")->capabilities; ?>
		
		<?php if(isset($get_role_sub["upload_files"]) && !in_array("images", $option["subscriber"]["enable"])){ ?>
			<h3><?php _e("Warning", "dexs-pm"); ?></h3>
			
			<table class="form-table">
				<tbody>			
					<tr valign="top">
						<th scope="row"><?php _e('Subscriber', 'dexs-pm'); ?></th>
						<td>
							<p class="description" style="margin-top:0;"><?php _e("The subscriber role can't upload images in PMs, but have still access to the Media Menu.", "dexs-pm"); ?></p>
							<input type="submit" value="<?php _e('Delete this access', 'dexs-pm'); ?>" name="delete[subscriber]" id="reset" class="button-secondary">
						</td>
					</tr>				
				</tbody>
			</table>
		<?php } ?>
		<?php if(isset($get_role_con["upload_files"]) && !in_array("images", $option["contributor"]["enable"])){ ?>
			<h3><?php _e("Warning", "dexs-pm"); ?></h3>
			
			<table class="form-table">
				<tbody>			
					<tr valign="top">
						<th scope="row"><?php _e('Contributor', 'dexs-pm'); ?></th>
						<td>
							<p class="description" style="margin-top:0;"><?php _e("The contributor role can't upload images in PMs, but have still access to the Media Menu.", "dexs-pm"); ?></p>
							<input type="submit" value="<?php _e('Delete this access', 'dexs-pm'); ?>" name="delete[contributor]" id="reset" class="button-secondary">
						</td>
					</tr>				
				</tbody>
			</table>			
		<?php } ?>
		<br class='clear'><br class='clear'>

		<h3><?php _e('Individual User Settings', 'dexs-pm'); ?></h3>
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
		
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<td colspan="2">
						<input type="hidden" value="settings" name="dexs_pm" id="auth">
						<input type="hidden" value="6" name="action" id="settings_action">
						<input type="hidden" value="1" name="tab" id="settings_tab">
						<input type="submit" value="<?php _e('Save Changes', 'dexs-pm'); ?>" name="save" id="save" class="button-primary">
					</td>
				</tr>
			</tbody>
		</table>
	</form>