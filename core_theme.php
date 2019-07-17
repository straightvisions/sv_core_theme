<?php
namespace sv100;

/**
 * @author			straightvisions GmbH
 * @package			sv100
 * @copyright       2019 straightvisions GmbH
 * @link			https://straightvisions.com
 * @since			1.0
 * @license			See license.txt or https://straightvisions.com
 */

require_once( 'core/core.php' );

class init extends \sv_core\core {
	const version 						= 4105;
	const version_core_match 			= 4010;
	
	public static $is_child_theme 		= false;
	private $modules_registered 		= array();
	protected static $active_theme_path = '';
	protected static $parent_theme_path = '';
	protected static $active_theme_url 	= '';
	protected static $parent_theme_url 	= '';
	protected static $modules_loaded 	= array();
	protected $module_title 			= false;
	protected $module_desc 				= false;
	private $first_load					= false;
	protected static $settings_components	= false;
	
	protected static $scripts_loaded 	= false;
	protected static $theme_core_initialized			= false;
	
	public function load() {
		if(!$this->setup( __NAMESPACE__, __FILE__ . '../' )){
			return false;
		}
		
		load_theme_textdomain( 'sv100', get_template_directory() . '/languages' );
		
		$this->set_section_title( 'SV100' );
		$this->set_section_desc( 'SV100 Theme' );
		
		static::$active_theme_path = trailingslashit( get_stylesheet_directory() );
		static::$parent_theme_path = trailingslashit( get_template_directory() );
		static::$active_theme_url  = trailingslashit( get_stylesheet_directory_uri() );
		static::$parent_theme_url  = trailingslashit( get_template_directory_uri() );
		
		$this->load_core_theme_modules()->check_first_load()->init_modules();
		
		$this->wordpress_version_check( '5.0.0' );
	}
	public function wordpress_version_notice() {
		echo '<div class="error"><p>';
		/* translators: %s: Minimum required version */
		printf( __( '%1$s requires WordPress %2$s or later to function properly. Please upgrade WordPress before activating %3$s.', 'sv100' ), $this->get_section_title(),'5.0.0', $this->get_section_title() );
		echo '</p></div>';
	}
	
	public function get_active_theme_path(): string {
		return static::$active_theme_path;
	}
	
	public function get_parent_theme_path(): string {
		return static::$parent_theme_path;
	}
	
	public function get_active_theme_url(): string {
		return static::$active_theme_url;
	}
	
	public function get_parent_theme_url(): string {
		return static::$parent_theme_url;
	}
	
	public function get_modules_registered(): array {
		return $this->get_root()->modules_registered;
	}
	
	public function set_modules_registered( string $path ) {
		$this->get_root()->modules_registered[ basename( $path ) ] = $path;
	}
	
	public function get_module_title(): string {
		return $this->module_title ? $this->module_title : $this->get_module_name();
	}
	
	public function get_module_desc(): string {
		return $this->module_desc ? $this->module_desc : 'No Description defined.';
	}
	
	public function set_module_title( string $title ) {
		$this->module_title = $title;
	}
	
	public function set_module_desc( string $desc ) {
		$this->module_desc = $desc;
	}
	
	protected function load_core_theme_modules(): init {
		require_once( $this->get_path('core_theme/settings_components/settings_components.php' ));
		
		static::$settings_components = new settings_components();
		$this->get_settings_components()->set_root( $this->get_root( ));
		$this->get_settings_components()->set_parent( $this );
		$this->get_settings_components()->init();
		
		return $this;
	}
	public function get_settings_components(){
		return static::$settings_components;
	}
	
	public function get_settings_component( $suffix, $component = false, $default_value = false ) {
		if ( ! $component ) {
			return $this->get_settings_components()->get_component( $suffix, $this );
		}
		
		return $this->get_settings_components()->set_component( $suffix, $component, $default_value, $this );
	}
	
	public function init_modules(): init {
		$modules = glob( $this->get_parent_theme_path() . 'lib/modules/*' );
		
		if ( $this->get_active_theme_path() != $this->get_parent_theme_path() ) {
			$this->set_is_child_theme( true );
		}
		
		// register modules
		foreach ( $modules as $module ) {
			$this->set_modules_registered( $module );
		}
		
		$this->load_module( 'sv_modules', $this->get_parent_theme_path() . 'lib/modules/sv_modules/', trailingslashit( $this->get_parent_theme_url() . 'lib/modules/sv_modules' ) );

		$this->sv_modules
			->load_settings()
			->get_settings()['sv_modules']
			->set_title( $this->get_root()->sv_modules->get_module_title() )
			->set_description( $this->get_root()->sv_modules->get_module_desc() )
			->load_type( 'checkbox' )
			->set_disabled( true )
			->run_type()
			->set_data( 1 );
		
		foreach ( $modules as $module ) {
			$name = basename( $module );
			$path = trailingslashit( $module );
			$url  = trailingslashit( $this->get_parent_theme_url() . 'lib/modules/' . $name );
			
			if ($name != 'sv_modules') {
				if ( $this->load_module( $name, $path, $url ) ) {
					$this->sv_modules->get_settings()[ $name ]
						->set_title( $this->get_root()->$name->get_module_title() )
						->set_description( $this->get_root()->$name->get_module_desc() )
						->load_type( 'checkbox' );
				} else {
					$this->sv_modules->get_settings()[ $name ]
						->set_title( $name )
						->set_description( 'Description is available once activated.' )
						->load_type( 'checkbox' );
				}
			}
		}
		
		return $this;
	}
	
	private function load_module_check( string $name, string $path, bool $required = false ): bool {
		// Module file does not exist
		if ( ! file_exists( $path . $name . '.php' ) ) {
			return false;
		}
		
		/* required Modules */
		if($required === true){
			return true;
		}
		
		if ( $name === 'sv_modules' ) {
			return true;
		}
		
		// not set yet
		if ( isset( $this->sv_modules ) && $this->sv_modules->get_settings()[ $name ]->run_type()->get_data() === false ) {
			return true;
		}
		
		// set active
		if ( isset( $this->sv_modules ) && intval( $this->sv_modules->get_settings()[ $name ]->run_type()->get_data() ) === 1 ) {
			return true;
		}
		
		return false;
	}
	
	public function load_module( string $name, string $path, string $url, bool $required = false ): bool {
		if($this->is_module_loaded($name)){ // already loaded
			return true;
		}

		if ( $this->load_module_check( $name, $path, $required ) ) {
			require_once( $path . $name . '.php' );
			
			// Checks for child theme & child module
			$child_module = $this->get_active_theme_path() . 'lib/modules/' . $name;

			if ( $this->is_child_theme() && file_exists( $child_module ) ) {
				$child_path       = trailingslashit( $child_module );
				$child_url        = $this->get_active_theme_url() . 'lib/modules/' . $name . '/';
				$child_class_name = $this->get_name() . '_child\\' . $name;
				
				require_once( $child_path . $name . '.php' );
				
				$this->$name = new $child_class_name();
				$this->$name->set_name( $this->get_root()->get_prefix( $this->$name->get_module_name() ) );
				$this->$name->set_path( $child_path );
				$this->$name->set_url( $child_url );
			} else {
				$class_name  = $this->get_root()->get_name() . '\\' . $name;
				$this->get_root()->$name = new $class_name();
				$this->get_root()->$name->set_name( $this->get_root()->get_prefix( $this->get_root()->$name->get_module_name() ) );
				$this->get_root()->$name->set_path( $path );
				$this->get_root()->$name->set_url( $url );
			}
			
			$this->get_root()->$name->set_root( $this->get_root() );
			$this->get_root()->$name->set_parent( $this );
			$this->get_root()->$name->init();
			
			$this->set_modules_loaded($this->get_root()->$name->get_prefix(), $this->get_root()->$name);

			return true;
		} else {
			return false;
		}
	}
	
	private function set_is_child_theme( bool $value ) {
		static::$is_child_theme = $value;
	}
	
	private function is_child_theme(): bool {
		return static::$is_child_theme;
	}
	
	public function get_path( $path = '' ): string {
		if($this->get_module_name() != 'init'){
			$module			= 'modules/'.$this->get_module_name() . '/';
		}else {
			$module			= '';
		}
		
		if ( $this->is_child_theme() ) {
			$active_theme_file_path = $this->get_active_theme_path() . 'lib/'. $module.$path;
			if ( file_exists( $active_theme_file_path ) ) {
				return $active_theme_file_path;
			}
		}
		
		$root_theme_file_path = $this->get_parent_theme_path() . 'lib/' . $module . $path;
		
		return $root_theme_file_path;
	}
	
	public function get_url( $path = '' ): string {
		if ( $this->is_child_theme() ) {
			$active_theme_file_path = $this->get_active_theme_path() . 'lib/modules/' . $this->get_module_name() . '/' . $path;
			$active_theme_file_url  = $this->get_active_theme_url() . 'lib/modules/' . $this->get_module_name() . '/' . $path;
			
			if ( file_exists( $active_theme_file_path ) ) {
				return $active_theme_file_url;
			}
		}
		
		$root_theme_file_path = $this->get_parent_theme_path() . 'lib/modules/' . $this->get_module_name() . '/' . $path;
		$root_theme_file_url  = $this->get_parent_theme_url() . 'lib/modules/' . $this->get_module_name() . '/' . $path;
		
		if ( file_exists( $root_theme_file_path ) ) {
			return $root_theme_file_url;
		} else {
			// check if this is a child and files are in parent
			$root_theme_file_path = $this->get_parent_theme_path() . 'lib/modules/' . $this->get_parent()->get_module_name() . '/' . $path;
			if ( file_exists( $root_theme_file_path ) ) {
				$root_theme_file_url = $this->get_parent_theme_url() . 'lib/modules/' . $this->get_parent()->get_module_name() . '/' . $path;
				
				return $root_theme_file_url;
			} else {
				return '';
			}
		}
	}

	protected function set_modules_loaded(string $name, $object){
		$this->get_root()::$modules_loaded[$name] = $object;
	}
	protected function get_modules_loaded(): array {
		return $this->get_root()::$modules_loaded;
	}
	protected function is_module_loaded(string $name): bool{
		return isset($this->get_modules_loaded()[$this->get_root()->get_prefix($name)]) ? true : false;
	}

	public function get_module( string $name, bool $required = false ) {
		if($this->is_module_loaded($name)){
			return $this->get_modules_loaded()[$this->get_root()->get_prefix($name)];
		}

		$loaded = $this->get_root()->load_module($name, trailingslashit($this->get_root()->get_parent_theme_path() . 'lib/modules/'.$name), trailingslashit( $this->get_root()->get_parent_theme_url() . 'lib/modules/'.$name ), $required );
		
		if($loaded){
			return $this->get_root()->get_modules_loaded()[$this->get_root()->get_prefix($name) ];
		}

		return false;
	}
	
	protected function get_modules_settings(): array {
		$settings = array();
		
		foreach ( $this->get_modules_loaded() as $prefix => $module ) {
			if ( $prefix !== 'sv100_sv_settings' && ! empty( $module->s ) ) {
				$module_settings = array();
				
				foreach ( $module->s as $setting => $value ) {
					if ( isset( $value ) && ! empty( $value ) ) {
						$module_settings[ $setting ] = $value->run_type()->get_data();
					}
				}
				
				$settings[ $prefix ] = $module_settings;
			}
		}
		
		return $settings;
	}
	
	protected function get_scripts_settings(): array {
		$settings = array();
		
		foreach ( static::$scripts->get_scripts() as $script ) {
			$name				= static::$scripts->get_prefix( 'settings_' . $script->get_UID() );
			
			if ( isset( static::$scripts->s[ $script->get_UID() ] ) ) {
				$settings[ $name ] 	= static::$scripts->s[ $script->get_UID() ]->run_type()->get_data();
			}
		}
		
		return $settings;
	}
	
	private function check_first_load(): init {
		if ( ! get_option( $this->get_prefix( 'first_load' ) ) ) {
			add_option( $this->get_prefix( 'first_load' ), true );
			
			$this->first_load = true;
		}
		
		return $this;
	}
	
	protected function is_first_load() {
		return $this->get_root()->first_load;
	}
	protected function init_subcore(){
		if ( !static::$theme_core_initialized ) {
			static::$theme_core_initialized = true;
			
			add_action( 'admin_menu', array( $this , 'add_theme_page' ), 100 );
		}
	}
	public function add_theme_page(){
		\add_theme_page(
			$this->get_section_title(),		// page title
			$this->get_section_title(),		// menu title
			'manage_options',		// capability
			$this->get_prefix(),			// menu slug
			function(){	// callable function
				$this->load_page();
			}
		);
	}
}

$GLOBALS['sv100'] = new init();
$GLOBALS['sv100']->load();