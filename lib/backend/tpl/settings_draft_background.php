<h3 class="divider"><?php _e( 'Background', 'sv100' ); ?></h3>
<div>
	<div class="sv_setting_flex">
		<?php
			echo $module->get_settings()['background_color']->run_type()->form();
			echo $module->get_settings()['background_image']->run_type()->form();
			echo $module->get_settings()['background_image_media_size']->run_type()->form();
		?>
	</div>
	<div class="sv_setting_flex">
		<?php
			echo $module->get_settings()['background_image_position']->run_type()->form();
			echo $module->get_settings()['background_image_size']->run_type()->form();
			echo $module->get_settings()['background_image_repeat']->run_type()->form();
			echo $module->get_settings()['background_image_attachment']->run_type()->form();
		?>
	</div>
</div>