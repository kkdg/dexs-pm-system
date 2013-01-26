	
	<script type="text/javascript">
		function markAll(){
			if(document.getElementById('all').checked){
				for(i = 0; i < document.messages.length; i++){
					document.messages.elements["select[]"][i].checked = true;
				}			
			} else {
				for(i = 0; i < document.messages.length; i++){
					document.messages.elements["select[]"][i].checked = false;
				}
			}
		}
		
		function markMe(){
			if(document.getElementById('all').checked){
				document.getElementById('all').checked = false;
			}
		}
	</script>
	<br>
	<form method="post" name="messages" action="">
		<select class="pm_action" name="action">
			<option value="-1"><?php _e('Bulk Actions', 'dexs-pm'); ?></option>
			<?php echo $bulk_actions; ?>
		</select>
		<input type="submit" class="pm_button" name="the_action_box" value="<?php _e('Apply', 'dexs-pm'); ?>">
		
		<div class="pm_pages" style="float:right;">
			<span class="pm_pages_num">
				<?php if($counter == 1){
					_e('One Message', 'dexs-pm');
				} else {
					echo $counter." ".__('Messages', 'dexs-pm');						
				} ?>
			</span>
			<span class="pm_links">
				<a class="pm_button <?php echo $disa; ?>" title="" href="<?php echo $pm_url; ?>">&laquo;</a>
				<a class="pm_button <?php echo $disa; ?>" title="" href="<?php echo $pm_url.$prev_link ?>">&lsaquo;</a>
					<span class="paging-input">
						<?php if(!isset($_GET['view'])){ echo "1"; } else { echo $_GET['view']+1; } ?> of <?php if(ceil($counter/$limit) == "0"){ echo "1"; } else {echo ceil($counter/$limit); } ?>
					</span>
				<a class="pm_button <?php echo $disb; ?>" title="" href="<?php echo $pm_url.$next_link; ?>">&rsaquo;</a>
				<a class="pm_button <?php echo $disb; ?>" title="" href="<?php echo $pm_url.$last_link; ?>">&raquo;</a>
			</span>
		</div>
		
		<table class="pm_table">
			<thead>
				<tr valign="top">
					<th style="width: 1%;" scope="row" class="manage-column check-column"><input type="checkbox" id="all" name="all" OnClick="markAll();"></th>
					<th style="width: 22%;" class="manage-column"><?php echo $first_td; ?></th>
					<th style="width: 22%;" class="manage-column"><?php _e('Subject', 'dexs-pm'); ?></th>
					<th style="width: 43%;" class="manage-column"><?php _e('Excerpt', 'dexs-pm'); ?></th>
					<th style="width: 14%;" class="manage-column"><?php _e('Date', 'dexs-pm'); ?></th>
				</tr>
			</thead>
			
			<tbody>
				<?php if(!empty($messages)){ ?>
				
					<?php foreach($messages AS $m){ ?>
						<tr valign="top">
							<th scope="row" class="manage-column check-column"><input type="checkbox" name="select[]" OnClick="markMe();" value="<?php echo $m->pm_id; ?>"></th>
							<td><?php
								if($table == "inbox"){
									$sender = get_userdata($m->pm_sender_id);
									echo $sender->display_name;
								}
								if($table == "outbox"){
									$recipients = explode(",", $m->pm_recipient_ids);
									if(count($recipients) > 2){
										echo "<b>".count($recipients)." ".__('Recipients', 'dexs-pm')."</b>";
									} else {
										if(count($recipients) == 1){
											$rec_1 = get_userdata($recipients[0]);
											echo $rec_1->display_name;									
										} else {
											$rec_1 = get_userdata($recipients[0]);
											$rec_2 = get_userdata($recipients[1]);
											echo $rec_1->display_name.", ".$rec_2->display_name;		
										}
									}
								}
								if($table == "archive" || $table == "trash"){
									if($m->pm_sender_id == $current_user->ID){
										$recipients = explode(",", $m->pm_recipient_ids);
										if(count($recipients) > 2){
											echo "<b>".count($recipients)." ".__('Recipients', 'dexs-pm')."</b>";
										} else {
											if(count($recipients) == 1){
												$rec_1 = get_userdata($recipients[0]);
												echo "<span style='color:#bc0b0b'>".$rec_1->display_name."</span>";									
											} else {
												$rec_1 = get_userdata($recipients[0]);
												$rec_2 = get_userdata($recipients[1]);
												echo "<span style='color:#bc0b0b'>".$rec_1->display_name.", ".$rec_2->display_name."</span>";		
											}
										}
									} else {
										$sender = get_userdata($m->pm_sender_id);
										echo "<span style='color:#21759b'>".$sender->display_name."</span>";
									}
								}
							?></td>
							<td>
							<span class="pm_subject"><a href="<?php echo $url."pm_".$table; ?>&action=read&id=<?php echo $m->pm_id; ?>"><?php echo $m->pm_subject; ?></a></span>
								<div class="pm_table_actions">
									<?php if($m->pm_sender_id != $current_user-ID){ ?>
										<span class="normal"><a href="<?php echo $url; ?>send_pm&action=answer&id=<?php echo $m->pm_id; ?>"><?php _e('Answer', 'dexs-pm'); ?></a></span> | 
									<?php } ?>
										<span class="normal"><a href="<?php echo $url; ?>send_pm&action=forward&id=<?php echo $m->pm_id; ?>"><?php _e('Forward', 'dexs-pm'); ?></a></span>
								</div>
							</td>
							<td><?php echo strip_tags(substr($m->pm_message, 0, 90)); if(strlen($m->pm_message) >= 91){ echo "..."; } ?>
							<br>
								<div class="pm_table_actions">
									<?php if($table == "inbox" || $table == "outbox"){ ?>
										<span class="normal"><a href="<?php echo $url."pm_".$table; ?>&action=4&id=<?php echo $m->pm_id; ?>"><?php _e('Archive', 'dexs-pm'); ?></a></span> | 
										<span class="delete"><a href="<?php echo $url."pm_".$table; ?>&action=2&id=<?php echo $m->pm_id; ?>"><?php _e('Trash', 'dexs-pm'); ?></a></span>
									<?php } elseif($table == "trash"){ ?>
										<span class="normal"><a href="<?php echo $url."pm_".$table; ?>&action=1&id=<?php echo $m->pm_id; ?>"><?php _e('Restore', 'dexs-pm'); ?></a></span> | 
										<span class="delete"><a href="<?php echo $url."pm_".$table; ?>&action=3&id=<?php echo $m->pm_id; ?>"><?php _e('Delete', 'dexs-pm'); ?></a></span>
									<?php } elseif($table == "archive"){ ?>
										<span class="normal"><a href="<?php echo $url."pm_".$table; ?>&action=1&id=<?php echo $m->pm_id; ?>"><?php _e('Move Back', 'dexs-pm'); ?></a></span> | 
										<span class="delete"><a href="<?php echo $url."pm_".$table; ?>&action=2&id=<?php echo $m->pm_id; ?>"><?php _e('Trash', 'dexs-pm'); ?></a></span>
									<?php } ?>								
								</div>				
							</td>
							<td><?php
								$date = date_create($m->pm_send);
								echo date_format($date, get_option('date_format'));
							?></td>
						</tr>
					<?php } ?>
			
				<?php } else { ?>
					<tr valign="top">
						<td colspan="4" style="text-align:center;padding: 10px 0;color: #777;"><em><?php echo __('You have no messages in your', 'dexs-pm')." ".$name; ?></em></td>
					</tr>	
				<?php } ?>
			</tbody>
		</table>
	</form>
