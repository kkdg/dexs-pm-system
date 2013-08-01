<?php
	class DexsPMWidget extends WP_Widget {

		function DexsPMWidget() {
			// Instantiate the parent object
			parent::__construct(false, 'Dexs PM Widget');
		}

		function widget($args, $instance) {
			global $dexsPM, $dexsPMA;
			if($dexsPM->check_permissions("frontend") && is_user_logged_in()){
				extract($args);
				/* LOAD WIDGET CONFIG */
				$title = empty($instance['title']) ? false : apply_filters('widget_title', $instance['title']);
				$alert = isset($instance['show_alert'])? $instance['show_alert'] : true;
				$folder = isset($instance['show_folder'])? $instance['show_folder'] : true;
				$counter = isset($instance['show_counter'])? $instance['show_counter'] : true;
				$show_counter_count = isset($instance['show_counter_count'])? $instance['show_counter_count'] : true;
				
				/* SET CSS DEFAULT CONFIG */
				$bg_counter = "#B22222"; $border_counter = "#228B22"; $bg_counter_bar = "#00FF7F"; $font_color = "#000000";
				$shadow_color = "-webkit-box-shadow: inset 0 0px 1px 1px #FFFFFF;";
				$shadow_color .= "-moz-box-shadow: inset 0 0px 1px 1px #FFFFFF;";
				$shadow_color .= "box-shadow: inset 0 0px 1px 1px #FFFFFF;";
				
				/* LOAD CSS CONFIG */
				if(isset($instance['bg_counter'])){ $bg_counter = empty($instance['bg_counter']) ? "transparent" : "#".$instance['bg_counter']; }
				if(isset($instance['border_counter'])){ $border_counter = empty($instance['border_counter']) ? "transparent" : "#".$instance['border_counter']; }
				if(isset($instance['bg_counter_bar'])){ $bg_counter_bar = empty($instance['bg_counter_bar']) ? "transparent" : "#".$instance['bg_counter_bar']; }
				if(isset($instance['font_color'])){ $font_color = empty($instance['font_color']) ? "transparent" : "#".$instance['font_color']; }
				if(isset($instance['shadow_color'])){
					if(!empty($instance['shadow_color'])){
						$shadow_color = "-webkit-box-shadow: inset 0 0px 1px 1px #".$new_instance['shadow_color'].";";
						$shadow_color .= "-moz-box-shadow: inset 0 0px 1px 1px #".$new_instance['shadow_color'].";";
						$shadow_color .= "box-shadow: inset 0 0px 1px 1px #".$new_instance['shadow_color'].";";
					} else {
						$shadow_color = "";
					}
				}
			
				/* LOAD PM SETTINGS */
				$dpmw_count = $dexsPMA->count_messages("6");
				$max_messages = $dexsPM->check_permissions("max_messages");				
				if(!$dexsPM->get_frontend_id()){
					if($dexsPM->load_pm_settings("settings", "backend_navi") == 1){
						$page = "users.php?page=pm&folder=";
					} else {
						$page = "admin.php?page=pm&folder=";					
					}
				} else {
					$page = "?page_id=".$dexsPM->get_frontend_id()."&dpm=folder&folder=";
				}
				
				echo $before_widget;
				if($title){
					echo $before_title.$title.$after_title;
				}
				?>
				<?php if($alert){ ?>
					<?php if($dpmw_count["new"] > 0){ ?>
						<center style="font-size:13px;">
							<i><a href="<?php echo $page; ?>0" title="<?php _e("Go to your inbox!", "dexs-pm"); ?>" style="color:#FF4500;">
								<?php echo ($dpmw_count["new"] == 1)? __("You have a new message!", "dexs-pm") : __("You have", "dexs-pm")." ".$dpmw_count["new"]." ".__("new messages!", "dexs-pm"); ?>
							</a></i>
						</center>
					<?php } else { ?>
						<center>
							<i><small><?php _e("You have no new messages.", "dexs-pm"); ?></small></i>
						</center>
					<?php } ?>
				<?php } ?>
				
				<?php if($folder){ ?>
					<p>
						<ul>
							<li>
								<a href="<?php echo $page; ?>0" title="<?php _e("Go to your inbox!", "dexs-pm"); ?>"><?php _e("Inbox Folder", "dexs-pm"); ?></a>
								<span style="float:right;">(<?php echo $dpmw_count["inbox"]; ?>)</span>
							</li>
							
							<li>
								<a href="<?php echo $page; ?>1" title="<?php _e("Go to your outbox!", "dexs-pm"); ?>"><?php _e("Outbox Folder", "dexs-pm"); ?></a>
								<span style="float:right;">(<?php echo $dpmw_count["outbox"]; ?>)</span>
							</li>
							
							<li>
								<a href="<?php echo $page; ?>4" title="<?php _e("Go to your archive!", "dexs-pm"); ?>"><?php _e("Archive Folder", "dexs-pm"); ?></a>
								<span style="float:right;">(<?php echo $dpmw_count["archive"]; ?>)</span>
							</li>
							
							<li>
								<a href="<?php echo $page; ?>2" title="<?php _e("Go to your trash!", "dexs-pm"); ?>"><?php _e("Trash Folder", "dexs-pm"); ?></a>
								<span style="float:right;">(<?php echo $dpmw_count["trash"]; ?>)</span>
							</li>
						</ul>
					</p>
				<?php } ?>
				
				<?php if($counter){ ?>
					<?php if($max_messages == "-1"){
						$width = "100";
					} else if($max_messages == "0"){
						$width = "100";
					} else {
						$width = ($dpmw_count["all"]/$max_messages)*100;
					} ?>
					
					<style type="text/css">
					<!--
						.dpmsw_counter_bar{
							height: 5px;
							width: 100%;
							border: 1px solid <?php echo $border_counter; ?>;
							border-radius: 2px;
							-moz-border-radius: 2px;
							-webkit-border-radius: 2px;
							background-color: <?php echo $bg_counter ?>;
							text-align: center;
							<?php echo $shadow_color; ?>
						}
						.dpmsw_counter_bar_green{
							height: 5px;
							width: <?php echo $width; ?>%;
							background-color: <?php echo $bg_counter_bar; ?>;
							border-radius: 2px;
							-moz-border-radius: 2px;
							-webkit-border-radius: 2px;
							text-align: center;
							<?php echo $shadow_color; ?>
						}
						<?php if($show_counter_count){ ?>
							.dpmsw_counter_bar span{
								position: relative;
								font-size:11px;
								margin-top: -10px;
								font-color: <?php echo $font_color; ?>;
							}
						<?php } ?>
					-->
					</style>
					
					<p>
						<div class="dpmsw_counter_bar">
							<div class="dpmsw_counter_bar_green"></div>
							<?php if($show_counter_count){ ?>
								<span><?php echo $dpmw_count["all"]; ?> / <?php echo ($max_messages == "-1")? "&infin;" : $max_messages; ?></span>
							<?php } ?>
						</div>
					</p>					
				<?php } ?>
				<?php
				echo $after_widget;
			}
		}

		function update($new_instance, $old_instance) {
			$instance = $old_instance;
		
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['show_alert'] = isset($new_instance['show_alert'])? true : false;
			$instance['show_folder'] = isset($new_instance['show_folder'])? true : false;
			$instance['show_counter'] = isset($new_instance['show_counter'])? true : false;
			$instance['show_counter_count'] = isset($new_instance['show_counter_count'])? true : false;
			
			/* CSS */
			$instance['bg_counter'] = $new_instance['bg_counter'];
			$instance['border_counter'] = $new_instance['border_counter'];
			$instance['bg_counter_bar'] = $new_instance['bg_counter_bar'];
			$instance['font_color'] = $new_instance['font_color'];
			$instance['shadow_color'] = empty($new_instance['shadow_color'])? false : $new_instance['shadow_color'];
		
			return $instance;
		}

		function form($instance) {
			$defaults = array('title' => __('Private Messages', 'dexs-pm'), 'show_alert' => true, 'show_folder' => true, 'show_counter' => true, 'show_counter_count' => true, 'bg_counter' => 'B22222', 
							'border_counter' => '228B22', 'bg_counter_bar' => '00FF7F', 'font_color' => '000000', 'shadow_color' => 'FFFFFF');
			$instance = wp_parse_args((array) $instance, $defaults);
			?>
			<p>
				<label><?php _e('Widget Title', 'dexs-pm'); ?>:
				<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>">
				</label>
			</p>
			
			<p>
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_alert'); ?>" name="<?php echo $this->get_field_name('show_alert'); ?>" <?php echo ($instance['show_alert'])? "checked='checked'" : "" ?>>
				<label for="<?php echo $this->get_field_id('show_alert'); ?>"><?php _e("Display new Message(s) notice?", "dexs-pm"); ?></label>
			</p>
			
			<p>
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_folder'); ?>" name="<?php echo $this->get_field_name('show_folder'); ?>" <?php echo ($instance['show_folder'])? "checked='checked'" : "" ?>>
				<label for="<?php echo $this->get_field_id('show_folder'); ?>"><?php _e("Display folder system?", "dexs-pm"); ?></label>
			</p>
			
			<p>
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_counter'); ?>" name="<?php echo $this->get_field_name('show_counter'); ?>" <?php echo ($instance['show_counter'])? "checked='checked'" : "" ?>>
				<label for="<?php echo $this->get_field_id('show_counter'); ?>"><?php _e("Display counter bar?", "dexs-pm"); ?></label>
			</p>
			
			<p>
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_counter_count'); ?>" name="<?php echo $this->get_field_name('show_counter_count'); ?>" <?php echo ($instance['show_counter_count'])? "checked='checked'" : "" ?>>
				<label for="<?php echo $this->get_field_id('show_counter_count'); ?>"><?php _e("Display counter number?", "dexs-pm"); ?></label>
			</p>
			
			<h4 style="margin-bottom:4px;"><?php _e("CSS Color Adjustments", "dexs-pm"); ?></h4>
			<table style="width: 100%;"><tbody>
			<tr>
				<td><label for="<?php echo $this->get_field_id('bg_counter'); ?>"><?php _e('Counter Background', 'dexs-pm'); ?>:</label></td>
				<td style="text-align:right;">
					#<input type="text" size="5" id="<?php echo $this->get_field_id('bg_counter'); ?>" name="<?php echo $this->get_field_name('bg_counter'); ?>" value="<?php echo $instance['bg_counter']; ?>">
				</td>
			</tr>
			
			<tr>
				<td><label for="<?php echo $this->get_field_id('border_counter'); ?>"><?php _e('Counter Border Color', 'dexs-pm'); ?>:</label></td>
				<td style="text-align:right;">
					#<input type="text" size="5" id="<?php echo $this->get_field_id('border_counter'); ?>" name="<?php echo $this->get_field_name('border_counter'); ?>" value="<?php echo $instance['border_counter']; ?>">
				</td>
			</tr>
			
			<tr>
				<td><label for="<?php echo $this->get_field_id('bg_counter_bar'); ?>"><?php _e('Counter Bar Color', 'dexs-pm'); ?>:</label></td>
				<td style="text-align:right;">
					#<input type="text" size="5" id="<?php echo $this->get_field_id('bg_counter_bar'); ?>" name="<?php echo $this->get_field_name('bg_counter_bar'); ?>" value="<?php echo $instance['bg_counter_bar']; ?>">
				</td>
			</tr>
			
			<tr>
				<td><label for="<?php echo $this->get_field_id('font_color'); ?>"><?php _e('Counter Font Color', 'dexs-pm'); ?>:</label></td>
				<td style="text-align:right;">
					#<input type="text" size="5" id="<?php echo $this->get_field_id('font_color'); ?>" name="<?php echo $this->get_field_name('font_color'); ?>" value="<?php echo $instance['font_color']; ?>">
				</td>
			</tr>
			
			<tr>
				<td><label for="<?php echo $this->get_field_id('shadow_color'); ?>"><?php _e('Counter Shadow Color', 'dexs-pm'); ?>:</label></td>
				<td style="text-align:right;">
					#<input type="text" size="5" id="<?php echo $this->get_field_id('shadow_color'); ?>" name="<?php echo $this->get_field_name('shadow_color'); ?>" value="<?php echo $instance['shadow_color']; ?>">
				</td>
			</tr>
			
			<tr>
				<td colspan="2">			
					<p class="description">
						<?php _e("Leave this option blank to deactivate the box shadow!", "dexs-pm"); ?>
					</p>
				</td>
			</tr>
			</tbody></table>
			<?php
		}
	}

	function myplugin_register_widgets() {
		register_widget( 'DexsPMWidget' );
	}

	add_action( 'widgets_init', 'myplugin_register_widgets' );
?>