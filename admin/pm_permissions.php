<!-------------------------------------------------
|	DEXS PM SYSTEM
|	PERMISSIONS SETTINGS
---------------------------------------------------
|	Coypright 2012 - 2013
|	SamBrishesWeb WordPress
|	http://www.sambrishes.net/wordpress
-------------------------------------------------->
<?php
	$sub = explode(',', $option_permissions['subscriber']);
	$con = explode(',', $option_permissions['contributor']);
	$aut = explode(',', $option_permissions['author']);
	$edi = explode(',', $option_permissions['editor']);
	$adm = explode(',', $option_permissions['administrator']);
?>

<?php if(isset($_GET['status'])){ ?>
	<?php if($_GET['status'] == "updated"){ ?>
		<div id="settings-error-settings_updated" class="updated settings-error">
			<p><b><?php _e('Settings saved.', 'dexs-pm'); ?></b></p>
		</div>
	<?php } ?>
<?php } ?>

<form action="" method="post">
	<h3 class="title"><?php _e('Role Permissions Settings', 'dexs-pm'); ?></h3>
	<table class="wp-list-table widefat" style="width:auto;">
		<thead>
			<tr valign="top">
				<th style="width:25%;" scope="col" class="manage-column"></th>
				<th style="text-align:center;width:15%;" scope="col" class="manage-column"><?php _e('Subscriber', 'dexs-pm'); ?></th>
				<th style="text-align:center;width:15%;" scope="col" class="manage-column"><?php _e('Contributor', 'dexs-pm'); ?></th>
				<th style="text-align:center;width:15%;" scope="col" class="manage-column"><?php _e('Author', 'dexs-pm'); ?></th>
				<th style="text-align:center;width:15%;" scope="col" class="manage-column"><?php _e('Editor', 'dexs-pm'); ?></th>
				<th style="text-align:center;width:15%;" scope="col" class="manage-column"><?php _e('Administrator', 'dexs-pm'); ?></th>
			</tr>
		</thead>
		
		<tfoot>
			<tr valign="top">
				<th style="width:25%;" scope="col" class="manage-column"></th>
				<th style="text-align:center;width:15%;" scope="col" class="manage-column"><?php _e('Subscriber', 'dexs-pm'); ?></th>
				<th style="text-align:center;width:15%;" scope="col" class="manage-column"><?php _e('Contributor', 'dexs-pm'); ?></th>
				<th style="text-align:center;width:15%;" scope="col" class="manage-column"><?php _e('Author', 'dexs-pm'); ?></th>
				<th style="text-align:center;width:15%;" scope="col" class="manage-column"><?php _e('Editor', 'dexs-pm'); ?></th>
				<th style="text-align:center;width:15%;" scope="col" class="manage-column"><?php _e('Administrator', 'dexs-pm'); ?></th>
			</tr>
		</tfoot>
			
		<tbody>
			<tr valign="top">
				<td style="vertical-align:middle;padding: 6px auto;"><b><?php _e('Deactivate PM System', 'dexs-pm'); ?></b><input type="text" style="visibility:hidden;width:1px;"></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="deactivate_subscriber" value="1"<?php if($sub[0]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="deactivate_contributor" value="1"<?php if($con[0]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="deactivate_author" value="1"<?php if($aut[0]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="deactivate_editor" value="1"<?php if($edi[0]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="deactivate_administrator" <?php if($adm[0]=="1"){ echo " checked='checked'"; } ?> disabled="disabled"></td>
			</tr>
		
			<tr valign="top">
				<td style="vertical-align:middle;padding: 6px auto;" valign="center"><b><?php _e('Max. Number of PMs', 'dexs-pm'); ?></b><input type="text" style="visibility:hidden;width:1px;"></td>
				<td style='text-align:center;vertical-align:middle;'><input type="number" step="1" min="-1" value="<?php echo $sub[1]; ?>" name="number_subscriber" class="small-text"></td>
				<td style='text-align:center;vertical-align:middle;'><input type="number" step="1" min="-1" value="<?php echo $con[1]; ?>" name="number_contributor" class="small-text"></td>
				<td style='text-align:center;vertical-align:middle;'><input type="number" step="1" min="-1" value="<?php echo $aut[1]; ?>" name="number_author" class="small-text"></td>
				<td style='text-align:center;vertical-align:middle;'><input type="number" step="1" min="-1" value="<?php echo $edi[1]; ?>" name="number_editor" class="small-text"></td>
				<td style='text-align:center;vertical-align:middle;'><input type="number" step="1" min="-1" value="<?php echo $adm[1]; ?>" name="number_administrator" class="small-text"></td>
			</tr>
			
			<tr valign="top">
				<td style="vertical-align:middle;padding: 6px auto;"><b><?php _e('Enable Images in PMs', 'dexs-pm'); ?></b><input type="text" style="visibility:hidden;width:1px;"></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="attachment_subscriber" <?php if($sub[2]=="1"){ echo " checked='checked'"; } ?> disabled="disabled"></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="attachment_contributor" <?php if($con[2]=="1"){ echo " checked='checked'"; } ?> disabled="disabled"></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="attachment_author" value="1"<?php if($aut[2]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="attachment_editor" value="1"<?php if($edi[2]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="attachment_administrator" value="1"<?php if($adm[2]=="1"){ echo " checked='checked'"; } ?>></td>
			</tr>
			
			<tr valign="top">
				<td style="vertical-align:middle;padding: 6px auto;"><b><?php _e('Enable PM System on Backend', 'dexs-pm'); ?></b><input type="text" style="visibility:hidden;width:1px;"></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="backend_subscriber" value="1"<?php if($sub[3]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="backend_contributor" value="1"<?php if($con[3]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="backend_author" value="1"<?php if($aut[3]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="backend_editor" value="1"<?php if($edi[3]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="backend_administrator" <?php if($adm[3]=="1"){ echo " checked='checked'"; } ?> disabled="disabled"></td>
			</tr>
			
			<tr valign="top">
				<td style="vertical-align:middle;padding: 6px auto;"><b><?php _e('Enable PM System on Frontend', 'dexs-pm'); ?></b><input type="text" style="visibility:hidden;width:1px;"></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="frontend_subscriber" value="1"<?php if($sub[4]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="frontend_contributor" value="1"<?php if($con[4]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="frontend_author" value="1"<?php if($aut[4]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="frontend_editor" value="1"<?php if($edi[4]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="frontend_administrator" <?php if($adm[4]=="1"){ echo " checked='checked'"; } ?> disabled="disabled"></td>
			</tr>

			<tr valign="top">
				<td style="vertical-align:middle;padding: 6px auto;"><?php _e('Default', 'dexs-pm'); ?> - <b><?php _e('Enable eMail Notification', 'dexs-pm'); ?></b><input type="text" style="visibility:hidden;width:1px;"></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="emailnote_subscriber" value="1"<?php if($sub[5]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="emailnote_contributor" value="1"<?php if($con[5]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="emailnote_author" value="1"<?php if($aut[5]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="emailnote_editor" value="1"<?php if($edi[5]=="1"){ echo " checked='checked'"; } ?>></td>
				<td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="emailnote_administrator" value="1"<?php if($adm[5]=="1"){ echo " checked='checked'"; } ?>></td>
			</tr>			
		</tbody>
	</table><br>
	<p class="description">
		<?php _e('<b>Number of PMs - Note:</b>', 'dexs-pm'); ?><br>
		<code>-1</code> <?php _e('Unlimited: All users of this role can unlimited write and send private messages!', 'dexs-pm'); ?><br>
		<code>0</code> <?php _e('Nothing: All users of this role cannot even write and send one private message!', 'dexs-pm'); ?><br>
	</p>
	<br class='clear'>

	<h3><?php _e('Individual User Settings', 'dexs-pm'); ?></h3>
	<table class="form-table">
		<tbody>			
			<tr valign="top">
				<th scope="row"><?php _e('Exclude Users by ID', 'dexs-pm'); ?></th>
				<td>
					<input type="text" name="exclude_users" value="<?php echo $option_permissions['exclude_users']; ?>" class="regular-text">
					<p class="description"><?php _e('Separate the user ids with a comma!', 'dexs-pm'); ?></p>
				</td>
			</tr>		
			
			<tr valign="top">
				<th scope="row"><?php _e('Include Users by ID', 'dexs-pm'); ?></th>
				<td>
					<input type="text" name="include_users" value="<?php echo $option_permissions['include_users']; ?>" class="regular-text">
					<p class="description"><?php _e('Separate the user ids with a comma!', 'dexs-pm'); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	
	<br class="clear">
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<td colspan="2">
					<input type="hidden" value="0" name="attachment_subscriber" id="attachment_subscriber">
					<input type="hidden" value="0" name="attachment_contributor" id="attachment_contributor">
					<input type="hidden" value="0" name="deactivate_administrator" id="deactivate_administrator">
					<input type="hidden" value="1" name="backend_administrator" id="backend_administrator">
					<input type="hidden" value="1" name="frontend_administrator" id="frontend_administrator">
					<input type="hidden" value="permissions" name="type" id="permissions">
					<input type="submit" value="<?php _e('Save Changes', 'dexs-pm'); ?>" name="save" id="save" class="button-primary">&nbsp;&nbsp;&nbsp;
					<input type="submit" value="<?php _e('Reset to Default', 'dexs-pm'); ?>" name="reset" id="reset" class="button-secondary">
				</td>
			</tr>
		</tbody>
	</table>
</form>