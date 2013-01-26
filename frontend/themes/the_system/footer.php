
	<?php $theme = get_pm_theme(); ?>

	<table class="pm_copyright">
		<tr>
			<td>
				<?php if($options['frontend_copy'] == "1"){ ?>
					Dexs Private Messages (PM) System v.<?php echo VERSION; ?><br>
					Written & Copyright &copy 2012 - <?php echo date('Y'); ?> by <a href="http://www.sambrishes.net/wordpress" title="WordPress Plugins 4 Free and 4 Fun" style="text-decoration:none;">SamBrishesWeb WordPress</a>
				<?php } ?>			
			</td>
			<td>
				<?php if($options['frontend_tcopy'] == "1" && !empty($theme)){ ?>
					<?php echo $theme['name']." Theme ".$theme['version']; ?><br>
					<?php echo "Copyright &copy ".date('Y')." <a href='".$theme['web']."'>".$theme['author']."</a>"; ?>
				<?php } ?>	
			</td>
		</tr>
	</table>
