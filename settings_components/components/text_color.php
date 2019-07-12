<?php
	namespace sv100;
	
	class text_color extends settings_components {
		// Config
		private $title 			= 'Text Color';
		private $type			= 'color';
		private $default_value	= '#000000';
		
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