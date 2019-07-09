<h3 class="divider"><?php _e( 'Font - General', 'sv100' ); ?></h3>
<div class="sv_setting_flex">
	<?php
		echo $module->get_settings()['font_family']->run_type()->form();
		echo $module->get_settings()['font_size']->run_type()->form();
		echo $module->get_settings()['font_line_height']->run_type()->form();
	?>
</div>
<div class="sv_setting_flex">
	<?php
		echo $module->get_settings()['text_decoration']->run_type()->form();
		echo $module->get_settings()['font_color']->run_type()->form();
	?>
</div>

<h3 class="divider"><?php _e( 'Font - Links', 'sv100' ); ?></h3>
<div class="sv_setting_flex">
	<?php
		echo $module->get_settings()['font_family_link']->run_type()->form();
		echo $module->get_settings()['font_size_link']->run_type()->form();
		echo $module->get_settings()['font_line_height_link']->run_type()->form();
		echo $module->get_settings()['text_decoration_link']->run_type()->form();
	?>
</div>
<div class="sv_setting_flex">
	<?php
		echo $module->get_settings()['font_color_link']->run_type()->form();
		echo $module->get_settings()['font_background_color_active_link']->run_type()->form();
		echo $module->get_settings()['font_background_color_link']->run_type()->form();
	?>
</div>

<h3 class="divider"><?php _e( 'Font - Links (Hover/Focus)', 'sv100' ); ?></h3>
<div class="sv_setting_flex">
	<?php
		echo $module->get_settings()['font_family_link_hover']->run_type()->form();
		echo $module->get_settings()['font_size_link_hover']->run_type()->form();
		echo $module->get_settings()['font_line_height_link_hover']->run_type()->form();
		echo $module->get_settings()['text_decoration_link_hover']->run_type()->form();
	?>
</div>
<div class="sv_setting_flex">
	<?php
		echo $module->get_settings()['font_color_link_hover']->run_type()->form();
		echo $module->get_settings()['font_background_color_active_link_hover']->run_type()->form();
		echo $module->get_settings()['font_background_color_link_hover']->run_type()->form();
	?>
</div>