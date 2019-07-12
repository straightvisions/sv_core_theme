<?php
	namespace sv100;
	
	class font_size extends settings_components {
		// Config
		private $title 			= 'Font Size';
		private $description 	= 'Font Size in Pixel';
		private $type			= 'number';
		private $default_value	= 16;
		
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( $this->title, 'sv100' );
			$setting->description	= __( $this->description, 'sv100' );
			$setting->default_value	= $this->default_value;
			$setting->type			= $this->type;
			
			$setting->init();
		}
	}