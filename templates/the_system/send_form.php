	
	<link rel='stylesheet' type='text/css' href='<?php echo plugins_url('include/css/admin_form.css', dirname(dirname(__FILE__))); ?>'>
	
	<?php if($dexsPMA->warn){ ?>
		<div id="settings-error-settings_updated" class="error settings-error">
			<p><b><?php _e("Error", "dexs-pm"); ?></b> <?php echo $dexsPMA->warn_message; ?></p>
		</div>
	<?php } else { ?>
		<form method="post" action="" enctype="multipart/form-data">	
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div id="titlediv">
						<div id="titlewrap">
						
						<script type="text/javascript">
							document.getElementById("recipients").style.width = "1000px";
							alert("test");
						</script>

						<?php if($dexsPMA->rec_list_style){ 	# AUTOCOMPLETE INPUT FIELD ?>
							<label class="" id="title-prompt-text2" for="recipients"><?php _e("Enter the Recipients here", "dexs-pm"); ?></label>
							<input type="text" name="recipients" size="30" value="" id="recipients" autocomplete="off" />
							<div id="facebook-auto">					  
								<div class="default">&nbsp;&nbsp;<?php _e('Type here the names of each recipients.', 'dexs-pm'); ?></div>
								<ul class="feed">
									<?php if(!empty($dexsPMA->pm_rec)){
										foreach($dexsPMA->pm_rec AS $id => $rec){
											echo "<li value='".$id."'>".$rec."</li><a></a>";
										}
									} ?>
								</ul>
							</div>							
						<?php } else { 		# DROPDOWN SELECTION FIELD ?>
							
							<select id="recipient_change" onchange="get_recipient(this.value)" style="margin-bottom: 5px;display:inline-block;">
								<option id="nothing" name="nothing" value="nothing"><?php _e('Select a Recipient', 'dexs-pm'); ?></option>
								<optgroup id="addthisnow" label="------------"></optgroup>
									
									<?php foreach($dexsPMA->pm_users AS $user){ ?>
										<option id="<?php echo $user->ID ?>" value="<?php echo $user->ID.",".$user->display_name; ?>" <?php if(array_key_exists($user->ID, $dexsPMA->pm_rec)){ echo "disabled='disabled'"; } ?>>
											<?php echo $user->display_name; ?></option>
									<?php } ?>									
							</select>								
							<label class="" id="title-prompt-text2" for="recipients" style="display:inline-block;float:right;"><?php _e("Select the Recipients here", "dexs-pm"); ?></label>

							<ul id="the_fields" class="holderdrop">
								<li class="bit-box" style="visibility:hidden;width:0;padding-left:0;padding-right:0;margin-left:0;margin-right:0;">&nbsp;</li>
								<?php if(!empty($dexsPMA->pm_rec)){ ?>
									<?php foreach($dexsPMA->pm_rec AS $id => $name){ ?>
										<li class="bit-box" id="user_<?php echo $id; ?>">
											<?php echo $name; ?><a class="closebutton" onClick="remove_recipient('<?php echo $id."_-_".$name; ?>')"></a>
										</li>
									<?php } ?>
								<?php } ?>
							</ul>
							<span id="fields">									
								<?php if(!empty($pm_rec_users)){ ?>
									<?php foreach($dexsPMA->pm_rec AS $id => $name){ ?>
										<input type="hidden" name="recipients[]" value="<?php echo $id; ?>" id="theuser_<?php echo $id;?>">
									<?php } ?>
								<?php } ?>				
							</span>
						<?php } ?>
						</div>
						
						<br class="clear">
						<div id="titlewrap2">
							<label class="" id="title-prompt-text1" class="right_prompt" for="title"><?php _e("Enter the Subject here", "dexs-pm"); ?></label>
							<input type="text" name="subject" class="pm_sender_subject" size="30" value="<?php echo $dexsPMA->pm_sub; ?>" id="title" autocomplete="off" />
						</div>
					</div>
					
					<div id="postdivrich" class="postarea">						
						<br class="clear">
						<?php wp_editor($dexsPMA->pm_mes, "pm_message", $dexsPMA->editor_settings); ?>
					</div>
				</div>
				
				<div class="pm_sender_bottom">
					<div class="sender_attachments">
						<div class="misc-pub-section">
							<div id="post-formats-select">
								<input type="file" style="width: 240px;" name="attachment">
							</div>
						</div>
						
						<?php if($dexsPMA->user_attachments){ ?>
							<div class="misc-pub-section"><i>
								(<?php _e("Max. Size", "dexs-pm"); ?>: <?php echo $dexsPM->load_pm_settings("settings", "attachment_size"); ?>kB) - 
								(<?php _e("Allowed Formats", "dexs-pm"); ?>:
								<?php echo $dexsPM->load_pm_settings("settings", "attachment_formats"); ?>)
							</i></div>		
						<?php } ?>								
					</div>
					
					
					<table>
						<tbody>
							<tr>
								<td>
									<div class="misc-pub-section">
										<label for="post_status"><?php _e("Priority", "dexs-pm"); ?>:</label>
										
										<span id="post-status-display">
											<select name="priority">
												<option value="0"><?php _e("Normal", "dexs-pm"); ?></option>
												<option value="1"><?php _e("Middle", "dexs-pm"); ?></option>
												<option value="2"><?php _e("High", "dexs-pm"); ?></option>
											</select>
										</span>
									</div>										
								</td>
								
								<td style="text-align:right;">
									<input type="hidden" name="dexs_pm" value="send_pm">
									<input type="hidden" name="send_action" value="0">
									<input type="submit" name="publish" id="publish" class="button button-primary button-large" value="<?php _e("Send Message", "dexs-pm"); ?>">
								</td>
							</tr>
						</tbody>
					</table>
				</div>				
			</div>
			<br class="clear" />
		</div>
		</form>
	<?php } ?>
