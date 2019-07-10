<?php
	namespace sv100;
	
	class font_family extends settings_components {
		// Config
		private $title 			= 'Font Family';
		private $description 	= 'Choose a font for your text';
		private $type			= 'select';
		
		// Creates the component and adds it to the components array
		public function init() {
			$setting 				= new component();
			$setting->set_root( $this->get_root() );
			$setting->set_parent( $this );
			
			$setting->name 			= str_replace( 'sv100\\', '', get_class() );
			$setting->title			= __( $this->title, 'sv100' );
			$setting->description	= __( $this->description, 'sv100' );
			$setting->type			= $this->type;
			$setting->options		= $this->get_fonts();
			
			$setting->init();
		}
		
		// Returns all font's in SV WebfontLoader
		private function get_fonts(): array {
			$fonts			= array( '' => __( 'choose...', 'sv100' ) );
			$font_array 	= $this->get_module( 'sv_webfontloader') ? $this->get_module( 'sv_webfontloader')->get_setting( 'fonts' )->run_type()->get_data() : '';
			
			if ( $font_array ) {
				foreach( $font_array as $font ) {
					$fonts[ $font['entry_label'] ]		= $font['entry_label'];
				}
			}
			
			return $fonts;
		}
	}