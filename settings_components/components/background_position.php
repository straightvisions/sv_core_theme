<?php
	namespace sv100;
	
	class background_position extends settings_components {
		// Config
		private $title 			= 'Background Position';
		private $type			= 'text';
		private $default_value	= 'center top';
		private $placeholder	= 'center top';
		
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( $this->title, 'sv100' );
			$setting->type			= $this->type;
			$setting->default_value	= $this->default_value;
			$setting->placeholder	= $this->placeholder;
			
			$setting->init();
		}
	}