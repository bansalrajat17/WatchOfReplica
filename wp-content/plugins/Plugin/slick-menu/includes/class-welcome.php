<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Slick_Menu_Welcome {

 	/**
	 * The single instance of Slick_Menu_Welcome.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;
	
	public $default_section = 'about';
	public $section = '';
	public $sections = array();

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;
 	
	public function __construct ( $parent ) {

		$this->parent = &$parent;
		$this->parent->welcome = &$this;
		
		$this->sections = array(
			'license' => esc_html('License', 'slick-menu'),
			'changelog' => esc_html__( 'Change Log', 'slick-menu' ),
			'api' => esc_html__( 'JS API', 'slick-menu' ),
			'support' => esc_html__( 'Support', 'slick-menu' ),
			'shop' => esc_html__( 'Shop', 'slick-menu' ),
		);
		
		if(!empty($_GET['section'])) {
			$this->section = esc_html($_GET['section']);
		}else{
			$this->section = key(array_slice($this->sections, 0, 1));
		}

		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ), array($this, 'action_link' ));
		add_action('admin_menu', array( &$this,'register_menu'), PHP_INT_MAX);
 
	} // end constructor
	
	function action_link( $links ) {
		
		if(!$this->parent->license()->isActivated()) {
			$link_label = esc_html__('Activate License', 'slick-menu');
			$link_style = "color: #a00;";
		}else{
			$link_label = esc_html__('License Activated', 'slick-menu');
			$link_style = "color: green;";
		}	
		
		$links[] = '<a style="'.esc_attr($link_style).'" href="'. esc_url( $this->parent->plugin_url('license') ) .'">'.$link_label.'</a>';
		return $links;
	}
		
	function register_menu() {

		foreach($this->sections as $id => $section) {
			if($id == $this->default_section) {
				add_submenu_page( $this->parent->plugin_slug(), $this->parent->plugin_name(), $section, 'read', $this->parent->plugin_slug(), array($this, 'create_dashboard'));
			}else{
	
				add_submenu_page( $this->parent->plugin_slug(), $this->parent->plugin_name(), $section, 'read', $this->parent->plugin_slug($id), function() use ($id) {
					$this->section = $id;
					$this->create_dashboard();
				});
			}	
		}
	}
	
	function create_dashboard() {
		
		include_once( $this->parent->dir.'/includes/welcome/main.php' );
	}

	function show_nav() {
		
		echo '<h2 class="nav-tab-wrapper">';
		
		foreach($this->sections as $id => $section) {
			
			$active = '';
			if($this->is_section($id)) {
				$active = ' nav-tab-active';
			}
			echo '<a href="'.$this->get_section_url($id).'" class="nav-tab'.$active.'">'.$section.'</a>';
		}
		
		echo '</h2>';
	}
	
	function show_section() {
		
		include( $this->parent->dir.'/includes/welcome/sections/'.$this->section.'.php' );
	}
	
	function is_section($section) {
		
		return $this->section === $section;
	}
		

	function get_url() {
		
		return esc_url($this->parent->plugin_url($this->default_section));
	}
	
	function get_section_id() {
		
		return $this->section;
	}
	
	function get_section_title() {
		
		return $this->sections[$this->section];
	}

	public function get_section_url($section = '', $args = array()) {
		
		if($section == $this->default_section) {
			$section = '';
		}	
		return esc_url($this->parent->plugin_admin_url($section, $args));
	}
		
	public function get_shop_products() {

		$api_url = 'http://xplodedthemes.com/api/products.php?format=html&exclude='.$this->parent->plugin_slug();
		$shop_html = $this->get_remote_content($api_url);
		
		return $shop_html;
	}

	private function get_remote_content( $url, $json_decode = false ) {
		
		$cache_key = md5($url);
		
		$content = get_site_transient( $cache_key );

		if ( $content === false || !empty($_GET['nocache']) !== null ) {
	
			$response = wp_remote_get( $url, array( 'sslverify' => false ) );

			// Stop here if the is an error.
			if ( is_wp_error( $response ) ) {
				
				$content = '';
				
				// Set temporary transient.
				set_site_transient($cache_key, $content, MINUTE_IN_SECONDS );
				
			}else{
		
				// Retrieve data from the body and decode json format.
				$content = wp_remote_retrieve_body( $response );

				set_site_transient($cache_key, $content, DAY_IN_SECONDS );
			}	
	
		}
	
		if($json_decode) {
			$content = json_decode($content , true );
		}
				
		return $content;
	}	
	
	/**
	 * Slick_Menu_Welcome Instance
	 *
	 * Ensures only one instance of Slick_Menu_Welcome is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Slick_Menu()
	 * @return Slick_Menu_Welcome instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->plugin_version() );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->plugin_version() );
	} // End __wakeup()	 	
}
