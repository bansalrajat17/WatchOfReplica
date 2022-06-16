<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Slick_Menu_PCache {

 	/**
	 * The single instance of Slick_Menu_PCache.
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
		$this->parent->pcache = &$this;
		
		$this->pcache_flush_prefix = 'smcache-';
		$this->pcache_prefix = 'smcache-';
		
		if($this->parent->lang->enabled()) {
			$this->pcache_prefix .= $this->parent->lang->get_current_language().'-';
		}
		
		if(get_current_user_id() !== 0) {
			$this->pcache_prefix .= get_current_user_id().'-';
		}
		
		if($this->parent->print_stats && !defined('DOING_AJAX')) {
			
			add_action('shutdown', array($this, 'stats'));
		
		}
		
		add_action( 'sm_rwmb_after_save_post',  array($this, 'flush' ), 10, 1 );
		add_action('wp_update_nav_menu', array($this, 'flush' ), 10, 1 );
		add_action('admin_menu', array( &$this,'register_menu'), PHP_INT_MAX);
	}	
		
	public function register_menu() {
				
		add_submenu_page( 
			$this->parent->plugin_slug(), 
			$this->parent->plugin_name(), 
			esc_html__('Flush Cache', 'slick-menu'), 
			'read', 
			$this->parent->plugin_slug('flush-cache'), 
			array( &$this,'flush_cache_page') 
		);
	}	
	
	public function flush_cache_page() {
		
		$this->flush();
		?>
		
		<div style="display:table; width:100%;height:90vh;font-size:40px;line-height:1.3;color:#181818;font-weight: 200;text-align:center;">
			<div style="display:table-cell; vertical-align: middle;">
				<?php echo sprintf(esc_html__('%sSlick Menu%s Cache flushed Successfully', 'slick-menu'), '<strong>', '</strong>'); ?><br>
				<?php echo esc_html__('Redirecting, please wait...', 'slick-menu'); ?>
				<meta http-equiv="refresh" content="2; url=<?php echo $this->parent->plugin_url();?>">
			</div>
		</div>
		
		<?php
	}
		 	
 	public function set($id, $val) {

 		$key = $this->get_key($id);
 		
 		$this->missed[$key] = array($id, $key);
 		
	 	if($this->parent->sm_debug)
	 		return false;

	 	update_option($key, $val);
 	}
 	
 	public function get($id) {
	 	
	 	$key = $this->get_key($id);
 		
 		$this->tries[$key] = array($id, $key);

	 	if($this->parent->sm_debug)
	 		return false;
	 	
	 	$value = wp_cache_get( $key );

	 	if($value === false) {

		 	$value = get_option($key);
		 	wp_cache_set( $key, $value );
	 	}
	 	
	 	return $value;
	 	
 	}
 	
 	public function delete($id) {

	 	$key = $this->get_key($id);
	 	
 	}
 	

	public function get_key($id) {
		
		$key = $id;
		
		if(!is_string($key)) {
			$key = serialize($key);
		}
		
		return $this->pcache_prefix.md5($key);
	}

	public function flush() {

		global $wpdb;
		
		$key_search = $this->pcache_flush_prefix;
		
		$query = $wpdb->prepare( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE (%s)", $key_search.'%' );

		$wpdb->query($query);	
		
		$this->flush_w3_total_cache();
		$this->flush_wp_super_cache();
	}
	
	function flush_w3_total_cache(){
		
		//clear W3TC page cache
		if(function_exists('w3tc_pgcache_flush'))
			w3tc_pgcache_flush();
	
		//clear W3TC database cache (comment out if not using)
		if(function_exists('w3tc_dbcache_flush'))
			w3tc_dbcache_flush();
	
		//clear W3TC object cache
		if(function_exists('w3tc_objectcache_flush'))
			w3tc_objectcache_flush();
	
		//clear W3TC minify cache
		if(function_exists('w3tc_minify_flush'))
			w3tc_minify_flush();
	}
	
	function flush_wp_super_cache() {
		
		if(function_exists('wp_cache_clear_cache')) {
			
			global $wpdb;
			wp_cache_clear_cache( $wpdb->blogid );
		}
	}

	function stats() {
		
		echo '<div class="slick-menu-debug-box slick-menu-debug-cache-stats" style="margin: 20px 0;">';
		echo '<label>Persistent Cache Stats</label><br>';
		echo '<strong>cached_objects:</strong><br><pre>'.print_r(array_diff_key($this->tries, $this->missed), true).'</pre>';
		echo '<br><strong>uncached_objects:</strong><br><pre>'.print_r($this->missed, true).'</pre>';
		echo '</div>';
	}


	/**
	 * Slick_Menu_PCache Instance
	 *
	 * Ensures only one instance of Slick_Menu_PCache is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Slick_Menu()
	 * @return Slick_Menu_PCache instance
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
