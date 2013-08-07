	<?php
		$read_pm = $dexsPMA->load_messages("", $_GET['pmid']);
			
		if(!empty($read_pm)){
			$_POST['pm_id'] = $_GET['pmid'];
		} else {
			$error = __("The message could not be found!", "dexs-pm");
		}
	?>
	<?php if(isset($error)){ ?>
		<div id="settings-error-settings_updated" class="error settings-error">
			<p><b><?php _e("Error", "dexs-pm"); ?></b> <?php echo $error; ?></p>
		</div>
	<?php } else { ?>	
		<form action="" method="post">
			<div id="poststuff">
				<table class="wp-list-table widefat pm_message_table" valign="center">
					<tbody>
						<tr>
							<th class="pm-table-th"><?php _e("Sender", "dexs-pm"); ?></th>
							<td class="pm-table-td">
								<?php if($read_pm->pm_sender == $this->cur_user){ ?>
									<?php _e("You", "dexs-pm"); echo " (".get_userdata($read_pm->pm_sender)->display_name.")"; ?>
								<?php } else { ?>
									<?php echo "<a href='".$dexsPMT->get_folder_url()."&folder=5&rec=".$read_pm->pm_sender."' title='".__("Send this user a PM", "dexs-pm")."'>".get_userdata($read_pm->pm_sender)->display_name."</a>"; ?>
								<?php } ?>
							</td>
						</tr>
						
						<tr>
							<th class="pm-table-th"><?php _e("Recipients", "dexs-pm"); ?></th>
							<td class="pm-table-td">
								<?php foreach($read_pm->pm_recipients AS $uid => $rec){ ?>
									<?php if($uid == $this->cur_user){ ?>
										<?php $out[] = _e("You", "dexs-pm"); echo " (".get_userdata($uid)->display_name.")"; ?>
									<?php } else { ?>
										<?php $out[] = "<a href='".$dexsPMT->get_folder_url()."&folder=5&rec=$uid' title='".__("Send this user a PM", "dexs-pm")."'>".get_userdata($uid)->display_name."</a>"; ?>
									<?php } ?>
								<?php } ?>
								<?php echo implode(", ", $out); ?>
							</td>
						</tr>
					</tbody>
				</table>

				<table class="wp-list-table widefat pm_message_table" valign="center">
					<tbody>
						<tr>
							<th class="pm-table-th"><?php _e("Subject", "dexs-pm"); ?></th>
							<td class="pm-table-td">
								<b><?php echo $read_pm->pm_subject; ?></b>
								<span class="pm-info-right" style="text-align:right;display:inline-block;float:right;">
									<i><?php _e("Priority", "dexs-pm"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;</i>
									<?php if($read_pm->pm_meta['priority'] == 0){ _e("Normal", "dexs-pm"); } else ?>
									<?php if($read_pm->pm_meta['priority'] == 1){ _e("Middle", "dexs-pm"); } else ?>
									<?php if($read_pm->pm_meta['priority'] == 2){ _e("High", "dexs-pm"); } ?>
								</span>
							</td>
						</tr>
						
						<tr>
							<th class="pm-table-th"><?php _e("Message Info", "dexs-pm"); ?></th>
							<td class="pm-table-td">
								<span class="pm-info" style="display:inline-block;">
									<i><?php _e("Attachment", "dexs-pm"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;</i>
									<?php if(!$read_pm->pm_meta['file']){ echo "<i>".__("No Attachments!", "dexs-pm")."</i>"; } else { ?>
										<a href="<?php echo $dexsPMT->pm_attachment_url(true)."/".$read_pm->pm_meta['file_meta']['file']; ?>" target="_blank"><?php echo $read_pm->pm_meta['file_meta']['name']." (".ceil(705574/1024); ?> kb)</a>
									<?php } ?>
								</span>
								
								<span class="pm-info-right" style="text-align:right;display:inline-block;float:right;">
									<i><?php _e("PM Date", "dexs-pm"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;</i> 
									<?php echo date_format(date_create($read_pm->pm_send), get_option('date_format')." ".get_option('time_format')); ?>
								</span>
							</td>
						</tr>
						
						<tr>
							<td class="pm-table-td message" colspan="2"><?php echo stripslashes(nl2br($read_pm->pm_message)); ?></td>
						</tr>
					</tbody>
				</table>
				<div id="postdivrich" class="postarea">
					<input type="hidden" name="dexs_pm" value="read_pm">
					<p style="float:left;">
						<input type="submit" name="send_action[2]" class="button button-primary button-large action" value="<?php _e("Answer", "dexs-pm"); ?>">&nbsp;&nbsp;
						<input type="submit" name="send_action[1]" class="button button-secondary button-large action" value="<?php _e("Forward", "dexs-pm"); ?>">
					</p>
				
					<p style="float:right;">
					
						<?php if($this->pm_folder == "4" || $this->pm_folder == "2"){ ?>
							<input type="submit" name="action[5]" class="button button-secondary button-large action" value="<?php _e("Restore", "dexs-pm"); ?>">&nbsp;&nbsp;
						<?php } ?>
						
						<?php if($this->pm_folder == "0" || $this->pm_folder == "1"){ ?>
							<input type="submit" name="action[4]" class="button button-secondary button-large action" value="<?php _e("Archive", "dexs-pm"); ?>">&nbsp;&nbsp;
						<?php } ?>
						
						<?php if($this->pm_folder != "2"){ ?>
							<input type="submit" name="action[2]" class="button button-secondary button-large action" value="<?php _e("Delete", "dexs-pm"); ?>">&nbsp;&nbsp;
						<?php } ?>
						
						<?php if($this->pm_folder == "2"){ ?>
							<input type="submit" name="action[3]" class="button button-secondary button-large action" value="<?php _e("Delete Permanently", "dexs-pm"); ?>">&nbsp;&nbsp;
						<?php } ?>
						
					</p>
				</div>	
			</div>
		<form>
	<?php } ?>