<?php
	namespace sv100;
	
	class background_fit extends settings_components {
		// Config
		private $title 			= 'Background Fit';
		private $description	= 'Defines how the background image aspect ratio behaves';
		private $type			= 'select';
		private $default_value	= 'cover';
		
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( $this->title, 'sv100' );
			$setting->description	= __( $this->description, 'sv100' );
			$setting->type			= $this->type;
			$setting->default_value	= $this->default_value;
			$setting->options		= array(
				'cover' => __( 'Cover', 'sv100' ),
				'contain' => __( 'Contain', 'sv100' )
			);
			
			$setting->init();
		}
	}