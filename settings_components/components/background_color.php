<?php
	namespace sv100;
	
	class background_color extends settings_components {
		// Config
		private $title 			= 'Background Color';
		private $type			= 'color';
		private $default_value	= '#FFFFFF';
		
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( $this->title, 'sv100' );
			$setting->default_value	= $this->default_value;
			$setting->type			= $this->type;
			
			$setting->init();
		}
	}