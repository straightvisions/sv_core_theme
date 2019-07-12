<?php
	namespace sv100;
	
	class text_decoration extends settings_components {
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( 'Text Decoration', 'sv100' );
			$setting->default_value	= 'none';
			$setting->type			= 'select';
			$setting->options		= array(
				'none'			=> __( 'None', 'sv100' ),
				'underline'		=> __( 'Underline', 'sv100' ),
				'line-through'	=> __( 'Line Through', 'sv100' ),
				'overline'		=> __( 'Overline', 'sv100' ),
			);
			
			$setting->init();
		}
	}