<?php
/**
 *	Display Admin Interface PM Tables
 *	
 *	@param string "inbox", "outbox", "archive", "trash", EMPTY
 *	@since 0.9.0 BETA
 */
function get_pm_table($table = "inbox"){
	global $wpdb, $current_user, $default;
	$table_name = $wpdb->prefix."dexs_pms";
	
	if(isset($_GET['action']) && $_GET['action'] == "read"){
		return;
	}
	
	if($table != "inbox" && $table != "outbox" && $table != "archive" && $table != "trash"){
		$table = "inbox";
	}
	
	/* GET/SET PM SETTINGS */
	$user_settings = get_user_meta( $current_user->ID, "dexs_pm_settings", true ); $user_id = $current_user->ID;
	if(empty($user_settings[$table."_num"])){ $limit = 20; } else { $limit = $user_settings[$table."_num"]; }
	if(!isset($_GET['view'])){ $page = 0; } else { $page = $_GET['view']*$limit; }
	$get_messages = $wpdb->get_results("SELECT * FROM $table_name ORDER BY pm_send DESC LIMIT $page,$limit");
	
	if(preg_match("#admin.php#", $_SERVER["PHP_SELF"])){ $pm_url = "admin.php?page=".$_GET['page']; } else
	if(preg_match("#users.php#", $_SERVER["PHP_SELF"])){ $pm_url = "users.php?page=".$_GET['page']; }
	if(isset($_GET['table'])){ $pm_url .= "&table=".$_GET['table']; }
	
	if(preg_match("#admin.php#", $_SERVER["PHP_SELF"])){ $send_table = "admin.php?page=".$_GET['page']; } else
	if(preg_match("#users.php#", $_SERVER["PHP_SELF"])){ $send_table = "users.php?page=".$_GET['page']; }
	$send_table .= "&table=send_pm";
	
	$counter = count_pm("", $table);
	if(!isset($_GET['view'])){ $disa = "disabled"; } else { $disa = ""; }
	if($counter <= ($_GET['view']+1)*$limit){ $disb = "disabled"; } else { $disb = ""; }
	
	/* GET PAGE LINKS */
	$prev_link = ""; $next_link = ""; $last_link = "";
	if(isset($_GET['view']) && $_GET['view'] > 2){
		$prev_link = "&view=".$_GET['view']-1;
	}
	if(!isset($_GET['view'])){
		if($counter > $limit){
			$next_link = "&view=1";
		}
	} else {
		if($counter > $limit){
			if(($_GET['view']+1)*$limit >= $counter){
				$next_link = "&view=".$_GET['view'];		
			} else {
				$next_link = "&view=".$_GET['view']+1;		
			}
		}
	}
	if($counter > $limit){
		$code = ceil($counter/$limit)-1;
		$last_link = "&view=".$code;
	}
	
	$options = get_option('dexs_pm_system', $default);
	
	/* GET/SET PM SPECIFY OPTIONS */
	$table_system .= '<th style="width:2%;" scope="col" class="manage-column check-column"><input type="checkbox"></th>';
	
	if($table == "inbox"){
		$name = __('Inbox', 'dexs-pm');
		$bulk_actions .= '<option value="4">'.__('Archive', 'dexs-pm').'</option>';
		$bulk_actions .= '<option value="2">'.__('Trash', 'dexs-pm').'</option>';
		$bulk_actions .= '<option value="1">'.__('Mark as Read', 'dexs-pm').'</option>';
		$bulk_actions .= '<option value="0">'.__('Mark as Unread', 'dexs-pm').'</option>';
		
		$table_system .= '<th style="width:20%;" scope="col" class="manage-column">'.__('Sender', 'dexs-pm').'</th>';
		$table_system .= '<th style="width:25%;" scope="col" class="manage-column">'.__('Subject Title', 'dexs-pm').'</th>';
		$table_system .= '<th style="width:38%;" scope="col" class="manage-column">'.__('Excerpt', 'dexs-pm').'</th>';
		$table_system .= '<th style="width:15%;" scope="col" class="manage-column">'.__('Date', 'dexs-pm').'</th>';
	} else
	if($table == "outbox"){
		$name = __('Outbox', 'dexs-pm');
		$bulk_actions .= '<option value="4">'.__('Archive', 'dexs-pm').'</option>';
		$bulk_actions .= '<option value="2">'.__('Trash', 'dexs-pm').'</option>';

		$table_system .= '<th style="width:20%;" scope="col" class="manage-column">'.__('Recipient(s)', 'dexs-pm').'</th>';
		$table_system .= '<th style="width:25%;" scope="col" class="manage-column">'.__('Subject Title', 'dexs-pm').'</th>';
		$table_system .= '<th style="width:38%;" scope="col" class="manage-column">'.__('Excerpt', 'dexs-pm').'</th>';
		$table_system .= '<th style="width:15%;" scope="col" class="manage-column">'.__('Date', 'dexs-pm').'</th>';
	} else
	if($table == "archive"){
		$name = __('Archive', 'dexs-pm');
		$bulk_actions .= '<option value="1">'.__('Move Back', 'dexs-pm').'</option>';
		$bulk_actions .= '<option value="2">'.__('Trash', 'dexs-pm').'</option>';

		$table_system .= '<th style="width:20%;" scope="col" class="manage-column">'.__('Users', 'dexs-pm').'</th>';
		$table_system .= '<th style="width:25%;" scope="col" class="manage-column">'.__('Subject Title', 'dexs-pm').'</th>';
		$table_system .= '<th style="width:38%;" scope="col" class="manage-column">'.__('Excerpt', 'dexs-pm').'</th>';
		$table_system .= '<th style="width:15%;" scope="col" class="manage-column">'.__('Date', 'dexs-pm').'</th>';
	} else
	if($table == "trash"){
		$name = __('Trash', 'dexs-pm');
		$bulk_actions .= '<option value="1">'.__('Restore', 'dexs-pm').'</option>';
		$bulk_actions .= '<option value="3">'.__('Delete Permanently', 'dexs-pm').'</option>';

		$table_system .= '<th style="width:20%;" scope="col" class="manage-column">'.__('Users', 'dexs-pm').'</th>';
		$table_system .= '<th style="width:25%;" scope="col" class="manage-column">'.__('Subject Title', 'dexs-pm').'</th>';
		$table_system .= '<th style="width:38%;" scope="col" class="manage-column">'.__('Excerpt', 'dexs-pm').'</th>';
		$table_system .= '<th style="width:15%;" scope="col" class="manage-column">'.__('Date', 'dexs-pm').'</th>';
	}
?>
	<form method="post" action="">
	
	<!-- TOP TABLE START -->
		<div class="tablenav top">
			<div class="alignleft actions">
				<select name="action">
					<option value="-1"><?php _e('Bulk Actions', 'dexs-pm'); ?></option>
					<?php echo $bulk_actions; ?>
				</select>
				<input type="submit" class="button action" name="the_action_box" value="<?php _e('Apply', 'dexs-pm'); ?>">
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
					<a class="first-page <?php echo $disa; ?>" title="" href="<?php echo $pm_url; ?>">&laquo;</a>
					<a class="prev-page <?php echo $disa; ?>" title="" href="<?php echo $pm_url.$prev_link ?>">&lsaquo;</a>
						<span class="paging-input">
							<?php if(!isset($_GET['view'])){ echo "1"; } else { echo $_GET['view']+1; } ?> of <?php if(ceil($counter/$limit) == "0"){ echo "1"; } else {echo ceil($counter/$limit); } ?>
						</span>
					<a class="next-page <?php echo $disb; ?>" title="" href="<?php echo $pm_url.$next_link; ?>">&rsaquo;</a>
					<a class="last-page <?php echo $disb; ?>" title="" href="<?php echo $pm_url.$last_link; ?>">&raquo;</a>
				</span>
			</div>
		</div>
	<!-- TOP TABLE END -->

	
	<!-- MAIN TABLE START -->
		<table class="wp-list-table widefat inbox">
			<thead>
				<tr>
					<?php echo $table_system; ?>
				</tr>
			</thead>
			
			<tbody>
				<?php if($counter != 0){ $c = 0; ?>
					<?php foreach($get_messages AS $pm){ ?>
						<?php if(preg_match("#".$user_id."\b#", $pm->pm_recipient_ids) || preg_match("#".$user_id."#", $pm->pm_sender_id)){ ?>
							<?php $checkstatus = unserialize($pm->pm_status); ?>
							<?php $sender = get_userdata( $pm->pm_sender_id ); ?>
							<?php if($pm->pm_priority == 0){ $color = "none"; } ?>
							<?php if($pm->pm_priority == 1){ $color = "#eaeaea"; } ?>
							<?php if($pm->pm_priority == 2){ $color = "#e9f1ff"; } ?>
							<?php if($pm->pm_priority == 3){ $color = "#d5e4fe"; } ?>
							
							<?php if($table == "inbox"){ $c++;?>
							<?php if(array_key_exists("recipient_$user_id", $checkstatus) && $checkstatus["recipient_$user_id"] == 1 || array_key_exists("recipient_$user_id", $checkstatus) && $checkstatus["recipient_$user_id"] == 0){ ?>
								<tr style="background-color:<?php echo $color; ?>">
									<th scope="row" class="check-column"><input type="checkbox" name="select[]" value="<?php echo $pm->pm_id; ?>"></th>
									<td class="username column-username">
										<?php echo get_avatar( $pm->pm_sender_id, 32 ); ?>
										<?php echo $sender->display_name; ?><br>
										<div class="row-actions">
											<span class="send_pm"><a href="<?php echo $send_table; ?>&action=write_to_users&id=<?php echo $pm->pm_sender_id; ?>"><?php _e('Write a PM', 'dexs-pm'); ?></a></span>
										</div>
									</td>
									<td class="username column-username">
										<?php if($checkstatus["recipient_$user_id"] == 0){ ?>
											<img src="<?php echo plugins_url('dexs-pm-system/images/pm.png' , "dexs-pm-system"); ?>" height="32px" width="32px" class="avatar avatar-32 photo">
										<?php } else { ?>
											<img src="<?php echo plugins_url('dexs-pm-system/images/pm_open.png' , "dexs-pm-system"); ?>" height="32px" width="32px" class="avatar avatar-32 photo">
										<?php } ?>
										<?php if($checkstatus["recipient_$user_id"] == 0){ echo "<strong>  "; } ?>
											<a href="<?php echo $pm_url; ?>&action=read&id=<?php echo $pm->pm_id; ?>"><?php echo $pm->pm_subject; ?></a>
										<?php if($checkstatus["recipient_$user_id"] == 0){ echo "</strong>"; } ?>
										<br>
										<div class="row-actions">
											<span class="read"><a href="<?php echo $pm_url; ?>&action=read&id=<?php echo $pm->pm_id; ?>">
											<?php if($checkstatus["recipient_$user_id"] == 0 && $pm->pm_priority == "3"){ echo "<b>"; } ?>
												<?php _e('Read', 'dexs-pm'); ?>
											<?php if($checkstatus["recipient_$user_id"] == 0 && $pm->pm_priority == "3"){ echo "</b>"; } ?>
											</a></span> | 
											<span class="answer"><a href="<?php echo $send_table; ?>&action=answer&id=<?php echo $pm->pm_id; ?>"><?php _e('Answer', 'dexs-pm'); ?></a></span> |
											<span class="forward"><a href="<?php echo $send_table; ?>&action=forward&id=<?php echo $pm->pm_id; ?>"><?php _e('Forward', 'dexs-pm'); ?></a></span>
										</div>
									</td>
									<td><?php echo strip_tags(substr($pm->pm_message, 0, 180)); if(strlen($pm->pm_message) >= 181){ echo "..."; } ?>
									<br>
										<div class="row-actions">
											<?php if($checkstatus["recipient_$user_id"] == 0){ ?>
												<span class="markasread"><a href="<?php echo $pm_url; ?>&action=1&id=<?php echo $pm->pm_id; ?>"><?php _e('Mark as Read', 'dexs-pm'); ?></a></span> | 
											<?php } else { ?>
												<span class="markasunread"><a href="<?php echo $pm_url; ?>&action=0&id=<?php echo $pm->pm_id; ?>"><?php _e('Mark as Unread', 'dexs-pm'); ?></a></span> | 
											<?php } ?>
											<span class="archive"><a href="<?php echo $pm_url; ?>&action=4&id=<?php echo $pm->pm_id; ?>"><?php _e('Archive', 'dexs-pm'); ?></a></span> | 
											<span class="delete"><a href="<?php echo $pm_url; ?>&action=2&id=<?php echo $pm->pm_id; ?>"><?php _e('Trash', 'dexs-pm'); ?></a></span>
										</div>					
									</td>
									<td><?php
										$date = date_create($pm->pm_send);
										echo date_format($date, get_option('date_format')." ".get_option('time_format'));
									?></td>
								</tr>
							<?php } ?>
							<?php } ?>
							
							<?php if($table == "outbox"){ $c++;?>
							<?php if(array_key_exists("sender_$user_id", $checkstatus) && $checkstatus["sender_$user_id"] == 1){ ?>
								<tr>
									<th scope="row" class="check-column"><input type="checkbox" name="select[]" value="<?php echo $pm->pm_id; ?>"></th>
									<td>
										<?php $list_recipients = explode(",", $pm->pm_recipient_ids);
										$the_users = "";
										foreach($list_recipients AS $list_reci){
											$list_user = get_userdata($list_reci);
											if($checkstatus["recipient_$list_reci"] != "0"){ $col = "#21759b"; } else { $col = "#bc0b0b"; }
											$the_users[] = "<span style='color:$col'>".$list_user->display_name."<span>";
										}
										echo implode(', ', $the_users); ?>
									</td>
									<td class="username column-username">
											<a href="<?php echo $pm_url; ?>&action=read&id=<?php echo $pm->pm_id; ?>"><?php echo $pm->pm_subject; ?></a>
										<br>
										<div class="row-actions">
											<span class="read"><a href="<?php echo $pm_url; ?>&action=read&id=<?php echo $pm->pm_id; ?>"><?php _e('Read', 'dexs-pm'); ?></a></span> | 
											<span class="forward"><a href="<?php echo $send_table; ?>&action=forward&id=<?php echo $pm->pm_id; ?>"><?php _e('Forward', 'dexs-pm'); ?></a></span>
										</div>
									</td>
									<td><?php echo strip_tags(substr($pm->pm_message, 0, 180)); if(strlen($pm->pm_message) >= 181){ echo "..."; } ?>
									<br>
										<div class="row-actions">
											<span class="archive"><a href="<?php echo $pm_url; ?>&action=4&id=<?php echo $pm->pm_id; ?>"><?php _e('Archive', 'dexs-pm'); ?></a></span> | 
											<span class="delete"><a href="<?php echo $pm_url; ?>&action=2&id=<?php echo $pm->pm_id; ?>"><?php _e('Trash', 'dexs-pm'); ?></a></span>
										</div>					
									</td>
									<td><?php
										$date = date_create($pm->pm_send);
										echo date_format($date, get_option('date_format')." ".get_option('time_format'));
									?></td>
								</tr>								
							<?php } ?>
							<?php } ?>
							
							<?php if($table == "archive"){ $c++;?>
							<?php if(array_key_exists("sender_$user_id", $checkstatus) && $checkstatus["sender_$user_id"] == 4 || array_key_exists("recipient_$user_id", $checkstatus) && $checkstatus["recipient_$user_id"] == 4){ ?>
								<tr>
									<th scope="row" class="check-column"><input type="checkbox" name="select[]" value="<?php echo $pm->pm_id; ?>"></th>
									<td>
										<?php $list_recipients = explode(",", $pm->pm_recipient_ids);
										$get_s_sender = get_userdata($pm->pm_sender_id);
										echo "<span style='color:#21759b'>".$get_s_sender->display_name."</span><br>";
										$the_users = "";
										foreach($list_recipients AS $list_reci){
											$list_user = get_userdata($list_reci);
											$the_users[] = "<span style='color:#bc0b0b'>".$list_user->display_name."</span>";
										}
										echo implode(', ', $the_users); ?>
									</td>
									<td class="username column-username">
											<a href="<?php echo $pm_url; ?>&action=read&id=<?php echo $pm->pm_id; ?>"><?php echo $pm->pm_subject; ?></a>
										<br>
										<div class="row-actions">
											<span class="read"><a href="<?php echo $pm_url; ?>&action=read&id=<?php echo $pm->pm_id; ?>"><?php _e('Read', 'dexs-pm'); ?></a></span> | 
											<span class="forward"><a href="<?php echo $send_table; ?>&action=forward&id=<?php echo $pm->pm_id; ?>"><?php _e('Forward', 'dexs-pm'); ?></a></span>
										</div>
									</td>
									<td><?php echo strip_tags(substr($pm->pm_message, 0, 180)); if(strlen($pm->pm_message) >= 181){ echo "..."; } ?>
									<br>
										<div class="row-actions">
											<span class="restore"><a href="<?php echo $pm_url; ?>&action=1&id=<?php echo $pm->pm_id; ?>"><?php _e('Move Back', 'dexs-pm'); ?></a></span> | 
											<span class="delete"><a href="<?php echo $pm_url; ?>&action=2&id=<?php echo $pm->pm_id; ?>"><?php _e('Trash', 'dexs-pm'); ?></a></span>	
										</div>					
									</td>
									<td><?php
										$date = date_create($pm->pm_send);
										echo date_format($date, get_option('date_format')." ".get_option('time_format'));
									?></td>
								</tr>									
							<?php } ?>
							<?php } ?>
							
							<?php if($table == "trash"){ $c++;?>
							<?php if(array_key_exists("sender_$user_id", $checkstatus) && $checkstatus["sender_$user_id"] == 2 || array_key_exists("recipient_$user_id", $checkstatus) && $checkstatus["recipient_$user_id"] == 2){ ?>
								<tr>
									<th scope="row" class="check-column"><input type="checkbox" name="select[]" value="<?php echo $pm->pm_id; ?>"></th>
									<td>
										<?php $list_recipients = explode(",", $pm->pm_recipient_ids);
										$get_s_sender = get_userdata($pm->pm_sender_id);
										echo "<span style='color:#21759b'>".$get_s_sender->display_name."</span><br>";
										$the_users = "";
										foreach($list_recipients AS $list_reci){
											$list_user = get_userdata($list_reci);
											$the_users[] = "<span style='color:#bc0b0b'>".$list_user->display_name."<span>";
										}
										echo implode(', ', $the_users); ?>
									</td>
									<td class="username column-username">
											<a href="<?php echo $pm_url; ?>&action=read&id=<?php echo $pm->pm_id; ?>"><?php echo $pm->pm_subject; ?></a>
										<br>
										<div class="row-actions">
											<span class="read"><a href="<?php echo $pm_url; ?>&action=read&id=<?php echo $pm->pm_id; ?>"><?php _e('Read', 'dexs-pm'); ?></a></span>
										</div>
									</td>
									<td><?php echo strip_tags(substr($pm->pm_message, 0, 180)); if(strlen($pm->pm_message) >= 181){ echo "..."; } ?>
									<br>
										<div class="row-actions">
											<span class="restore"><a href="<?php echo $pm_url; ?>&action=1&id=<?php echo $pm->pm_id; ?>"><?php _e('Restore', 'dexs-pm'); ?></a></span> | 
											<span class="delete"><a href="<?php echo $pm_url; ?>&action=3&id=<?php echo $pm->pm_id; ?>"><?php _e('Delete Permanently', 'dexs-pm'); ?></a></span>	
										</div>					
									</td>
									<td><?php
										$date = date_create($pm->pm_send);
										echo date_format($date, get_option('date_format')." ".get_option('time_format'));
									?></td>
								</tr>							
							<?php } ?>
							<?php } ?>
							
						<?php } ?>
					<?php } ?>					
					<?php if($c == 0){ ?>
						<td colspan="5" align="center"><h3><?php echo __('No messages found!', 'dexs-pm'); ?></h3></td>						
					<?php } ?>
				<?php } else { ?>
					<td colspan="5" align="center"><h3><?php echo __('You have no messages in your', 'dexs-pm')." ".$name; ?></h3></td>
				<?php } ?>	
			</tbody>
			
			<tfoot>
				<tr>
					<?php echo $table_system; ?>				
				</tr>
			</tfoot>
		</table>
	<!-- MAIN TABLE END -->
	
	
	<!-- BOTTOM TABLE START -->
		<div class="tablenav bottom">
			<div class="alignleft actions">
				<select name="action2">
					<option value="-1"><?php _e('Bulk Actions', 'dexs-pm'); ?></option>
					<?php echo $bulk_actions; ?>
				</select>
				<input type="submit" class="button action" name="the_action_box2" value="<?php _e('Apply', 'dexs-pm'); ?>">
			</div>
		
			<div class="tablenav-pages one-page">
				<?php if($table == "inbox"){ ?>
					<div style="width:auto;padding: 0 4px;"><i><?php _e('Legend', 'dexs-pm'); ?> <?php _e('Priority', 'dexs-pm'); ?></i>: 
					<span style="padding:3px;background-color:#F9F9F9;border:1px solid #bbb;"><?php _e('Normal', 'dexs-pm'); ?></span> | 
					<span style="padding:3px;background-color:#eaeaea;border:1px solid #bbb;"><?php _e('Medium', 'dexs-pm'); ?></span> |
					<span style="padding:3px;background-color:#e9f1ff;border:1px solid #bbb;"><?php _e('High', 'dexs-pm'); ?></span> | 
					<span style="padding:3px;background-color:#d5e4fe;border:1px solid #bbb;"><?php _e('Very High', 'dexs-pm'); ?></span></div>
				<?php } elseif($table == "outbox"){ ?>
					<i><?php _e('Legend', 'dexs-pm'); ?></i>: 
					<span style="color:#21759b"><?php _e('Has read the message', 'dexs-pm'); ?></span> | <span style="color:#bc0b0b"><?php _e('Has <b>NOT</b> read the message', 'dexs-pm'); ?></span>
				<?php } else { ?>
					<i><?php _e('Legend', 'dexs-pm'); ?></i>: 
					<span style="color:#21759b"><?php _e('The Sender', 'dexs-pm'); ?></span> | <span style="color:#bc0b0b"><?php _e('The Recipient(s)', 'dexs-pm'); ?></span>
				<?php } ?>
			</div>
		</div>
	<!-- BOTTOM TABLE END -->
	</form>
	<?php if($options['backend_copyright'] == 1){ ?>
		<p class="description" style="margin:40px 0 0 4px;font-size:10px;line-height:14px;">
			Dexs Private Messages (PM) System v.<?php echo VERSION; ?><br>
			Written & Copyright &copy 2012 - <?php echo date('Y'); ?> by <a href="http://www.sambrishes.net/wordpress" title="WordPress Plugins 4 Free and 4 Fun" style="text-decoration:none;">SamBrishesWeb WordPress</a>
		</p>
	<?php } ?>
<?php } ?>


<?php
/**
 *	Display Admin Send Table
 *	
 *	@since 0.9.0 BETA
 */	
function pm_send_table($get_reci = "", $get_subject = "", $get_message = ""){
	global $wpdb, $current_user, $default;
	get_currentuserinfo();  
	
	if(!isset($get_reci)){ $get_reci = ""; }
	if(!isset($get_subject)){ $get_subject = ""; }
	if(!isset($get_message)){ $get_message = ""; }
	
	$the_options = get_option('dexs_pm_system', $default);
	$table_name = $wpdb->prefix."dexs_pms";
	$user_names = $wpdb->get_results( "SELECT display_name, ID FROM $wpdb->users WHERE ID != '".$current_user->ID."' ORDER BY display_name ASC" );
	
	if(isset($_POST['the_recipient_ids']) && $_POST['the_recipient_ids'] != ""){ $get_reci = $_POST['the_recipient_ids']; }
	if(is_array($get_reci)){ $get_reci = implode(",", $get_reci); }
	if(isset($_POST['subject'])){ $get_subject = $_POST['subject']; }
	if(isset($_POST['the_message'])){ $get_message = $_POST['the_message'];	}
	
	if(check_permission($current_user->ID, "images") == true){
		$media_buttons = true;
	} else {
		$media_buttons = false;
	}
	
	$settings = array(
		'dfw' => false,
		'textarea_name' => 'the_message',
		'media_buttons' => $media_buttons, 
		'quicktags' => false, 
		'tinymce' => array(
			'theme_advanced_buttons1' => 'undo, redo, separator, forecolor, separator, bold, italic, underline, strikethrough, separator, link, unlink, separator, bullist, numlist, separator, hr, removeformat, separator', 
			'theme_advanced_buttons2' => '')
	);
	
	if($the_options['recipient_listing'] == "1"){
		# The FaceBook List
		foreach ($user_names AS $user_name){
			$usernames2 .= '{"caption":"'.$user_name->display_name.'","value":"'.$user_name->ID.'"}, ';
		}
		$usernames = substr($usernames2, 0, -2);
		if (!is_admin()) add_action("wp_enqueue_scripts", "my_jquery_enqueue", 11);
		
		/* LOAD AUTOCPMPLETE INPUT FIELD */
		echo '<script type="text/javascript" src="../wp-content/plugins/dexs-pm-system/include/js/prototype.js"></script>';
		echo '<script type="text/javascript" src="../wp-content/plugins/dexs-pm-system/include/js/scriptaculous.js"></script>';
		echo '<script type="text/javascript" src="../wp-content/plugins/dexs-pm-system/include/js/facebooklist.js"></script>';
	} else {
		/* LOAD DROP DOWN INPUT FIELD */
		echo '<script type="text/javascript" src="../wp-content/plugins/dexs-pm-system/include/js/dropdown.js"></script>';
	} ?>
	
	<div id="poststuff">
		<form action="" method="post" autocomplete="off">
			<div id="titlediv" style="margin-bottom:20px;">
				<div id="titlewrap">
					<?php if($get_subject == ""){ ?>
						<label id="title-prompt-text" for="subject" OnClick="document.getElementById('title').select();document.getElementById('title-prompt-text').style.visibility='hidden';">
							<?php _e('Enter Subject here', 'dexs-pm'); ?>
						</label>
					<?php } ?>
					<input type="text" name="subject" value="<?php if(isset($get_subject)){ echo $get_subject; } ?>" id="title" style="width:600px;" OnClick="document.getElementById('title-prompt-text').style.visibility='hidden';" 
					OnBlur="if(this.value == ''){ document.getElementById('title-prompt-text').style.visibility='visible'; }">
				</div>
			</div>
			
			<div class="postbox" style="width: 600px;margin-bottom:30px;">
				<h3 class="hndle" style="cursor:auto;"><span><?php _e('Recipients', 'dexs-pm'); ?></span></h3>
				<div class="inside">
					<?php if($the_options[recipient_listing] == "1"){ ?>		<!-- AutoComplete Input Style -->
						<input type="text" value="<?php echo $recipient; ?>" id="facebook-demo" name="the_recipient_ids">
						<div id="facebook-auto">
			  
						  <div class="default">&nbsp;&nbsp;<?php _e('Type here the names of each recipients.', 'dexs-pm'); ?></div>
						  <ul class="feed">
							<?php if(!empty($get_reci)){
								$recipient2 = explode(',', $get_reci);
								foreach($recipient2 AS $rec){
									$rec1 = $wpdb->get_var("SELECT display_name FROM $wpdb->users WHERE ID='$rec' LIMIT 1");
									echo "<li value='".$rec."'>".$rec1."</li><a></a>";
								}
							} ?>
						  </ul>
						</div>					
					<?php } else { ?>											<!-- DropDown Input Style -->
						<select id="recipient_change" name="recipient_change[]" onchange="get_recipient(this.value)" style="margin-bottom: 10px;">
							<option id="nothing" name="nothing" value="nothing"><?php _e('Select a Recipient', 'dexs-pm'); ?></option>
							<optgroup id="addthisnow" label="------------"></optgroup>
							
								<?php foreach($user_names AS $user_name){
										echo "<option id='".$user_name->ID."' value='".$user_name->ID.",".$user_name->display_name."'"; 
											if(!empty($get_reci) && preg_match("/\b".$user_name->ID."\b/i", $get_reci)){ echo " disabled='disabled'"; }
										echo ">".$user_name->display_name."</option>";
								} ?>
							
						</select>
						<ul id="the_fields" class="holderdrop">
							<li class="bit-box" style="visibility:hidden;width:0;padding-left:0;padding-right:0;margin-left:0;margin-right:0;">&nbsp;</li>
							<?php if(!empty($get_reci)){
									$recipient2 = explode(',', $get_reci);
									foreach($recipient2 AS $rec){
										$rec1 = $wpdb->get_var("SELECT display_name FROM $wpdb->users WHERE ID='$rec' LIMIT 1");
										echo "<li class='bit-box' id='user_".$rec."'>".$rec1."<a class='closebutton' onclick=\"remove_recipient('".$rec."_-_".$rec1."')\"></a></li>";
									}
								} ?>
						</ul>
						<span id="fields">
							<?php if(!empty($get_reci)){
								$recipient2a = explode(',', $get_reci);
								foreach($recipient2a AS $rec){
									echo "<input type='hidden' name='the_recipient_ids[]' value='".$rec."' id='theuser_".$rec."'>";
								}
							} ?>					
						</span>
					<?php }?>
				</div>
			</div>
			
			<div id="postexcerpt" class="postbox" style="width: 600px;margin-bottom:20px;">
				<div class="handlediv" style="width:160px;text-align:right;background:none;">
					<div style="padding-top:2px;padding-right:2px;">
						<label for="priority"><?php _e('Priority', 'dexs-pm'); ?></label>&nbsp;&nbsp;
						<select name="priority" id="priority">
							<option value="0"><?php _e('Normal', 'dexs-pm'); ?></option>
							<option value="1"><?php _e('Medium', 'dexs-pm'); ?></option>
							<option value="2"><?php _e('High', 'dexs-pm'); ?></option>
							<option value="3"><?php _e('Very High', 'dexs-pm'); ?></option>
						</select>
					</div>
				</div>
				<h3 class="hndle" style="cursor:auto;"><span><?php _e('Message', 'dexs-pm'); ?></span></h3>
				<div class="inside">
					<p style="width:100%; text-align:right;margin: 5px 0px;padding: 0;">
						<?php 
							wp_editor( stripslashes(htmlspecialchars(nl2br($get_message))), 'pm_message', $settings );
						?>
					</p>			
				</div>
			</div>
			
			<?php if(check_permission($current_user->id, "pm") == true){ ?>
				<input type="submit" class="button-primary" name="send_pm" value="<?php _e('Send PM', 'dexs-pm'); ?>">&nbsp;&nbsp;&nbsp;
			<?php } ?>
			<input type="reset" class="button-secondary" value="<?php _e('Reset', 'dexs-pm'); ?>">
		</form>
	</div>
	<script language="JavaScript">
		document.observe('dom:loaded', function() {
		
		  tlist2 = new FacebookList('facebook-demo', 'facebook-auto',{ newValues: false });

		  var myjson = [<?php echo $usernames; ?>];
		  myjson.each(function(t){tlist2.autoFeed(t)});
		});    
	</script>
<?php } ?>


<?php
/**
 *	Display Admin Settings Table
 *	
 *	@since 0.9.0 BETA
 */
function pm_settings_table(){
	global $current_user, $default;
	get_currentuserinfo();
	
	$user_settings = get_user_meta($current_user->ID, "dexs_pm_settings", true);
	$general_settings = get_option('dexs_pm_system', $default);

	if(!empty($user_settings['email_notice'])){
		$check = $user_settings['email_notice'];
	} else {
		if(check_permission($current_user->ID, "default") == true){
			$check = "1";
		} else {
			$check = "0";
		}
	}
	
?>	
	<?php if(!isset($_GET['success'])){ echo '<div class="tablenav top" style="height:1px;margin:0;padding:0;"></div>'; } ?>
	<form action="" method="post">
									
		<h3 class="title"><?php _e('Messages per Side', 'dexs-pm'); ?></h3>	
		<table class="form-table">
			<tbody>			
				<tr valign="top">
					<td style="padding:0;margin:0;">
						<table><tr>
							<td style="padding: 0 20px 0 10px;"><label for="inbox"><?php _e('Inbox', 'dexs-pm'); ?></label></td>
							<td style="padding: 0 20px 0 10px;"><label for="outbox"><?php _e('Outbox', 'dexs-pm'); ?></label></td>
							<td style="padding: 0 20px 0 10px;"><label for="archive"><?php _e('Archive', 'dexs-pm'); ?></label></td>
							<td style="padding: 0 20px 0 10px;"><label for="trash"><?php _e('Trash', 'dexs-pm'); ?></label></td>
						</tr><tr>
							<td style="padding: 0 20px 0 10px;">
								<input type="number" step="1" min="-1" id="inbox" name="inbox_num" class="small-text" value="<?php if($user_settings['inbox_num'] != ""){ echo $user_settings['inbox_num']; } else { echo "20"; } ?>">
							</td>
							<td style="padding: 0 20px 0 10px;">
								<input type="number" step="1" min="-1" id="outbox" name="outbox_num" class="small-text" value="<?php if($user_settings['outbox_num'] != ""){ echo $user_settings['outbox_num']; } else { echo "20"; } ?>">
							</td>
							<td style="padding: 0 20px 0 10px;">
								<input type="number" step="1" min="-1" id="archive" name="archive_num" class="small-text" value="<?php if($user_settings['archive_num'] != ""){ echo $user_settings['archive_num']; } else { echo "20"; } ?>">
							</td>
							<td style="padding: 0 20px 0 10px;">
								<input type="number" step="1" min="-1" id="trash" name="trash_num" class="small-text" value="<?php if($user_settings['trash_num'] != ""){ echo $user_settings['trash_num']; } else { echo "20"; } ?>">
							</td>						
						</tr></table>
					</td>
				</tr>
			</tbody>
		</table>
			
		<?php if($general_settings['email_notice'] == "1"){ ?>
			<br class="clear">
			<h3><?php _e('eMail Notification', 'dexs-pm'); ?></h3>
			<table class="form-table">
				<tbody>			
					<tr valign="top">
						<td style="padding: 0 20px 0 10px;">
							<label for="enable"><input type="radio" value="1" name="email_notice" id="enable" <?php if($check == "1"){ echo "checked='checked'"; } ?>>
								<span><?php _e('Activate', 'dexs-pm'); ?></span></label><br>
							<label for="disable"><input type="radio" value="0" name="email_notice" id="disable" <?php if($check == "0"){ echo "checked='checked'"; } ?>>
								<span><?php _e('Deactivate', 'dexs-pm'); ?></span></label>
						
						</td>
					</tr>
				</tbody>
			</table>
		<?php } ?>			
		
		<p class="submit"><input type="submit" name="submit_user" id="submit_user" value="<?php _e('Save Changes', 'dexs-pm'); ?>" class="button-primary"></p>

	</form>
<?php			
}
?>


<?php
/**
 *	Display Admin Settings Table
 *	
 *	@param string PM ID
 *	@param stdClass Object DB Array
 *	@since 0.9.0 BETA
 */
function pm_read_table($id, $get_pm){
	global $wpdb, $current_user;
	$get_pm = get_object_vars($get_pm);
	if(preg_match("#admin.php#", $_SERVER["PHP_SELF"])){ $pm_url = "admin.php?page=".$_GET['page']; } else
	if(preg_match("#users.php#", $_SERVER["PHP_SELF"])){ $pm_url = "users.php?page=".$_GET['page']; }
	$pm_url .= "&table=send_pm";
	
	if(preg_match("#admin.php#", $_SERVER["PHP_SELF"])){ $action_url = "admin.php?page=".$_GET['page']; } else
	if(preg_match("#users.php#", $_SERVER["PHP_SELF"])){ $action_url = "users.php?page=".$_GET['page']; }
	if(isset($_GET['table'])){ $action_url .= "&table=".$_GET['table']; }

	$get_status = unserialize($get_pm['pm_status']);
	$get_user_id = $current_user->ID;
	$get_pm_recipients = explode(",", $get_pm['pm_recipient_ids']);
	$count_recipient = count($get_pm_recipients);
	$sender_id = get_userdata($get_pm['pm_sender_id']);
	$get_pm_date = date_create($get_pm['pm_send']);
	if($get_pm['pm_priority'] == "1"){
		$pri = __('Middle', 'dexs-pm');
	} else if($get_pm['pm_priority'] == "2"){
		$pri = __('<b>High</b>', 'dexs-pm');
	} else if($get_pm['pm_priority'] == "3"){
		$pri = "<span style='font-weight:bold;color:#cf2222;'>".__('Very High', 'dexs-pm')."</span>";
	} else {
		$pri = __('<i>Normal</i>', 'dexs-pm');
	}
?>
	<script type='text/javascript'>
		function visible(value){
			var field = value;
			if(document.getElementById('user_' + field).style.visibility == ''){
				document.getElementById('user_' + field).style.visibility = 'visible';
			} else {
				document.getElementById('user_' + field).style.visibility = '';
			}
		}
	</script>

	<div id="poststuff">
		<form method="post" action="">
			<input type="hidden" name="pm_id" value="<?php echo $get_pm['pm_id']; ?>">
			<input type="hidden" name="send" value="true">
			<div id="postexcerpt" class="postbox" style="width: 600px;margin-bottom:20px;">
				<div class="handlediv" style="width:160px;text-align:right;background:none;">
					<div style="padding: 9px 10px 0 0;">
						<?php echo date_format($get_pm_date, get_option('date_format')." ".get_option('time_format')); ?>
					</div>
				</div>
				<h3 class="hndle" style="cursor:auto;"><span><?php _e('The Sender', 'dexs-pm'); ?></span></h3>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<td width='550px;' class='username column-username'>
									<?php echo get_avatar( $sender_id->ID, 50 ).$sender_id->display_name."<br>"; ?>
									<?php if($sender_id->ID == $get_user_id){ ?>
										<input type='checkbox' name='send_pm_recipients[]' OnClick='visible(this.value);' value='<?php echo $sender_id->ID; ?>' disabled='disabled'>  | 
										<span class='write'><?php _e('You', 'dexs-pm'); ?></span>
									<?php } else { ?>
										<input type='checkbox' name='send_pm_recipients[]' OnClick='visible(this.value);' value='<?php echo $sender_id->ID; ?>'>  | 
										<span class='write'><a class='no-underline' href='<?php echo $pm_url; ?>&action=write_to_users&id=<?php echo $get_pm['pm_sender_id'] ?>'><?php _e('Write a PM', 'dexs-pm'); ?></a></span>
									<?php } ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<div id="postexcerpt" class="postbox" style="width: 600px;margin-bottom:20px;">
				<div class="handlediv" style="width:160px;text-align:right;background:none;">
					<div style="padding: 9px 10px 0 0;">
						<?php if($count_recipient == "1"){
							_e('1 Recipient', 'dexs-pm');
						} else {
							echo $count_recipient." ".__('Recipients', 'dexs-pm');		
						} ?>
					</div>
				</div>
				<h3 class="hndle" style="cursor:auto;"><span><?php _e('The Recipient(s)', 'dexs-pm'); ?></span></h3>
				<div class="inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<?php $i = 0; foreach($get_pm_recipients AS $get_pm_recipient){ $i++; ?>
									<?php $user = get_userdata($get_pm_recipient); ?>
									<?php if(strlen($user->display_name) > 20){
										echo "<td width='300px;' colspan='2' class='username column-username'>"; $i++;
									} else {
										echo "<td width='150px;' class='username column-username'>";
									} ?>
										<?php echo get_avatar( $get_pm_recipient, 40 ).$user->display_name."<br>"; ?>
										<div class="row-actions" id="user_<?php echo $get_pm_recipient; ?>">
										<?php if($get_pm_recipient == $current_user->ID){ ?>
											<input type='checkbox' name='send_pm_recipients[]' OnClick='visible(this.value);' value='<?php echo $user->ID; ?>' disabled='disabled'>  | 
											<span class='write'><?php _e('You', 'dexs-pm'); ?></span>
										<?php } else { ?>
											<input type='checkbox' name='send_pm_recipients[]' OnClick='visible(this.value);' value='<?php echo $user->ID; ?>'>  | 
											<span class='write'><a class='no-underline' href='<?php echo $pm_url; ?>&action=write_to_users&id=<?php echo $user->ID; ?>'><?php _e('Write a PM', 'dexs-pm'); ?></a></span>
										<?php } ?>
										</div>
									</td>
									
									<?php if($i == 3){ $i=0; ?>
											</tr>
											<tr valign="top">
									<?php } ?>
								<?php } ?>
							</tr>
							<tr>
								<td style="padding:0;margin:0;">
									<input type='submit' name='write_to_users' class='no-button' value='<?php _e('Write to all selected users', 'dexs-pm')?>'>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<div id="postexcerpt" class="postbox" style="width: 600px;margin-bottom:20px;">
				<div class="handlediv" style="width:160px;text-align:right;background:none;">
					<div style="padding: 9px 10px 0 0;">
						<?php echo __('Priority', 'dexs-pm').": ".$pri; ?>
					</div>
				</div>
				<h3 class="hndle" style="cursor:auto;"><span><?php _e('The Message', 'dexs-pm'); ?></span></h3>
				<div class="inside">
					<h3 class="title" style="background:none;border:none;padding:6px 4px 6px 4px;margin:6px 4px 4px 4px;box-shadow:none;cursor:auto;"><?php echo $get_pm['pm_subject']; ?></h3>
					<?php echo stripslashes(nl2br($get_pm['pm_message'])); ?>
					<hr>
					<div style="width:100%;">
						<span style="min-width:50%;text-align:left;">
							<?php if($get_pm['pm_sender_id'] != $current_user->ID){ ?>
								<a class='no-underline' href='<?php echo $pm_url; ?>&action=answer&id=<?php echo $get_pm['pm_id']; ?>'><?php _e('Answer', 'dexs-pm'); ?></a> | 
							<?php } ?>
							<input type='submit' name='answer_to_users' class='no-button' value='<?php _e('Answer to all selected users', 'dexs-pm')?>'> | 
							<a class='no-underline' href='<?php echo $pm_url; ?>&action=forward&id=<?php echo $get_pm['pm_id']; ?>'><?php _e('Forward', 'dexs-pm'); ?></a>
						</span>
						<span style="float:right;">
							<?php if(!isset($_GET['table']) || isset($_GET['table']) && $_GET['table'] != "pm_archive"){ echo "<a class='no-underline' href='".$action_url."&action=4&id=".$get_pm['pm_id']."'>".__('Archive', 'dexs-pm')."</a> | "; } ?>
							<?php if(!isset($_GET['table']) || isset($_GET['table']) && $_GET['table'] != "pm_trash"){ echo "<a class='no-underline' href='".$action_url."&action=2&id=".$get_pm['pm_id']."'>".__('Trash', 'dexs-pm')."</a>"; } ?>
							<?php if(isset($_GET['table']) && $_GET['table'] == "pm_trash"){ echo "<a class='no-underline' href='".$action_url."&action=1&id=".$get_pm['pm_id']."'>".__('Restore', 'dexs-pm')."</a> | "; } ?>
							<?php if(isset($_GET['table']) && $_GET['table'] == "pm_trash"){ echo "<a class='no-underline' href='".$action_url."&action=3&id=".$get_pm['pm_id']."'>".__('Delete Permanently', 'dexs-pm')."</a>"; } ?>
						</span>
					</div>
				</div>
			</div>
		</form>	
	</div>
<?php } ?>