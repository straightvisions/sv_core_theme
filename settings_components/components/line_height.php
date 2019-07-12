<?php
	namespace sv100;
	
	class line_height extends settings_components {
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( 'Line Height', 'sv100' );
			$setting->description	= __( 'Line Height in Pixel', 'sv100' );
			$setting->default_value	= 23;
			$setting->type			= 'number';
			
			$setting->init();
		}
	}