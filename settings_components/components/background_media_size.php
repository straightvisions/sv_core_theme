<?php
	namespace sv100;
	
	class background_media_size extends settings_components {
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( 'Background Media Size', 'sv100' );
			$setting->type			= 'select';
			$setting->default_value	= 'large';
			$setting->options		= array_combine( get_intermediate_image_sizes(), get_intermediate_image_sizes() );
			
			$setting->init();
		}
	}