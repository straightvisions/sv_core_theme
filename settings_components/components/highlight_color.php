<?php
	namespace sv100;
	
	class highlight_color extends settings_components {
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( 'Highlight Color', 'sv100' );
			$setting->description	= __( 'This color is used for highlighting elements, like links on hover/focus.', 'sv100' );
			$setting->default_value	= '#358ae9';
			$setting->type			= 'color';
			
			$setting->init();
		}
	}