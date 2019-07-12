<?php
	namespace sv100;
	
	class background_repeat extends settings_components {
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( 'Background Repeat', 'sv100' );
			$setting->type			= 'select';
			$setting->default_value	= 'no-repeat';
			$setting->options		= array(
				'no-repeat' => __( 'No Repeat', 'sv100' ),
				'repeat' 	=> __( 'Repeat', 'sv100' ),
				'repeat-x' 	=> __( 'Repeat Horizontally', 'sv100' ),
				'repeat-y' 	=> __( 'Repeat Vertically', 'sv100' ),
				'space' 	=> __( 'Space', 'sv100' ),
				'round' 	=> __( 'Round', 'sv100' ),
				'initial' 	=> __( 'Initial', 'sv100' ),
				'inherit' 	=> __( 'Inherit', 'sv100' )
			);
			
			$setting->init();
		}
	}