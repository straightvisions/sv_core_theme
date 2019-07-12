<?php
	namespace sv100;
	
	class background_size extends settings_components {
		// Config
		private $title 			= 'Background Size';
		private $type			= 'number';
		private $default_value	= 0;
		private $placeholder	= '0 ';
		private $min			= 0;
		
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( $this->title, 'sv100' );
			$setting->description	= '<p>' . __( 'Background Size in Pixel', 'sv100' ) . '<br>
					' . __( 'If disabled Background Fit will take effect.', 'sv100' ) . '</p>
					<p><strong>' . __( '0 = Disabled', 'sv100' ) . '</strong></p>';
			$setting->type			= $this->type;
			$setting->default_value	= $this->default_value;
			$setting->placeholder	= $this->placeholder;
			$setting->min			= $this->min;
			
			$setting->init();
		}
	}