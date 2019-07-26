<?php
	namespace sv100;
	
	class position extends settings_components {
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( 'Position', 'sv100' );
			$setting->type			= 'select';
			$setting->default_value	= 'static';
			$setting->options		= array(
				'static'	=> __( 'static', 'sv100' ),
				'fixed'		=> __( 'fixed', 'sv100' ),
				'sticky'	=> __( 'sticky', 'sv100' )
			);
			
			$setting->init();
		}
	}