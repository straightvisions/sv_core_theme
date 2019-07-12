<?php
	namespace sv100;
	
	class background_attachment extends settings_components {
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( 'Background Attachment', 'sv100' );
			$setting->type			= 'select';
			$setting->default_value	= 'fixed';
			$setting->options		= array(
				'fixed' 	=> __( 'Fixed', 'sv100' ),
				'scroll' 	=> __( 'Scroll', 'sv100' ),
				'local' 	=> __( 'Local', 'sv100' ),
				'initial' 	=> __( 'Initial', 'sv100' ),
				'inherit' 	=> __( 'Inherit', 'sv100' )
			);
			
			$setting->init();
		}
	}