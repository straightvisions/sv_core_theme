<?php
namespace sv100;

require_once( 'core/core.php' );

class init extends \sv_core\core {
	const version 								= 2000; // should match version in style.css and readme.txt
	const version_core_match 					= 10000;

	public static $is_child_theme 				= false;
	private $modules_registered 				= array();
	protected static $active_theme_path 		= '';
	protected static $parent_theme_path 		= '';
	protected static $active_theme_url 		= '';
	protected static $parent_theme_url 		= '';
	protected $module_title 					= false;
	protected $module_desc 						= false;

	private $first_load							= false;
	protected static $settings_components		= false;

	protected static $scripts_loaded 			= false;
	protected static $theme_core_initialized	= false;
	protected $has_sidebar						= false;

	protected $metaboxes						= false;

	public function init() {
		if(!$this->setup( __NAMESPACE__, __FILE__ . '../' )){
			return false;
		}

		if(get_template_directory() !== get_stylesheet_directory()){
			load_theme_textdomain( 'sv100', get_stylesheet_directory() . '/languages' );
		}

		load_theme_textdomain( 'sv100', get_template_directory() . '/languages' );

		$this->set_section_title( __( 'SV100', 'sv100' ) )
		->set_section_desc( __( 'SV100 Theme', 'sv100' ) )
		->set_section_type('')
			->set_section_privacy( '<p>' . $this->get_section_title() . __(' does not collect or share any data',  'sv100_companion').'</p>' );

		static::$active_theme_path = trailingslashit( get_stylesheet_directory() );
		static::$parent_theme_path = trailingslashit( get_template_directory() );
		static::$active_theme_url  = trailingslashit( get_stylesheet_directory_uri() );
		static::$parent_theme_url  = trailingslashit( get_template_directory_uri() );

		$this->check_first_load()->init_modules();

		$this->wordpress_version_check( '6.0' );

		$this->theme_json_init();
	}

	public function wordpress_version_notice() {
		echo '<div class="error"><p>';
		/* translators: %s: Minimum required version */
		printf(
			__( '%1$s requires WordPress %2$s or later to function properly.
			Please upgrade WordPress before activating %3$s.', 'sv100' ),
			$this->get_section_title(),'6.0', $this->get_section_title()
		);
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
		return $this->module_desc ? $this->module_desc : __( 'No Description defined.', 'sv100' );
	}

	public function set_module_title( string $title ) {
		$this->module_title = $title;

		return $this;
	}

	public function set_module_desc( string $desc ) {
		$this->module_desc = $desc;

		return $this;
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

		$this->load_module(
			'sv_modules',
			$this->get_parent_theme_path() . 'lib/modules/sv_modules/',
			trailingslashit( $this->get_parent_theme_url() . 'lib/modules/sv_modules' ),
			true
		);

		$this->sv_modules
			->load_settings()
			->get_settings()['sv_modules']
			->set_title( $this->get_root()->sv_modules->get_module_title() )
			->set_description( $this->get_root()->sv_modules->get_module_desc() )
			->load_type( 'checkbox' )
			->set_disabled( true )
			->set_data( 1 );

		foreach ( $modules as $module ) {
			$name = basename( $module );
			$path = trailingslashit( $module );
			$url  = trailingslashit( $this->get_parent_theme_url() . 'lib/modules/' . $name );

			if ($name != 'sv_modules') {
				if ( $this->load_module( $name, $path, $url ) ) {
					$this->sv_modules->get_setting( $name )
						->set_title( $this->get_root()->$name->get_module_title() )
						->set_description( $this->get_root()->$name->get_module_desc() )
						->load_type( 'checkbox' );
				} else {
					$this->sv_modules->get_setting( $name )
						->set_title( $name )
						->set_description( __( 'Description is available once activated.', 'sv100' ) )
						->load_type( 'checkbox' );
				}
			}
		}

		return $this;
	}

	private function load_module_check( string $name, string $path, bool $required = false ): bool {
		// Module file does not exist
		if ( ! is_file( $path . $name . '.php' ) ) {
			error_log(__('Tried to load theme module which does not exist: ', 'sv100').$path . $name . '.php');
			return false;
		}

		/* required Modules */
		if($required === true){
			//error_log(__('Required Module loaded: ', 'sv100').$path . $name . '.php');
			return true;
		}

		// not set yet
		if ( isset( $this->sv_modules ) && $this->sv_modules->get_setting( $name )->get_data() === false ) {
			$this->sv_modules->get_setting( $name )->set_data(1);
			//error_log(__('Module without Activation-Setting loaded: ', 'sv100').$path . $name . '.php');
			return true;
		}

		// set active
		if ( isset( $this->sv_modules ) && intval( $this->sv_modules->get_setting( $name )->get_data() ) === 1 ) {
			//error_log(__('Active Module loaded: ', 'sv100').$path . $name . '.php');
			return true;
		}

		//error_log(__('Tried to load theme module which is disabled: ', 'sv100').$path . $name . '.php');

		return false;
	}

	public function load_module( string $name, string $path, string $url, bool $required = false ): bool {
		if($this->is_module_loaded($name)){ // already loaded
			return true;
		}

		if ( $this->load_module_check( $name, $path, $required ) ) {
			require_once( $path . $name . '.php' );

			// Checks for child theme
			$child_module = $this->get_active_theme_path() . 'lib/modules/' . $name;

			if ( $this->is_child_theme() && file_exists( $child_module ) ) {
				$child_path	   = trailingslashit( $child_module );
				$child_url		= $this->get_active_theme_url() . 'lib/modules/' . $name . '/';
				$child_class_name = $this->get_name() . '_child\\' . $name;

				require_once( $child_path . $name . '.php' );

				$this->$name = new $child_class_name();
				$this->$name
					->set_name( $this->get_root()->get_prefix( $this->$name->get_module_name() ) )
					->set_path( $child_path )
					->set_url( $child_url );

				add_action('wp', array($this->$name,'enqueue_scripts'));
				add_action('admin_init', array($this->$name,'enqueue_scripts'));

			} else {
				$class_name  = $this->get_root()->get_name() . '\\' . $name;
				$this->get_root()->$name = new $class_name();
				$this->get_root()->$name
					->set_name( $this->get_root()->get_prefix( $this->get_root()->$name->get_module_name() ) )
					->set_path( $path )
					->set_url( $url );

				add_action('wp', array($this->get_root()->$name,'enqueue_scripts'));
				add_action('admin_init', array($this->get_root()->$name,'enqueue_scripts'));
			}

			$this->get_root()->$name
				->set_root( $this->get_root() )
				->set_parent( $this )
				->init();

			$this->set_modules_loaded($this->get_root()->$name->get_prefix(), $this->get_root()->$name);

			// load scripts in admin and site editor
			if(is_admin()) {
				$this->get_root()->$name->init_admin();
			}else{
				add_action( 'current_screen', function() use ($name){
					if(get_current_screen() && get_current_screen()->base === 'site-editor'){
						$this->get_root()->$name->init_admin();
					}
				} );
			}

			return true;
		} else {
			return false;
		}
	}
	protected function init_admin() {
		$this->get_module($this->get_module_name())->load_settings()->register_scripts();
		foreach($this->get_scripts() as $script){
				$script->set_is_enqueued();
		}
	}
	protected function load_settings(){
		return $this;
	}
	protected function register_scripts(){
		if($this->get_css_cache_active()) {
			// Register Styles
			$this->get_script('config')
				->set_path('lib/css/config/init.php')
				->set_is_gutenberg()
				->set_is_enqueued();

			// if styles are enqueued inline, currently no check is possible to detect if they are actually in use for current page
			/*if(!is_admin()) {
				$this->get_script( 'config' )->set_inline();

				if(strlen($this->get_block_handle()) > 0){
					add_action( 'wp_enqueue_scripts', function(){ wp_dequeue_style( $this->get_block_handle() ); });
				}
			}*/

			if(strlen($this->get_block_handle())){
				$this->get_script('config')
				     ->set_ID($this->get_block_handle())
				     ->set_is_no_prefix();
			}

			$this->get_script('default')
				->set_path('lib/css/common/default.css')
				->set_inline()
				->set_is_gutenberg()
				->set_is_enqueued();

			$this->get_script('common')
				->set_path('lib/css/common/common.css')
				->set_inline()
				->set_is_gutenberg()
				->set_is_enqueued();
		}

		return $this;
	}
	public function enqueue_scripts() {
		if(!is_admin()){
			$this->load_settings()->register_scripts();
		}

		foreach($this->get_scripts() as $script){
			if(
				strlen($this->get_block_handle()) > 0
				&& $script->get_ID() != $this->get_block_handle()
				&& $script->get_ID() != 'common'
				&& $script->get_ID() != 'default'
			){
				$script->set_deps(array($this->get_script('config')->get_handle()));
			}

			$script->set_is_enqueued();
		}

		return $this;
	}

	private function set_is_child_theme( bool $value ) {
		static::$is_child_theme = $value;
	}

	private function is_child_theme(): bool {
		return static::$is_child_theme;
	}

	public function get_path( string $suffix = ''): string {
		if($this->get_module_name() != 'init'){
			$module			= 'modules/'.$this->get_module_name() . '/';
		}else {
			$module			= '';
		}

		if ( $this->is_child_theme() ) {
			$active_theme_file_path = $this->get_active_theme_path() . 'lib/'. $module.$suffix;

			if ( file_exists( $active_theme_file_path ) ) {
				return $active_theme_file_path;
			}
		}

		$root_theme_file_path = $this->get_parent_theme_path() . 'lib/' . $module . $suffix;

		return $root_theme_file_path;
	}

	public function get_url( string $suffix = ''): string {
		if ( $this->is_child_theme() ) {
			$active_theme_file_path = $this->get_active_theme_path() . 'lib/modules/' . $this->get_module_name() . '/' . $suffix;
			$active_theme_file_url  = $this->get_active_theme_url() . 'lib/modules/' . $this->get_module_name() . '/' . $suffix;

			if ( file_exists( $active_theme_file_path ) ) {
				return $active_theme_file_url;
			}
		}

		$root_theme_file_path = $this->get_parent_theme_path() . 'lib/modules/' . $this->get_module_name() . '/' . $suffix;
		$root_theme_file_url  = $this->get_parent_theme_url() . 'lib/modules/' . $this->get_module_name() . '/' . $suffix;

		if ( file_exists( $root_theme_file_path ) ) {
			return $root_theme_file_url;
		} else {
			// check if this is a child and files are in parent
			$root_theme_file_path = $this->get_parent_theme_path() . 'lib/modules/' . $this->get_parent()->get_module_name() . '/' . $suffix;
			if ( file_exists( $root_theme_file_path ) ) {
				$root_theme_file_url = $this->get_parent_theme_url() . 'lib/modules/' . $this->get_parent()->get_module_name() . '/' . $suffix;

				return $root_theme_file_url;
			} else {
				return '';
			}
		}
	}

	public function get_module( string $name, bool $required = false ) {
		if($this->is_module_loaded($name)){
			return $this->get_modules_loaded()[$this->get_root()->get_prefix($name)];
		}

		$loaded = $this->get_root()
					   ->load_module(
					   		$name,
							trailingslashit( $this->get_root()->get_parent_theme_path() . 'lib/modules/'.$name ),
							trailingslashit( $this->get_root()->get_parent_theme_url() . 'lib/modules/'.$name ),
							$required
					   );

		if($loaded){
			return $this->get_root()->get_modules_loaded()[$this->get_root()->get_prefix($name) ];
		}

		return false;
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

			do_action('sv100_init');
		}
	}
	public function has_sidebar(): bool{
		return $this->has_sidebar;
	}
	public function show_part(string $field): bool {
		global $post;

		if(!$post){
			return false;
		}

		$setting = $this->metaboxes->get_data( $post->ID, $this->get_prefix('show_'.$field), $this->get_setting( 'show_'.$field )->get_data() );

		// global settings allow post type based selection and are arrays
		if(is_array($setting)){
			// check for current post type
			if(isset($setting[get_post_type()])){
				$value = boolval($setting[get_post_type()]);
			}else{
				$value = boolval($setting['post']); // post type not found in settings, use post-setting instead as fallback
			}
		}else{
			$value = $setting;
		}

		return $value;
	}
	public function get_metabox_data(string $field): string {
		global $post;

		if ( is_404() ) {
			$post = get_post( $this->get_module('sv_content')->get_setting( '404_page' )->get_data() );
			setup_postdata($post);
		}

		if(!$post){
			return false;
		}

		$setting = $this->metaboxes->get_data( $post->ID, $this->get_prefix($field), $this->get_setting( $field )->get_data() );

		return strval($setting);
	}
	public function get_metabox_data_by_post_type(string $field): string {
		global $post;

		if ( is_404() ) {
			$post = get_post( $this->get_module('sv_content')->get_setting( '404_page' )->get_data() );
			setup_postdata($post);
		}

		if(!$post){
			return false;
		}

		$setting = $this->metaboxes->get_data( $post->ID, $this->get_prefix($field), $this->get_setting( $field.'_'.get_post_type() )->get_data() );

		return strval($setting);
	}
	public function theme_json_get_custom_path(){
		return wp_upload_dir()['basedir'].'/straightvisions/cache/'.$this->get_root()->get_prefix().'/theme.json';
	}
	public function theme_json_init(){
		$file   = $this->theme_json_get_custom_path();
		$path   = pathinfo($file, PATHINFO_DIRNAME);

		if(is_admin()){
			// create directories of not exist
			if (!is_dir($path)) {
				// dir doesn't exist, make it
				mkdir($path, 0755, true);
			}

			// copy original file if not exists
			if (!file_exists($file)) {
				copy( $this->get_root()->get_path( '../theme.json' ), $file );
			}
		}
	}
	public function theme_json_get_data(): array{
		return json_decode(file_get_contents($this->theme_json_get_custom_path()), true);
	}
	public function theme_json_update(){
		$new     = $this->theme_json_update_data();
		if($this->theme_json_get_data() !== $new){
			file_put_contents($this->theme_json_get_custom_path(), json_encode($new));
			copy( $this->theme_json_get_custom_path(), $this->get_active_theme_path().'theme.json' );
		}

		return $this;
	}
	public function theme_json_update_data(){
		// override this in child module with new data
		return $this->theme_json_get_data();
	}
}

$sv100 = new init();
$sv100->init();