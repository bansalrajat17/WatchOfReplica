<?php
/*
 * Plugin Name: Slick Menu
 * Version: 1.0.9.8
 * Plugin URI: http://www.slickmenu.net
 * Description: Slick Menu is an Advanced Responsive Vertical Push Menu with multi-level functionality that allows endless nesting of navigation elements. Automatically integrates with your existing wordpress menus.
 * Author: XplodedThemes
 * Author URI: http://www.xplodedthemes.com
 * Requires at least: 3.8
 * Tested up to: 4.9.6
 *
 * Text Domain: slick-menu
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Georges Haddad
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'SLICK_MENU_VERSION' ) ) {
	define('SLICK_MENU_VERSION', '1.0.9.8');
}	

// Load Framework
require_once( 'includes/modules/slick-menu-icons/slick-menu-icons.php' );
require_once( 'includes/modules/meta-box/meta-box.php' );
require_once( 'includes/modules/meta-box-group/meta-box-group.php' );
require_once( 'includes/modules/meta-box-extend/meta-box-extend.php' );	
require_once( 'includes/modules/sm-mb-nav-menu/sm-mb-nav-menu.php' );
require_once( 'includes/modules/sm-mb-settings-page/sm-mb-settings-page.php' );

if(is_admin()) {
	
	//require_once( 'includes/modules/sm-mb-admin-columns/sm-mb-admin-columns.php' );
	require_once( 'includes/modules/meta-box-tabs/meta-box-tabs.php' );
	require_once( 'includes/modules/meta-box-accordions/meta-box-accordions.php' );
	require_once( 'includes/modules/meta-box-columns/meta-box-columns.php' );
	require_once( 'includes/modules/meta-box-conditional-logic/meta-box-conditional-logic.php' );
	require_once( 'includes/modules/meta-box-include-exclude/meta-box-include-exclude.php' );
	
	// Load Admin Extensions
	require_once( 'includes/lib/class-update.php' );
	require_once( 'includes/lib/class-update-checker.php' );
	
}


// Load plugin function files
require_once( 'includes/data.php' );
require_once( 'includes/helpers.php' );

// Load plugin class files
require_once( 'includes/class-core.php' );
require_once( 'includes/class-migration.php' );
require_once( 'includes/class-language.php' );
require_once( 'includes/class-welcome.php' );
require_once( 'includes/class-cache.php' );
require_once( 'includes/class-pcache.php' );
require_once( 'includes/class-nav.php' );
require_once( 'includes/class-settings.php' );
require_once( 'includes/class-output.php' );
require_once( 'includes/class-styles-output.php' );

require_once( 'includes/class-menu-list.php' );
require_once( 'includes/class-walker.php' );


// Load plugin libraries
require_once( 'includes/lib/class-license.php' );
require_once( 'includes/lib/class-styles.php' );
require_once( 'includes/lib/class-image.php' );
require_once( 'includes/lib/class-post-type.php' );
require_once( 'includes/lib/class-extension.php' );
require_once( 'includes/extensions/extensions.class.php' );

// Setup Plugin

function Slick_Menu_Activated() {
    add_option('slick_menu_activation_redirect', true);
}
register_activation_hook(__FILE__, 'Slick_Menu_Activated');


/**
 * Returns the main instance of Slick_Menu to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Slick_Menu
 */
function Slick_Menu($class = null) {

	$instances = array();
	
	$instance = Slick_Menu::instance( __FILE__, SLICK_MENU_VERSION, '##XT_MARKET##' );
	
	// instantiate plugin's class
	$instances['migration'] = Slick_Menu_Migration::instance( $instance );
	$instances['lang'] = Slick_Menu_Language::instance( $instance );
	$instances['cache'] = Slick_Menu_Cache::instance( $instance );
	$instances['pcache'] = Slick_Menu_PCache::instance( $instance );
	$instances['nav'] = Slick_Menu_Nav::instance( $instance );
	$instances['output'] = Slick_Menu_Output::instance( $instance );
	$instances['styles'] = Slick_Menu_Styles_Output::instance( $instance );
	$instances['settings'] = Slick_Menu_Settings::instance( $instance );
	$instances['welcome'] = Slick_Menu_Welcome::instance( $instance );

	// Load API for generic admin functions
	$instances['extensions'] = Slick_Menu_Extensions::load( $instance, !is_admin() );	
		
	if(!empty($class) && !empty($instances[$class])) {
		return $instances[$class];	
	}
	
	return $instance;
}

add_action('plugins_loaded', 'Slick_Menu', 9999);	
