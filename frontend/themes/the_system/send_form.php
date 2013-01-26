<?php

	if(check_permission($current_user->ID, "images") == true){
		$media_buttons = true;
	} else {
		$media_buttons = false;
	}
	
	$settings = array(
		'textarea_name' => 'the_message',
		'media_buttons' => $media_buttons, 
		'quicktags' => false, 
		'tinymce' => array(
			'theme_advanced_buttons1' => 'undo, redo, separator, forecolor, separator, bold, italic, underline, strikethrough, separator, link, unlink, separator, bullist, numlist, separator, hr, removeformat, separator', 
			'theme_advanced_buttons2' => '')
	);
?>

	<form action="" method="post" autocomplete="off">

		<h3 class="pm_title"><?php _e('Subject', 'dexs-pm'); ?></h3>
		<input type="text" name="subject" value="<?php if(isset($get_subject)){ echo $get_subject; } ?>" id="title" class="pm_input"></text>
		
		<h3 class="pm_title"><?php _e('Recipients', 'dexs-pm'); ?></h3>		
		<?php if($options['recipient_listing'] == "1"){ ?>		<!-- AutoComplete Input Style -->
		
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
			
		<?php } else { ?>										<!-- DropDown Input Style -->
		
			<select id="recipient_change" name="recipient_change[]" onchange="get_recipient(this.value)" style="margin-bottom: 10px;" class="pm_action">
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
					
		<?php } ?>
				
		<div style="padding-top:2px;float:right;">
			<label for="priority"><?php _e('Priority', 'dexs-pm'); ?></label>&nbsp;&nbsp;
			<select name="priority" id="priority" class="pm_action">
				<option value="0"><?php _e('Normal', 'dexs-pm'); ?></option>
				<option value="1"><?php _e('Medium', 'dexs-pm'); ?></option>
				<option value="2"><?php _e('High', 'dexs-pm'); ?></option>
				<option value="3"><?php _e('Very High', 'dexs-pm'); ?></option>
			</select>
		</div>	
		<h3 class="pm_title"><?php _e('Message', 'dexs-pm'); ?></h3>
		<p style="width:100%; text-align:right;margin: 5px 0px;padding: 0;">
			<?php 
				wp_editor( stripslashes(htmlspecialchars(nl2br($get_message))), 'pm_message', $settings );
			?>
		</p>
		
		<?php if(check_permission($current_user->id, "pm") == true){ ?>	
			<input type="submit" class="pm_button" name="send_pm" value="<?php _e('Send PM', 'dexs-pm'); ?>">&nbsp;&nbsp;&nbsp;
		<?php } ?>
		<input type="reset" class="pm_button" value="<?php _e('Reset', 'dexs-pm'); ?>">
	</form>
	
	<script language="JavaScript">
		document.observe('dom:loaded', function() {
		
		  tlist2 = new FacebookList('facebook-demo', 'facebook-auto',{ newValues: false });

		  var myjson = [<?php echo $usernames; ?>];
		  myjson.each(function(t){tlist2.autoFeed(t)});
		});    
	</script>
	
	<?php wp_footer(); ?>