<?php
	$counter = $this->pm_counter;
	$offset = $this->pm_offset;
	$max_view = $this->pm_max_view;
	$use_page = $dexsPMT->get_folder_url($this->pm_folder);
?>
	<form method="post" name="messages" action="">
		<div class="tablenav top">
			<div class="alignleft actions">
				<input type="hidden" name="dexs_pm" value="folder">
				<select class="pm_action" name="action">
					<option value="-1"><?php _e('Bulk Actions', 'dexs-pm'); ?></option>
					
					<?php foreach($this->get_bulk_actions($this->pm_folder) AS $act => $desc){ ?>
						<option value="<?php echo $act; ?>"><?php echo $desc; ?></option>
					<?php } ?>
				</select>
				<input type="submit" class="button action pm_button" name="action_0" value="<?php _e('Apply', 'dexs-pm'); ?>">
			</div>
			
			<div class="tablenav-pages pm_pages" style="text-align:right;">
				<span class="displaying-num pm_pages_num">
					<?php if($counter == 1){
						_e('One Message', 'dexs-pm');
					} else {
						echo $counter." ".__('Messages', 'dexs-pm');						
					} ?>
				</span>
				
				<span class="pagination-links pm_links">
					<a class="pm_button first-page <?php if($offset == 1){ echo "disabled"; } ?>" title="<?php _e("Go to the First Page", "dexs-pm");?>" <?php if($offset >= ceil($counter/$max_view)){ echo "href='$use_page&row=1'"; } ?> style="cursor:pointer;">&laquo;</a>
					<a class="pm_button prev-page <?php if($offset == 1){ echo "disabled"; } ?>" title="<?php _e("Go to the Previous Page", "dexs-pm");?>" <?php if($offset >= ceil($counter/$max_view)){ echo "href='$use_page&row=".($offset-1)."'"; } ?> style="cursor:pointer;">&lsaquo;</a>
						<span class="paging-input">
							<?php echo $offset; ?> of <?php echo (ceil($counter/$max_view) == 0)? "1" : ceil($counter/$max_view); ?>
						</span>
					<a class="pm_button next-page <?php if($offset >= ceil($counter/$max_view)){ echo "disabled"; } ?>" title="<?php _e("Go to the Next Page", "dexs-pm");?>" <?php if($offset < ceil($counter/$max_view)){ echo "href='$use_page&row=".($offset+1)."'"; } ?> style="cursor:pointer;">&rsaquo;</a>
					<a class="pm_button last-page <?php if($offset >= ceil($counter/$max_view)){ echo "disabled"; } ?>" title="<?php _e("Go to the Last Page", "dexs-pm");?>" <?php if($offset < ceil($counter/$max_view)){ echo "href='$use_page&row=".ceil($counter/$max_view)."'"; } ?> style="cursor:pointer;">&raquo;</a>
				</span>
			</div>
		</div>
		<br class="clear">

		<!-- TABLE -->
		<table class="wp-list-table widefat inbox pm_table">
			<thead>
				<tr>
					<th style="width:2%;" scope="col" class="manage-column check-column">
						<input type="checkbox" id="all" name="all" OnClick="markAll();">
					</th>
					<?php $this->get_table_header($this->pm_folder, true); ?>
				</tr>
			</thead>
			
			<tbody>
				<?php if(count($this->pm_messages) == 0){ ?>
					<td colspan="5">
						<br class="clear"><center>
						<i><p class="description" style="text-align:center;"><?php _e("This folder contains no private messages.", "dexs-pm"); ?></p></i>
						</center>
					</td>
				<?php } ?>
				<?php foreach($this->pm_messages AS $message){ ?>
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
					<tr id="pm-<?php echo $message->pm_id; ?> " class="post-<?php echo $message->pm_id; ?> type-post format-standard alternate" style="background-color:<?php if($this->pm_folder == 0){ echo $priority; } ?>;">
						
						<th scope="row" class="manage-column check-column">
							<input type="checkbox" name="pm_id[]" OnClick="markMe();" value="<?php echo $message->pm_id; ?>">
						</th>
						
						<td class="subject">
							<?php if(!$dexsPMA->is_pm_read($meta_rec, $this->cur_user)){ ?><strong><?php } ?>
								<a class="row_title" href="<?php $this->get_folder_url($this->pm_folder, true); ?>&read=1&pmid=<?php echo $message->pm_id; ?>" title="<?php _e("Read", "dexs_pm"); ?> “<?php echo $message->pm_subject; ?>”"><?php echo $message->pm_subject; ?></a>
							<?php if(!$dexsPMA->is_pm_read($meta_rec, $this->cur_user)){ ?></strong><?php } ?>
							
							<div class="row-actions pm_table_actions">
								<?php if($this->pm_folder != "2"){ ?>
								<span class="edit">
									<a href="<?php $this->get_folder_url($this->pm_folder, true); ?>&read=1&pmid=<?php echo $message->pm_id; ?>" title="<?php _e("Read this message", "dexs-pm") ?>"><?php _e("Read", "dexs-pm") ?></a>&nbsp;|
								</span>
								<?php } ?>
								
								<?php if($this->pm_folder != "4"){ ?>
								<span class="edit">
									<a href="<?php $this->get_folder_url($this->pm_folder, true); ?>&action=4&pmid=<?php echo $message->pm_id; ?>" title="<?php _e("Archive this message", "dexs-pm") ?>"><?php _e("Archive", "dexs-pm") ?></a>&nbsp;|
								</span>
								<?php } ?>
								
								<?php if($this->pm_folder != "0" && $this->pm_folder != "1"){ ?>
								<span class="edit">
									<a href="<?php $this->get_folder_url($this->pm_folder, true); ?>&action=5&pmid=<?php echo $message->pm_id; ?>" title="<?php _e("Restore this message", "dexs-pm") ?>"><?php _e("Restore", "dexs-pm") ?></a>&nbsp;|
								</span>
								<?php } ?>
								
								<?php if($this->pm_folder != "2"){ ?>
								<span class="trash">
									<a href="<?php $this->get_folder_url($this->pm_folder, true); ?>&action=2&pmid=<?php echo $message->pm_id; ?>" title="<?php _e("Delete this message", "dexs-pm") ?>"><?php _e("Delete", "dexs-pm") ?></a>
								</span>
								<?php } ?>
								
								<?php if($this->pm_folder == "2"){ ?>
								<span class="trash">
									<a href="<?php $this->get_folder_url($this->pm_folder, true); ?>&action=3&pmid=<?php echo $message->pm_id; ?>" title="<?php _e("Delete this message permanently", "dexs-pm") ?>"><?php _e("Delete Permanently", "dexs-pm") ?></a>
								</span>
								<?php } ?>
							</div>
						</td>
						
						<td class="users"><?php $dexsPMT->load_user_structur($this->pm_folder, $message); ?></td>						
						
						<?php if($this->template_style){ ?>
							<td class="excerpt">
								<?php echo strip_tags(substr($message->pm_message, 0, 150)); ?>
								<div class="row-actions pm_table_actions">
									<span class="edit">
										<a href="<?php $this->get_folder_url(5, true); ?>&send_action=1&pmid=<?php echo $message->pm_id; ?>" title="<?php _e("Forward this message", "dexs-pm") ?>"><?php _e("Forward", "dexs-pm") ?></a>&nbsp;|
									</span>
									
									<span class="edit">
										<a href="<?php $this->get_folder_url(5, true); ?>&send_action=2&pmid=<?php echo $message->pm_id; ?>" title="<?php _e("Answer to this message", "dexs-pm") ?>"><?php _e("Answer", "dexs-pm") ?></a>
									</span>
								</div>							
							</td>
						<?php } ?>
						
						<td class="send_date" style="text-align:right;"><?php echo date_format(date_create($message->pm_send), get_option('date_format')." ".get_option('time_format')); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>	
	</form>