<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Slick_Menu_Migration {

 	/**
	 * The single instance of Slick_Menu_Migration.
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

	public function __construct ( $parent ) {

		$this->parent = &$parent;
		$this->parent->migration = &$this;
		
		add_action('admin_init', array($this, 'upgrade'), 10);
	}	
			

	function upgrade() {
		
		global $wpdb, $wp_filesystem; 
	
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		
		$version_option_key = $this->parent->plugin_slug('version');
	
		$old_version = get_option( $version_option_key, $this->parent->plugin_version() ); // false
		$new_version = $this->parent->plugin_version(); 
	
		if ( $new_version !== $old_version )
		{
	
			/*
			 * 1.0.3.4
			 */
	
	
			if ( $old_version < '1.0.3.4' )
			{
				$this->parent->move_menu_option('mobile-centered', 'level-mobile-centered');
			}

			/*
			 * 1.0.8
			 */
	
	
			if ( $old_version < '1.0.8' )
			{
				$menus = $this->parent->get_menus();
				foreach($menus as $menu) {
					
					$level_width = $this->parent->get_menu_option('level-width', $menu->term_id);
					if(strpos($level_width, "%") !== false) {
						$level_width = intval($level_width).'vw';
						$this->parent->update_menu_option('level-width', $level_width, $menu->term_id);
					}
					
					$menu_items_height = $this->parent->get_menu_option('menu-items-height', $menu->term_id);
					if(strpos($menu_items_height, "%") !== false) {
						$menu_items_height = intval($menu_items_height).'vh';
						$this->parent->update_menu_option('menu-items-height', $menu_items_height, $menu->term_id);
					}
					
					
					$menu_items = wp_get_nav_menu_items( $menu->term_id, array(
						'orderby' => 'menu_order'	
					));
			
				    foreach( $menu_items as $item ) {
					    
					    $level_width = $this->parent->get_menu_item_option('level-width', $item->ID);
						if(strpos($level_width, "%") !== false) {
							$level_width = intval($level_width).'vw';
							$this->parent->update_menu_item_option('level-width', $level_width, $item->ID);
						}
						
						$menu_items_height = $this->parent->get_menu_option('submenu-items-height', $item->ID);
						if(strpos($menu_items_height, "%") !== false) {
							$menu_items_height = intval($menu_items_height).'vh';
							$this->parent->update_menu_option('submenu-items-height', $menu_items_height, $item->ID);
						}
						
						$menu_items_height = $this->parent->get_menu_option('menu-item-height', $item->ID);
						if(strpos($menu_items_height, "%") !== false) {
							$menu_items_height = intval($menu_items_height).'vh';
							$this->parent->update_menu_option('menu-item-height', $menu_items_height, $item->ID);
						}
						
					}
				}
			}		

			// End Migrations	
					
			update_option($version_option_key, $new_version);
			
			$this->after_upgrade();
			
		}
	}

	
	function after_upgrade() {
		
		$this->parent->pcache->flush();
		delete_transient($this->parent->plugin_slug('changelog'));
		
		wp_redirect($this->parent->welcome->get_url());
		exit;
	}


	/**
	 * Slick_Menu_Migration Instance
	 *
	 * Ensures only one instance of Slick_Menu_Migration is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Slick_Menu()
	 * @return Slick_Menu_Migration instance
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
