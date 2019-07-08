<h3 class="divider"><?php _e( 'Font', 'sv100' ); ?></h3>
<div class="sv_setting_flex">
	<?php
		echo $module->get_settings()['font_family']->run_type()->form();
		echo $module->get_settings()['font_size']->run_type()->form();
		echo $module->get_settings()['font_color']->run_type()->form();
		echo $module->get_settings()['font_line_height']->run_type()->form();
	?>
</div>