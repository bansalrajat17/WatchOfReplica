<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Slick_Menu_Cache {

 	/**
	 * The single instance of Slick_Menu_Cache.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;
	
 	public $tries = array();
 	public $missed = array();
 	public $repeated = array();
 	
	public function __construct ( $parent ) {

		$this->parent = &$parent;
		$this->parent->cache = &$this;
		
		$this->cache_prefix = 'smcache-';
		
		if($this->parent->lang->enabled()) {
			$this->cache_prefix .= $this->parent->lang->get_current_language().'-';
		}
	
		if(get_current_user_id() !== 0) {
			$this->cache_prefix .= get_current_user_id().'-';
		}
			
		if($this->parent->print_stats && !defined('DOING_AJAX')) {
			
			add_action('shutdown', array($this, 'stats'));
		
		}

	}	
			
 	
 	public function set($id, $val) {

 		$key = $this->get_key($id);
 		
 		$this->missed[$key] = array($id, $key);
 		
	 	wp_cache_set($key, $val);
	 	
 	}
 	
 	public function get($id) {
	 	
	 	$key = $this->get_key($id);
	 	
 		$this->tries[$key] = array($id, $key);

	 	$value = wp_cache_get( $key );

	 	return $value;
	 	
 	}
 	
 	public function delete($id) {

	 	$key = $this->get_key($id);
	 	wp_cache_delete($key);
	 	
 	}
 	

	public function get_key($id) {
		
		$key = $id;
		
		if(!is_string($key)) {
			$key = serialize($key);
		}
		
		return $this->cache_prefix.md5($key);
	}


	function stats() {
		
		echo '<div class="slick-menu-debug-box slick-menu-debug-cache-stats" style="margin: 20px 0;">';
		echo '<label>Cache Stats</label><br>';
		echo '<strong>cached_objects:</strong><br><pre>'.print_r(array_diff_key($this->tries, $this->missed), true).'</pre>';
		echo '<br><strong>uncached_objects:</strong><br><pre>'.print_r($this->missed, true).'</pre>';
		echo '</div>';
	}


	/**
	 * Slick_Menu_Cache Instance
	 *
	 * Ensures only one instance of Slick_Menu_Cache is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Slick_Menu()
	 * @return Slick_Menu_Cache instance
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
