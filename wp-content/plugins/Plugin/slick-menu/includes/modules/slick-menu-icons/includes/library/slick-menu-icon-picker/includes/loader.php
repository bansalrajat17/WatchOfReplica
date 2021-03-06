<?php

/**
 * SM Icon Picker Loader
 *
 * @package SM_Icon_Picker
 * 
 */

final class SM_Icon_Picker_Loader {

	/**
	 * SM_Icon_Picker_Loader singleton
	 *
	 * @static
	 * @since  0.1.0
	 * @access protected
	 * @var    SM_Icon_Picker_Loader
	 */
	protected static $instance;

	/**
	 * Script IDs
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $script_ids = array();

	/**
	 * Stylesheet IDs
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $style_ids = array();

	/**
	 * Printed media templates
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $printed_templates = array();


	/**
	 * Setter magic
	 *
	 * @since  0.1.0
	 * @param  string $name Property name.
	 * @return bool
	 */
	public function __isset( $name ) {
		return isset( $this->$name );
	}


	/**
	 * Getter magic
	 *
	 * @since  0.1.0
	 * @param  string $name Property name.
	 * @return mixed  NULL if attribute doesn't exist.
	 */
	public function __get( $name ) {
		if ( isset( $this->$name ) ) {
			return $this->$name;
		}

		return null;
	}


	/**
	 * Get instance
	 *
	 * @since  0.1.0
	 * @return SM_Icon_Picker_Loader
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Constructor
	 *
	 * @since  0.1.0
	 * @access protected
	 */
	protected function __construct() {
		$this->register_assets();
		
		add_filter( 'media_view_strings', array( $this, '_media_view_strings' ) );

		/**
		 * Fires when SM Icon Picker loader is ready
		 *
		 * @since 0.1.0
		 * @param SM_Icon_Picker_Loader $this SM_Icon_Picker_Loader instance.
		 */
		do_action( 'slick_menu_icon_picker_loader_init', $this );
	}


	/**
	 * Add script
	 *
	 * @since  0.1.0
	 * @param  string $script_id Script ID.
	 * @return bool
	 */
	public function add_script( $script_id ) {
		if ( wp_script_is( $script_id, 'registered' ) ) {
			$this->script_ids[] = $script_id;

			return true;
		}

		return false;
	}


	/**
	 * Add stylesheet
	 *
	 * @since  0.1.0
	 * @param  string $stylesheet_id Stylesheet ID.
	 * @return bool
	 */
	public function add_style( $stylesheet_id ) {

			wp_enqueue_style( 'dashicons' );
		if ( wp_style_is( $stylesheet_id, 'registered' ) ) {
			$this->style_ids[] = $stylesheet_id;

			return true;
		}

		return false;
	}


	/**
	 * Register scripts & styles
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return void
	 */
	protected function register_assets() {
		$slick_menu_icon_picker = SM_Icon_Picker::instance();
		$suffix      = ( defined( 'SM_SCRIPT_DEBUG' ) && SM_SCRIPT_DEBUG ) ? '' : '.min';

		wp_register_script(
			'slick-menu-icon-picker',
			"{$slick_menu_icon_picker->url}/js/icon-picker{$suffix}.js",
			array( 'media-views' ),
			$slick_menu_icon_picker->version,
			true
		);
		$this->add_script( 'slick-menu-icon-picker' );

		wp_register_style(
			'slick-menu-icon-picker',
			"{$slick_menu_icon_picker->url}/css/icon-picker{$suffix}.css",
			false,
			$slick_menu_icon_picker->version
		);
		$this->add_style( 'slick-menu-icon-picker' );
	}


	/**
	 * Load admin functionalities
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function load() {
		$slick_menu_icon_picker = SM_Icon_Picker::instance();

		if ( ! is_admin() ) {
			_doing_it_wrong(
				__METHOD__,
				'It should only be called on admin pages.',
				esc_html( $slick_menu_icon_picker->version )
			);

			return;
		}

		if ( ! did_action( 'slick_menu_icon_picker_loader_init' ) ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					'It should not be called until the %s hook.',
					'<code>slick_menu_icon_picker_loader_init</code>'
				),
				esc_html( $slick_menu_icon_picker->version )
			);

			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, '_enqueue_assets' ) );
		add_action( 'print_media_templates', array( $this, '_media_templates' ) );
		
	}


	/**
	 * Filter media view strings
	 *
	 * @since   0.1.0
	 * @wp_hook filter media_view_strings
	 *
	 * @param  array  $strings Media view strings.
	 *
	 * @return  array
	 */
	public function _media_view_strings( $strings ) {
		$strings['iconPicker'] = array(
			'frameTitle' => __( 'SM Icon Picker', 'slick-menu-icon-picker' ),
			'allFilter'  => __( 'All', 'slick-menu-icon-picker' ),
			'selectIcon' => __( 'Select Icon', 'slick-menu-icon-picker' ),
		);

		return $strings;
	}


	/**
	 * Enqueue scripts & styles
	 *
	 * @since   0.1.0
	 * @wp_hook action admin_enqueue_scripts
	 * @return  void
	 */
	public function _enqueue_assets() {
		$slick_menu_icon_picker = SM_Icon_Picker::instance();

		wp_localize_script(
			'slick-menu-icon-picker',
			'iconPicker',
			array(
				'types' => $slick_menu_icon_picker->registry->get_types_for_js(),
			)
		);

		// Some pages don't call this by default, so let's make sure.
		wp_enqueue_media();

		foreach ( $this->script_ids as $script_id ) {
			wp_enqueue_script( $script_id );
		}

		foreach ( $this->style_ids as $style_id ) {
			wp_enqueue_style( $style_id );
		}

		/**
		 * Fires when admin functionality is loaded
		 *
		 * @since 0.1.0
		 * @param SM_Icon_Picker $slick_menu_icon_picker SM_Icon_Picker instance.
		 */
		do_action( 'slick_menu_icon_picker_admin_loaded', $slick_menu_icon_picker );
	}


	/**
	 * Media templates
	 *
	 * @since   0.1.0
	 * @wp_hook action print_media_templates
	 * @return  void
	 */
	public function _media_templates() {
		$slick_menu_icon_picker = SM_Icon_Picker::instance();

		foreach ( $slick_menu_icon_picker->registry->types as $type ) {
			if ( empty( $type->templates ) ) {
				continue;
			}

			$template_id_prefix = "tmpl-slick-menu-iconpicker-{$type->template_id}";
			if ( in_array( $template_id_prefix, $this->printed_templates ) ) {
				continue;
			}

			foreach ( $type->templates as $template_id_suffix => $template ) {
				$this->_print_template( "{$template_id_prefix}-{$template_id_suffix}", $template );
			}

			$this->printed_templates[] = $template_id_prefix;
		}

		/**
		 * Fires after all media templates have been printed
		 *
		 * @since 0.1.0
		 * @param SM_Icon_Picker $slick_menu_icon_picker SM Icon Picker instance.
		 */
		do_action( 'slick_menu_icon_picker_print_media_templates', $slick_menu_icon_picker );
	}


	/**
	 * Print media template
	 *
	 * @since  0.1.0
	 * @param  string $id       Template ID.
	 * @param  string $template Media template HTML.
	 * @return void
	 */
	protected function _print_template( $id, $template ) {
		?>
			<script type="text/html" id="<?php echo esc_attr( $id ) ?>">
				<?php echo $template; // xss ok ?>
			</script>
		<?php
	}
}
