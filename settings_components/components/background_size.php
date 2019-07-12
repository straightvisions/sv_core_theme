<?php
	namespace sv100;
	
	class background_size extends settings_components {
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( 'Background Size', 'sv100' );
			$setting->description	= '<p>' . __( 'Background Size in Pixel', 'sv100' ) . '<br>
					' . __( 'If disabled Background Fit will take effect.', 'sv100' ) . '</p>
					<p><strong>' . __( '0 = Disabled', 'sv100' ) . '</strong></p>';
			$setting->type			= 'number';
			$setting->default_value	= 0;
			$setting->placeholder	= '0 ';
			$setting->min			= 0;
			
			$setting->init();
		}
	}