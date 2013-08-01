<?php
	/*
	 *	DISPLAY BACKEN PM TABLES
	 *	
	 *	@param string 		{0 = inbox || 1 = outbox || 2 = trash || 3 = archive || 4 = settings || 5 = send || 6 = read}
	 *	@since 0.9.0 BETA
	 *	@update 1.0.0		rename
	 */
	function load_pm_table($folder){
		global $wpdb, $current_user, $dexsPM, $dexsPMA, $dexsPMT;
		
		$read = false;
		$user = $current_user->ID;									# GET CURRENT USER
		$user_settings = $dexsPM->user_settings("load", $user);		# GET USER SETTINGS
		
		if($dexsPM->load_pm_settings("settings", "backend_navi")){
			$spage = "admin.php?page=pm";
			$use_page = "admin.php?page=pm&folder=$folder";
		} else {
			$spage = "users.php?page=pm";
			$use_page = "users.php?page=pm&folder=$folder";		
		}
		
		if(isset($_GET['read'])){
			$read = true;
		}
		
		if(!$read){
			if($folder < 5){
				$messages = $dexsPMA->load_messages($folder);			# LOAD MESSAGES
				$counter = $dexsPMA->count_messages($folder, false);	# COUNT MESSAGES
				if(isset($_GET['row'])){
					$offset = $_GET['row']++;
				} else {
					$offset = 1;
				}
			}
			
			switch($folder){
				case "0":		# INBOX
					$name = __("Inbox", "dexs-pm");
					$max_view = $user_settings['inbox_num'];
					break;
					
				case "1":		# OUTBOX
					$name = __("Outbox", "dexs-pm");
					$max_view = $user_settings['outbox_num'];
					break;
					
				case "2":		# TRASH
					$name = __("Trash", "dexs-pm");
					$max_view = $user_settings['trash_num'];
					break;
					
				case "4":		# ARCHIVE
					$name = __("Archive", "dexs-pm");
					$max_view = $user_settings['archive_num'];
					break;
				
				case "5":
					$dexsPMA->send_pm_check();
					break;
			}			
		} else {
			$read_pm = $dexsPMA->load_messages("", $_GET['pmid']);
						
			if(!empty($read_pm)){
				$read_pm = $read_pm;
				$read_meta = maybe_unserialize($read_pm->pm_meta);
				$read_rec_meta = maybe_unserialize($read_pm->pm_recipients);
				
				$dexsPMA->pm_action("1", $_GET['pmid']);
				$_POST['pm_id'] = $_GET['pmid'];
			} else {
				$error = __("The message could not be found!", "dexs-pm");
			}
			
			if(file_exists(wp_upload_dir()['basedir']."/dexspm_files")){
				$upload_url = wp_upload_dir()['baseurl']."/dexspm_files";
			} else {
				$upload_url = wp_upload_dir()['baseurl'];
			}
		}
		
		/*
		 *	OUTPUT || CONTENT || TABLES || AN SO MUCH STUFF
		 */
	?>
		<form method="post" action="" enctype="multipart/form-data">	
	
		<!-- SEKTION 1: HEADER -->
		<?php if($folder < "5" && !$read){ ?>
			<div class="tablenav top">
				<div class="alignleft actions">
					<input type="hidden" name="dexs_pm" value="folder">
					<select name="action[]">
						<option value="-1"><?php _e('Bulk Actions', 'dexs-pm'); ?></option>
						
						<?php echo $dexsPMT->get_bulk_actions($folder, true); ?>
					</select>
					<input type="submit" class="button action" name="action_0" value="<?php _e('Apply', 'dexs-pm'); ?>">
				</div>
				
				<div class="tablenav-pages">
					<span class="displaying-num">
						<?php if($counter == 1){
							_e('One Message', 'dexs-pm');
						} else {
							echo $counter." ".__('Messages', 'dexs-pm');						
						} ?>
					</span>
					
					<span class="pagination-links">
						<a class="first-page <?php if($offset == 1){ echo "disabled"; } ?>" title="<?php _e("Go to the First Page", "dexs-pm");?>" <?php if($offset >= ceil($counter/$max_view)){ echo "href='$use_page&row=1'"; } ?> style="cursor:pointer;">&laquo;</a>
						<a class="prev-page <?php if($offset == 1){ echo "disabled"; } ?>" title="<?php _e("Go to the Previous Page", "dexs-pm");?>" <?php if($offset >= ceil($counter/$max_view)){ echo "href='$use_page&row=".($offset-1)."'"; } ?> style="cursor:pointer;">&lsaquo;</a>
							<span class="paging-input">
								<?php echo $offset; ?> of <?php echo (ceil($counter/$max_view) == 0)? "1" : ceil($counter/$max_view); ?>
							</span>
						<a class="next-page <?php if($offset >= ceil($counter/$max_view)){ echo "disabled"; } ?>" title="<?php _e("Go to the Next Page", "dexs-pm");?>" <?php if($offset < ceil($counter/$max_view)){ echo "href='$use_page&row=".($offset+1)."'"; } ?> style="cursor:pointer;">&rsaquo;</a>
						<a class="last-page <?php if($offset >= ceil($counter/$max_view)){ echo "disabled"; } ?>" title="<?php _e("Go to the Last Page", "dexs-pm");?>" <?php if($offset < ceil($counter/$max_view)){ echo "href='$use_page&row=".ceil($counter/$max_view)."'"; } ?> style="cursor:pointer;">&raquo;</a>
					</span>
				</div>
			</div>
		<?php } ?>

		
		<!-- SEKTION 2: TABLE -->
		<?php if($folder < "5" && !$read){ ?>
			<table class="wp-list-table widefat inbox">
				<thead>
					<tr>
						<th style="width:2%;" scope="col" class="manage-column check-column"><input type="checkbox"></th>
						
						<?php $dexsPMT->get_table_header($folder, true, true); ?>
					</tr>
				</thead>
				
				<tbody>
					<?php if(count($messages) == 0){ ?>
						<td colspan="5">
							<br class="clear"><center>
							<p class="description" style="text-align:center;"><?php _e("This folder contains no private messages.", "dexs-pm"); ?></p>
							</center><br class="clear">
						</td>
					<?php } ?>
					<?php foreach($messages AS $message){ ?>
						<?php 
							$meta = maybe_unserialize($message->pm_meta);
							$meta_rec = maybe_unserialize($message->pm_recipients);
							if($meta['priority'] == 1){
								$priority = "#F5F5F5";
							} elseif($meta['priority'] == 2){
								$priority = "#E8E8E8";
							} else {
								$priority = "#FCFCFC";
							}
						?>
						<tr id="pm-<?php echo $message->pm_id; ?> " class="post-<?php echo $message->pm_id; ?> type-post format-standard alternate" style="background-color:<?php if($folder == 0){ echo $priority; } ?>;">
							<th scope="row" class="check-column">
								<input type="checkbox" id="pm-select-<?php echo $message->pm_id; ?>" name="pm_id[]" value="<?php echo $message->pm_id; ?>">
							</th>
							
							<td class="subject">
								<?php if(!$dexsPMA->is_pm_read($meta_rec, $user)){ ?><strong><?php } ?>
									<a class="row_title" href="<?php echo $spage; ?>&folder=<?php echo $folder; ?>&read=1&pmid=<?php echo $message->pm_id; ?>" title="<?php _e("Read", "dexs_pm"); ?> “<?php echo $message->pm_subject; ?>”"><?php echo $message->pm_subject; ?></a>
								<?php if(!$dexsPMA->is_pm_read($meta_rec, $user)){ ?></strong><?php } ?>
							
								<div class="row-actions">
									<span class="edit">
										<a href="<?php echo $spage; ?>&folder=<?php echo $folder; ?>&read=1&pmid=<?php echo $message->pm_id; ?>&dpm=folder" title="<?php _e("Read this message", "dexs-pm") ?>"><?php _e("Read", "dexs-pm") ?></a>&nbsp;|
									</span>
									
									<?php if($folder != "4"){ ?>
									<span class="edit">
										<a href="<?php echo $use_page; ?>&action=4&pmid=<?php echo $message->pm_id; ?>&dpm=folder" title="<?php _e("Archive this message", "dexs-pm") ?>"><?php _e("Archive", "dexs-pm") ?></a>&nbsp;|
									</span>
									<?php } ?>
									
									<?php if($folder != "0" && $folder != "1"){ ?>
									<span class="edit">
										<a href="<?php echo $use_page; ?>&action=5&pmid=<?php echo $message->pm_id; ?>&dpm=folder" title="<?php _e("Restore this message", "dexs-pm") ?>"><?php _e("Restore", "dexs-pm") ?></a>&nbsp;|
									</span>
									<?php } ?>
									
									<?php if($folder != "2"){ ?>
									<span class="trash">
										<a href="<?php echo $use_page; ?>&action=2&pmid=<?php echo $message->pm_id; ?>&dpm=folder" title="<?php _e("Delete this message", "dexs-pm") ?>"><?php _e("Delete", "dexs-pm") ?></a>
									</span>
									<?php } ?>
									
									<?php if($folder == "2"){ ?>
									<span class="trash">
										<a href="<?php echo $use_page; ?>&action=3&pmid=<?php echo $message->pm_id; ?>&dpm=folder" title="<?php _e("Delete this message permanently", "dexs-pm") ?>"><?php _e("Delete Permanently", "dexs-pm") ?></a>
									</span>
									<?php } ?>
								</div>
							</td>
							
							<td class="users"><?php $dexsPMT->load_user_structur($folder, $message); ?></td>						
							
							<td class="excerpt">
								<?php echo strip_tags(substr($message->pm_message, 0, 150)); ?>
								<div class="row-actions">
									<span class="edit">
										<a href="<?php echo $spage; ?>&folder=5&send_action=1&pmid=<?php echo $message->pm_id; ?>&dpm=folder" title="<?php _e("Forward this message", "dexs-pm") ?>"><?php _e("Forward", "dexs-pm") ?></a>&nbsp;|
									</span>
									
									<span class="edit">
										<a href="<?php echo $spage; ?>&folder=5&send_action=2&pmid=<?php echo $message->pm_id; ?>&dpm=folder" title="<?php _e("Answer to this message", "dexs-pm") ?>"><?php _e("Answer", "dexs-pm") ?></a>
									</span>
								</div>							
							</td>
							
							<td class="send_date" style="text-align:right;"><?php echo date_format(date_create($message->pm_send), get_option('date_format')." ".get_option('time_format')); ?></td>
						</tr>
					<?php } ?>
				</tbody>
				
				<tfoot>
					<tr>
						<th style="width:2%;" scope="col" class="manage-column check-column"><input type="checkbox"></th>
						
						<?php $dexsPMT->get_table_header($folder, true, true); ?>
					</tr>
				</tfoot>
			</table>
		<?php } ?>
		
		
		<!-- SEKTION 2: SEND PM -->
		<?php if($folder == "5" && !$read){ ?>
			<?php if($dexsPMA->warn){ ?>
				<div id="settings-error-settings_updated" class="error settings-error">
					<p><b><?php _e("Error", "dexs-pm"); ?></b> <?php echo $dexsPMA->warn_message; ?></p>
				</div>
			<?php } else { ?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div id="titlediv">
							<div id="titlewrap">
							
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
								<input type="text" name="subject" size="30" value="<?php echo $dexsPMA->pm_sub; ?>" id="title" autocomplete="off" />
							</div>
						</div>
						
						<div id="postdivrich" class="postarea">
							<?php wp_editor($dexsPMA->pm_mes, "pm_message", $dexsPMA->editor_settings); ?>
						</div>
					</div>
					
					<div id="postbox-container-1" class="postbox-container">
						<div id="side-sortables" class="meta-box-sortables ui-sortable">
							<div id="submitdiv" class="postbox" style="cursor:poiner;">
								<h3 class="hndle" style="cursor:poiner;"><span><?php _e("Send Message", "dexs-pm"); ?></span></h3>
								
								<div class="inside">
									<div class="submitbox" id="submitpost">
										<div id="minor-publishing">
											<div id="misc-publishing-actions">
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
											</div>
											<div class="clear"></div>
										</div>

										<div id="major-publishing-actions">
											<div id="delete-action">
												<a class="submitdelete deletion" href=""><?php _e("Discard Message", "dexs-pm"); ?></a>
											</div>

											<div id="publishing-action">
												<span class="spinner" style="display: none;"></span>
												<input type="hidden" name="dexs_pm" value="send_pm">
												<input type="hidden" name="send_action" value="0">
												<input type="submit" name="publish" id="publish" class="button button-primary button-large" value="<?php _e("Send Message", "dexs-pm"); ?>">
											</div>
											
											<div class="clear"></div>
										</div>
									</div>
								</div>
							</div>
							
							<?php if($dexsPMA->user_attachments){ ?>
								<div id="formatdiv" class="postbox ">
									<h3 class="hndle"><span><?php _e("Attachments", "dexs-pm"); ?></span></h3>
									
									<div class="inside">
										<div class="misc-pub-section">
											<div id="post-formats-select">
												<input type="file" style="width: 228px;" name="attachment">
											</div>
										</div>
										
										<div class="misc-pub-section" style="border-bottom:none;">
											<p class="description">
												<?php _e("Max. Size", "dexs-pm"); ?>: <?php echo $dexsPM->load_pm_settings("settings", "attachment_size"); ?>kB<br>
												<?php _e("Allowed File Extensions", "dexs-pm"); ?>: <br>
												<?php echo $dexsPM->load_pm_settings("settings", "attachment_formats"); ?>
											</p>
										</div>										
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					
					<div id="postbox-container-2" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">
						</div>
						<div id="advanced-sortables" class="meta-box-sortables ui-sortable"></div>
					</div>
				</div>
				<br class="clear" />
			</div>
		<?php } ?>
		<?php } ?>

		
		<!-- SEKTION 2: USER SETTINGS -->
		<?php if($folder == "6" && !$read){ ?>
			<?php $set = $dexsPM->user_settings("load"); ?>
			<h3 class="title"><?php _e("Folder Settings", "dexs-pm"); ?></h3>
			<table class="form-table">
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
				</tbody>
			</table>
			<br class="clear">
			
			<table class="form-table">
				<tbody>
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
			<br class="clear">
			
			<?php $stats = $dexsPMA->count_messages("6"); ?>
			<h3 class="title"><?php _e("Statistics", "dexs-pm"); ?></h3>			
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e("Private Messages", "dexs-pm"); ?></th>
						<td>
							<code><?php echo $stats["all"]; ?></code>
							/ <code><?php if($dexsPM->check_permissions("max_messages") == "-1"){ echo "&infin;"; } else { echo $dexsPM->check_permissions("max_messages"); } ?></code>
							<p class="description">(<?php _e("Number", "dexs-pm"); ?> / <?php _e("Maximum", "dexs-pm"); ?>)</p>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e("Private Messages by folder", "dexs-pm"); ?></th>
						<td>
							<i style="color:#666;"><?php _e("Inbox", "dexs-pm"); ?>:</i> <code><?php echo $stats["inbox"]; ?></code>&nbsp;&nbsp;
							<i style="color:#666;"><?php _e("Outbox", "dexs-pm"); ?>:</i> <code><?php echo $stats["outbox"]; ?></code>&nbsp;&nbsp;
							<i style="color:#666;"><?php _e("Trash", "dexs-pm"); ?>:</i> <code><?php echo $stats["trash"]; ?></code>&nbsp;&nbsp;
							<i style="color:#666;"><?php _e("Archive", "dexs-pm"); ?>:</i> <code><?php echo $stats["archive"]; ?></code>
						</td>
					</tr>
				</tbody>
			</table>
			<br class="clear">
			
			<table class="form-table">
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
		<?php } ?>		
		
		
		<!-- SEKTION 2: READ MESSAGE -->
		<?php if($read){ ?>
			<?php if(isset($error)){ ?>
				<div id="settings-error-settings_updated" class="error settings-error">
					<p><b><?php _e("Error", "dexs-pm"); ?></b> <?php echo $error; ?></p>
				</div>
			<?php } else { ?>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div id="titlediv">
								<table class="wp-list-table widefat plugins" valign="center">
									<tbody>
										<tr>
											<th class="pm-table-th"><?php _e("Sender", "dexs-pm"); ?></th>
											<td class="pm-table-td">
												<?php if($read_pm->pm_sender == $user){ ?>
													<?php _e("You", "dexs-pm"); echo " (".get_userdata($read_pm->pm_sender)->display_name.")"; ?>
												<?php } else { ?>
													<?php echo "<a href='$spage&table=5&rec=".$read_pm->pm_sender."' title='".__("Send this user a PM", "dexs-pm")."'>".get_userdata($read_pm->pm_sender)->display_name."</a>"; ?>
												<?php } ?>
											</td>
										</tr>
										
										<tr>
											<th class="pm-table-th"><?php _e("Recipients", "dexs-pm"); ?></th>
											<td class="pm-table-td">
												<?php foreach($read_rec_meta AS $uid => $rec){ ?>
													<?php if($uid == $user){ ?>
														<?php $out[] = __("You", "dexs-pm")." (".get_userdata($uid)->display_name.")"; ?>
													<?php } else { ?>
														<?php $out[] = "<a href='$spage&table=5&rec=$uid' title='".__("Send this user a PM", "dexs-pm")."'>".get_userdata($uid)->display_name."</a>"; ?>
													<?php } ?>
												<?php } ?>
												<?php echo implode(", ", $out); ?>
											</td>
										</tr>
									</tbody>
								</table><br class="clear">

								<table class="wp-list-table widefat plugins" valign="center">
									<tbody>
										<tr>
											<th class="pm-table-th"><?php _e("Subject", "dexs-pm"); ?></th>
											<td class="pm-table-td">
												<b><?php echo $read_pm->pm_subject; ?></b>
												<span class="pm-info-right">
													<i><?php _e("Priority", "dexs-pm"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;</i>
													<?php if($read_meta['priority'] == 0){ _e("Normal", "dexs-pm"); } else ?>
													<?php if($read_meta['priority'] == 1){ _e("Middle", "dexs-pm"); } else ?>
													<?php if($read_meta['priority'] == 2){ _e("High", "dexs-pm"); } ?>
												</span>
											</td>
										</tr>
										
										<tr>
											<th class="pm-table-th"><?php _e("Message Info", "dexs-pm"); ?></th>
											<td class="pm-table-td">
												<span class="pm-info">
													<i><?php _e("Attachment", "dexs-pm"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;</i>
													<?php if(!$read_meta['file']){ echo "<i>".__("No Attachments!", "dexs-pm")."</i>"; } else { ?>
														<a href="<?php echo $upload_url."/".$read_meta['file_meta']['file']; ?>" target="_blank"><?php echo $read_meta['file_meta']['name']." (".ceil(705574/1024); ?> kb)</a>
													<?php } ?>
												</span>
												
												<span class="pm-info-right">
													<i><?php _e("PM Date", "dexs-pm"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;</i> 
													<?php echo date_format(date_create($read_pm->pm_send), get_option('date_format')." ".get_option('time_format')); ?>
												</span>
											</td>
										</tr>
										
										<tr>
											<th class="pm-table-th"><?php _e("Message", "dexs-pm"); ?></th>
											<td class="pm-table-td"><?php echo stripslashes(nl2br($read_pm->pm_message)); ?></td>
										</tr>
									</tbody>
								</table>
								<div id="postdivrich" class="postarea"><br class="clear">
									<input type="hidden" name="dexs_pm" value="read_pm">
									<p style="float:left;">
										<input type="submit" name="send_action[2]" class="button button-primary button-large action" value="<?php _e("Answer", "dexs-pm"); ?>">&nbsp;&nbsp;
										<input type="submit" name="send_action[1]" class="button button-secondary button-large action" value="<?php _e("Forward", "dexs-pm"); ?>">
									</p>
								
									<p style="float:right;">
									
										<?php if($folder == "4" || $folder == "2"){ ?>
											<input type="submit" name="action[5]" class="button button-secondary button-large action" value="<?php _e("Restore", "dexs-pm"); ?>">&nbsp;&nbsp;
										<?php } ?>
										
										<?php if($folder == "0" || $folder == "1"){ ?>
											<input type="submit" name="action[4]" class="button button-secondary button-large action" value="<?php _e("Archive", "dexs-pm"); ?>">&nbsp;&nbsp;
										<?php } ?>
										
										<?php if($folder != "2"){ ?>
											<input type="submit" name="action[2]" class="button button-secondary button-large action" value="<?php _e("Delete", "dexs-pm"); ?>">&nbsp;&nbsp;
										<?php } ?>
										
										<?php if($folder == "2"){ ?>
											<input type="submit" name="action[3]" class="button button-secondary button-large action" value="<?php _e("Delete Permanently", "dexs-pm"); ?>">&nbsp;&nbsp;
										<?php } ?>
										
									</p>
								</div>
								<?php if($read_meta['file']){ ?>
								<div id="postdivrich2" class="postarea"><br class="clear">
									<p class="description">
										<?php _e("The attachment was renamed for safety reasons, so don't wonder if you download the file.", "dexs-pm"); ?><br>
										<?php _e("The new name of the same file is:", "dexs-pm"); ?> <code><?php echo $read_meta['file_meta']['file']; ?></code>
									</p>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>	
		<?php } ?>	
		
		
		<!-- SEKTION 3: FOOTER -->
		<?php if($folder < "5" && !$read){ ?>
			<div class="tablenav bottom">
				<div class="alignleft actions">
					<input type="hidden" name="dexs_pm" value="folder">
					<select name="action[]">
						<option value="-1"><?php _e('Bulk Actions', 'dexs-pm'); ?></option>
						
						<?php echo $dexsPMT->get_bulk_actions($folder, true); ?>
					</select>
					<input type="submit" class="button action" name="action_1" value="<?php _e('Apply', 'dexs-pm'); ?>">
				</div>
				
				<div class="tablenav-pages">
					<span class="displaying-num">
						<?php if($counter == 1){
							_e('One Message', 'dexs-pm');
						} else {
							echo $counter." ".__('Messages', 'dexs-pm');						
						} ?>
					</span>
					
					<span class="pagination-links">
						<a class="first-page <?php if($offset == 1){ echo "disabled"; } ?>" title="<?php _e("Go to the First Page", "dexs-pm");?>" <?php if($offset >= ceil($counter/$max_view)){ echo "href='$use_page&row=1'"; } ?> style="cursor:pointer;">&laquo;</a>
						<a class="prev-page <?php if($offset == 1){ echo "disabled"; } ?>" title="<?php _e("Go to the Previous Page", "dexs-pm");?>" <?php if($offset >= ceil($counter/$max_view)){ echo "href='$use_page&row=".($offset-1)."'"; } ?> style="cursor:pointer;">&lsaquo;</a>
							<span class="paging-input">
								<?php echo $offset; ?> of <?php echo ceil($counter/$max_view); ?>
							</span>
						<a class="next-page <?php if($offset >= ceil($counter/$max_view)){ echo "disabled"; } ?>" title="<?php _e("Go to the Next Page", "dexs-pm");?>" <?php if($offset < ceil($counter/$max_view)){ echo "href='$use_page&row=".($offset+1)."'"; } ?> style="cursor:pointer;">&rsaquo;</a>
						<a class="last-page <?php if($offset >= ceil($counter/$max_view)){ echo "disabled"; } ?>" title="<?php _e("Go to the Last Page", "dexs-pm");?>" <?php if($offset < ceil($counter/$max_view)){ echo "href='$use_page&row=".ceil($counter/$max_view)."'"; } ?> style="cursor:pointer;">&raquo;</a>
					</span>
				</div>
			</div>
		<?php } ?>
		</form>
		
		<?php if($folder != "5" && $folder != "6"){ ?>
			<!-- SEKTION 4: COYPRIGHT NOTE -->
			<?php if($dexsPM->load_pm_settings("settings", 'show_copy')){ ?>
				<br class="clear">
				<p class="description" style="margin:10px 0 0 4px;font-size:10px;line-height:14px;">
					(Pit) Dexs Private Messages (PM) System v.<?php echo DPM_S_VER; ?><br>
					Copyright &copy 2012 - <?php echo date('Y'); ?> by PYTES.NET (SamBrishes)
				</p>
			<?php } ?>
		<?php } ?>
	<?php } ?>