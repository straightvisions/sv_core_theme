<?php
	namespace sv100;
	
	class background_color extends settings_components {
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( 'Background Color', 'sv100' );
			$setting->default_value	= '#FFFFFF';
			$setting->type			= 'color';
			
			$setting->init();
		}
	}