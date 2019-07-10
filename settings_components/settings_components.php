<?php
	namespace sv100;
	
	class settings_components extends init {
		protected static $components = array();
		
		public function init() {
			$this->load_components();
		}
		
		public function get_component( $suffix, $module ) {
			if ( ! isset( $module->s[ $suffix ] ) ) return false;

			return $module->s[ $suffix ];
		}
		
		public function set_component( $suffix, $component, $default_value, $module ) {
			if ( ! isset( static::$components[ $component ] ) ) return false;
			
			return static::$components[ $component ]->create( $suffix, $default_value, $module );
		}
		
		private function load_components(): settings_components {
			$components = glob( $this->get_parent_theme_path() . 'lib/core_theme/settings_components/components/*.php'  );
			
			foreach ( $components as $path ) {
				require_once( $path );
				
				$component 	= str_replace( '.php', '', basename( $path ) );
				$classname 	= $this->get_root()->get_name() . '\\' . $component;
				
				$this->get_root()->$component = new $classname();
				$this->get_root()->$component->set_root( $this->get_root() );
				$this->get_root()->$component->set_parent( $this );
				$this->get_root()->$component->init();
			}
			
			return $this;
		}
	}
	
	class component extends settings_components {
		public $name 			= '';
		public $title 			= '';
		public $description 	= '';
		public $type 			= '';
		public $default_value 	= false;
		public $placeholder 	= '';
		public $options 		= array();
		public $min				= false;
		public $max				= false;
		
		public function init() {
			static::$components[ $this->name ] = $this;
		}
		
		public function create( $suffix, $default_value, $module ) {
			$module->s[ $suffix ] = $module->get_setting()->set_ID( $suffix )->load_type( $this->type );
			
			if ( $this->title ) {
				$module->s[ $suffix ]->set_title( $this->title );
			}
			
			if ( $this->description ) {
				$module->s[ $suffix ]->set_description( $this->description );
			}
			
			if ( $this->default_value !== false ) {
				$module->s[ $suffix ]->set_default_value( $this->default_value );
			}
			
			if ( $default_value !== false ) {
				$module->s[ $suffix ]->set_default_value( $default_value );
			}
			
			if ( $this->options ) {
				$module->s[ $suffix ]->set_options( $this->options );
			}
			
			if ( $this->placeholder ) {
				$module->s[ $suffix ]->set_placeholder( $this->placeholder );
			}
			
			if ( $this->min !== false ) {
				$module->s[ $suffix ]->set_min( $this->min );
			}
			
			if ( $this->max !== false ) {
				$module->s[ $suffix ]->set_max( $this->max );
			}

			return $module->s[ $suffix ];
		}
	}