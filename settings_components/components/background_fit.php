<?php
	namespace sv100;
	
	class background_fit extends settings_components {
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( 'Background Fit', 'sv100' );
			$setting->description	= __( 'Defines how the background image aspect ratio behaves', 'sv100' );
			$setting->type			= 'select';
			$setting->default_value	= 'cover';
			$setting->options		= array(
				'cover' => __( 'Cover', 'sv100' ),
				'contain' => __( 'Contain', 'sv100' )
			);
			
			$setting->init();
		}
	}