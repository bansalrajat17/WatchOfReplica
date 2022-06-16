<?php
	
if ( ! defined( 'ABSPATH' ) ) exit;


if(!defined('XT_REPO_URL')) {
	define('XT_REPO_URL', 'http://repo.xplodedthemes.com');
}

if(!defined('XT_LICENSED_THEMES_URL')) {
	define('XT_LICENSED_THEMES_URL', 'http://license.xplodedthemes.com/licensed_theme.php');
}

class Slick_Menu_Extensions {	
	
	public static $parent;
	public static $parent_id;
	public static $parent_name;
	public static $menu_parent;
	public static $menu_slug;
	public static $path;
	public static $url;
	public static $is_theme;
	public static $licensed_theme = false;
	public static $licenses = array();

	public static function load ($instance, $apiOnly = false) {
		
		self::$parent = $instance;
		self::$parent_name = $instance->plugin_name();
		self::$parent_id = $instance->plugin_slug();
		self::$menu_parent = $instance->plugin_slug();
		self::$menu_slug = self::$parent_id.'-extensions';
		self::$path = $instance->dir.'/includes/extensions';
		self::$url = $instance->url.'/includes/extensions';
		self::$is_theme = false;
		
		if($apiOnly) {
			return __CLASS__;
		}
		
		require_once('tgmpa.class.php');
		
		self::tgmp()->parent_slug = self::$menu_parent;
		self::tgmp()->menu = self::$menu_slug;
				
		if(self::is_admin_page()) {
			add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_styles'), 100);
			add_filter('admin_body_class', array(__CLASS__, 'admin_body_class' ));
		}
			
		add_action('slick_menu_tgmpa_register', array(__CLASS__, 'register_required_plugins'));
		add_filter('slick_menu_tgmpa_admin_menu_args', array(__CLASS__, 'addons_page'), 10, 1); 
        add_action('admin_footer', array(__CLASS__, 'update_notice'), 10);
		add_action('admin_notices', array(__CLASS__, 'new_extension_notice' ), 10);

		if(!self::$is_theme) {
			
			self::$licensed_theme = self::is_licensed_theme();
			add_action('admin_init', array(__CLASS__, 'check_licenses'), 10, 1); 
		}
		
		return __CLASS__;
	}

	public static function enqueue_admin_styles() {
	
        wp_enqueue_style(self::$parent_id.'-extensions', self::$url.'/css/extensions.css', false, '', 'all' );
	
	}

	public static function admin_body_class( $classes ) {
		
		$classes .= ' xt-extensions';
		$classes .= ' '.self::$menu_slug;
		
	    return $classes;
	}


	/*
	 * Register recommended plugins for this theme.
	 */
	
	public static function register_required_plugins ()
	{
		if(!current_user_can('activate_plugins'))
			return false;
			
		
		$plugins = self::get_plugins();
		
		$config = array(
			'domain'       => 'slick-menu',
			'has_notices'  => true, // Show admin notices or not
			'is_automatic' => true // Automatically activate plugins after installation or not
		);
	
		slick_menu_tgmpa($plugins, $config);
	
	}
	
	public static function check_licenses() {
		
		$plugins = self::get_plugins();
		$deactivate_unlicensed = array();
		
		foreach($plugins as $plugin) {	
			
			if(!empty($plugin['market']) && !self::is_slickmenu_site()) {
				
				$market_product = $plugin['market'];
				$market_id = $market_product['market_id'];
				
				$refreshLicense = false;
				if(self::is_action('force_plugins')) {
					$refreshLicense = true;
				}
				
				$license = new Slick_Menu_License($market_product, $refreshLicense, $market_id);

				if(!$license->isActivated() && self::plugin_exists( $plugin['file_path'] ) && is_plugin_active($plugin['file_path'])) {
					$deactivate_unlicensed[] = $plugin['file_path'];
				}
				
				self::$licenses[$plugin['slug']] = $license;
			}
		}
		
		if(!self::$licensed_theme && !empty($deactivate_unlicensed)) {
			deactivate_plugins( $deactivate_unlicensed, true );
			wp_redirect(self::tgmp()->get_slick_menu_tgmpa_url());
			exit;
		}
	}
	
	public static function license_submsg_activate($msg, $product) {
		
		$plugin = self::get_market_plugin($product->id);
		
		if($plugin['slug'] == self::$parent_id) {
			return $msg;
		}
		
		$actions = self::plugin_actions($plugin, 'ids');
		
		$action = "";
		if(!empty($actions['activate'])) {
			$action = "to activate";
		}else if(!empty($actions['install'])) {
			$action = "to install and activate";
		}else{
			$action = "to have direct support and updates for";
		}	
		
		$msg = sprintf(
			esc_html('Validate your purchase code %1$s the plugin', 'slick-menu'), 
			'<strong>'.$action.'</strong>'
		);

		return $msg;
	}

	public static function license_submsg_activated($msg, $product) {

		$plugin = self::get_market_plugin($product->id);
			
		if($plugin['slug'] == self::$parent_id) {
			return $msg;
		}
			
		$actions = self::plugin_actions($plugin, 'ids');
		
		$action = "";
		if(!empty($actions['activate'])) {
			$action = "activate";
		}else if(!empty($actions['install'])) {
			$action = "install and activate";
		}	
					
		$msg = sprintf(
	    	esc_html__( 'You can now %1$s the plugin by clicking the button below', 'slick-menu'), 
			"<strong>".$action."</strong>"
		);

		return $msg;
	}


	public static function tgmp() {
		
		if(empty($GLOBALS['slick_menu_tgmpa'])) {
			$GLOBALS['slick_menu_tgmpa'] = SM_TGM_Plugin_Activation::get_instance();
		}
		
		return $GLOBALS['slick_menu_tgmpa'];
	}
	
	public static function addons_page($args) {
		
		$args['parent_slug'] = self::$menu_parent;
		$args['page_title'] = self::$parent_name.' ' .esc_html__('Extensions', 'slick-menu');
		$args['menu_title'] = esc_html__('Extensions', 'slick-menu');
		$args['menu_slug'] = self::$menu_slug;
		$args['capability'] = 'activate_plugins';
		$args['function'] = array(__CLASS__, 'addons_page_extensions');
		
		return $args;
	}
	
	
	public static function addons_page_extensions() { 

	  
	  ?>
	  <div class="wrap xt-addons-extensions">
	
	    <header class="xt-addons-header">
			<h3><?php echo esc_html( get_admin_page_title() ); ?></h3>
			<p class="xt-addons-desc">Extend the functionality of <?php echo self::$parent_name;?> to suit your needs via the plugins available below.</p>
	
			<?php
			$plugin_table = new SM_TGMPA_List_Table;
			
			// Return early if processing a plugin installation action.
			if ( ( ( 'slick-menu-tgmpa-bulk-install' === $plugin_table->current_action() || 'slick-menu-tgmpa-bulk-update' === $plugin_table->current_action() ) && $plugin_table->process_bulk_actions() ) || self::tgmp()->do_plugin_install() ) {
				return;
			}
		
			// Force refresh of available plugin information so we'll know about manual updates/deletes.
			wp_clean_plugins_cache( false );
			?>
		
			<?php $plugin_table->prepare_items(); ?>
			
			
			<?php
			$messages = '';	
			if ( ! empty( self::tgmp()->message ) && is_string( self::tgmp()->message ) ) {
				$messages =  wp_kses_post( self::tgmp()->message );
			}
			?>
			<div class="xt-extensions-messages"><?php echo $messages;?></div>
			
	    </header>

			
		<form id="slick-menu-tgmpa-plugins" action="" method="post">
		<input type="hidden" name="slick-menu-tgmpa-page" value="<?php echo esc_attr( self::tgmp()->menu ); ?>" />
		<input type="hidden" name="plugin_status" value="<?php echo esc_attr( $plugin_table->view_context ); ?>" />
		<?php wp_nonce_field( 'bulk-' . $plugin_table->_args['plural'] ); ?>
		
		<?php 
		$plugins = $plugin_table->items;	
		$update_plugins = array();
		$active_plugins = array();
		$inactive_plugins = array();
		$uninstalled_plugins = array();
		
		
		foreach ( $plugins as $plugin ) {
			
			if ( self::plugin_exists( $plugin['file_path'] ) ) {
			    
			    
			    if(self::tgmp()->does_plugin_require_update($plugin['slug'])) {
			
					$plugin['status'] = 'update-needed';
					$plugin['status_msg'] = 'Update Needed';
					$update_plugins[] = $plugin;
					
				}else{
					
				  	if ( is_plugin_active( $plugin['file_path'] ) ) {
				  		
				  		$plugin['status'] = 'active';
				  		$plugin['status_msg'] = 'Active';
				        $active_plugins[] = $plugin;
				    
				  	} else  {
	
				  		$plugin['status'] = 'inactive';
				  		$plugin['status_msg'] = 'Inactive';
						$inactive_plugins[] = $plugin;
				  	}
				  	
				}	  	
			  
			}else{
			
				$plugin['status'] = 'not-installed';
				$plugin['status_msg'] = 'Not Installed';
				$uninstalled_plugins[] = $plugin;
			
			}

		}
	
		?>
		<div class="xt-addons-extensions-list-wrap">

			<div class="xt-addons-bulk-actions">
				<label class="screen-reader-text" for="cb-select-all"><?php echo esc_html__( 'Select All', 'slick-menu' );?></label>
				<input id="cb-select-all" type="checkbox">
				<select name="action">
					
					<option value="-1"><?php echo esc_html__( 'Bulk Actions', 'slick-menu' );?></option>
					
					<?php foreach($plugin_table->get_bulk_actions() as $key => $action):?>
					
						<option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($action);?></option>
						
					<?php endforeach; ?>
					
				</select>
				<input type="submit" id="doaction" class="button action" value="<?php echo esc_html__( 'Apply', 'slick-menu' );?>">
				<input type="button" id="refresh-plugins" onclick="location.href='<?php echo self::tgmp()->get_slick_menu_tgmpa_url();?>&xt-action=force_plugins';" class="button action" value="<?php echo esc_html__( 'Refresh List', 'slick-menu' );?>">
			</div>
			
			<?php
			$plugin_table->views();
			
			self::plugin_list(esc_html__('Active Extensions', 'slick-menu'), $active_plugins, $plugin_table);
			self::plugin_list(esc_html__('Inactive Extensions', 'slick-menu'), $inactive_plugins, $plugin_table);
			self::plugin_list(esc_html__('Update Extensions', 'slick-menu'), $update_plugins, $plugin_table);
			self::plugin_list(esc_html__('Uninstalled Extensions', 'slick-menu'), $uninstalled_plugins, $plugin_table);
			?>
			</div>
	    </form>
		<script>
		jQuery(document).ready(function($) {
			
			$('.notice, .error, #message').each(function() {
				$(this).appendTo('.xt-extensions-messages').fadeIn();
			})
			
			$('.xt-addons-bulk-actions #cb-select-all').on('click', function() {
	
				$('.xt-addons-extensions-list .checkbox-wrap input').trigger('click');
				
			});

			
			$(document).on('sm-license-activated', function(e, license, license_summary, license_form) {
				
				var $plugin = $('.xt-addons-extension-wrap[data-market-product-id="'+license.product_id+'"]');
				
				if($plugin.length === 0) {
					return false;	
				}	
				
				var slug = $plugin.data('slug');	
				var actions = $plugin.data('actions');
				var action = '';
				var action_label = '';
				var link = false;
				
				if(typeof(actions.activate) !== 'undefined') {
					
					action = 'activate';
					action_label = 'Activate Plugin';
					link = '<?php echo self::tgmp()->get_slick_menu_tgmpa_url();?>&xt-action='+action+'&slug='+slug;
					
				}else if(typeof(actions.install) !== 'undefined') {
					
					action = 'install';
					action_label = 'Install Plugin';
					link = '<?php echo self::tgmp()->get_slick_menu_tgmpa_url();?>&xt-action='+action+'&slug='+slug;
				}
				
				if(link !== false) {
					
					$(license_form).append('<br><br><a href="'+link+'" class="button button-primary">'+action_label+'</a>');

				}
			});
			
		});
		</script>

	  </div>
	
	<?php }
		
	public static function plugin_list($title, $plugins, &$plugin_table) {
		
		if(empty($plugins))
			return false;
			
		add_thickbox();
			
		$id = sanitize_title($title);	
		?>
		
		<h3><?php echo esc_html($title);?></h3>
		<ul class="xt-addons-extensions-list cf <?php echo esc_attr($id);?>" id="xt-addons-extensions-list">
		<?php

		foreach ( $plugins as $plugin ) :
	
			$plugin_data = self::get_plugin($plugin['slug']);
			
			$actions_data = self::plugin_actions($plugin);
			$actions_urls = $actions_data['urls'];
			$actions_links = $actions_data['links'];
			$actions_ids = $actions_data['ids'];
			
			$status         = $plugin['status'];
			$status_message = $plugin['status_msg'];

			$license = null;
			if(!empty(self::$licenses[$plugin['slug']])) {
				$license = self::$licenses[$plugin['slug']];
				
				add_filter('sm_license_submsg_activate_'.$license->product()->id, array(__CLASS__, 'license_submsg_activate'), 10, 2); 
				add_filter('sm_license_submsg_activated_'.$license->product()->id, array(__CLASS__, 'license_submsg_activated'), 10, 2); 
				
			}
	        ?>
	
	        <li data-market-product-id="<?php echo (!empty($license) ? $license->product()->id : '');?>" data-slug="<?php echo esc_attr($plugin['slug']);?>" data-actions="<?php echo htmlentities(json_encode($actions_ids), ENT_QUOTES, 'UTF-8'); ?>" class="xt-addons-extension-wrap <?php echo esc_attr($status); ?>" id="<?php echo esc_attr($plugin['slug']); ?>">
		        <div class="xt-addons-extension">
					<?php if(self::is_new_extension($plugin_data)): ?>
						<span class="xt-new-extension">New</span>
					<?php endif; ?>
		          	<div class="top cf">
						<img src="<?php echo XT_REPO_URL . '/logos/'.$plugin['slug'].'.png'; ?>" class="img">
						<div class="info">
							<h4 class="title"><?php echo esc_html($plugin['name']); ?></h4>
							
							<?php if(!empty($plugin_data['author'])) : ?>
							<p class="author"><cite>By <?php echo esc_html($plugin_data['author']); ?></cite></p>
							<?php endif; ?>
							
							<?php if(!empty($plugin_data['description'])) : ?>
							<p class="desc"><?php echo esc_html($plugin_data['description']); ?></p>
							<?php endif; ?>
							
							<div class="statuses">
								<?php if(!empty($plugin['installed_version'])): ?>
								<span class="version status">v.<?php echo esc_html($plugin['installed_version']); ?></span> 
								<?php endif; ?>
								<span class="status <?php echo esc_attr($status); ?>"><?php echo esc_html($status_message); ?></span>
							</div>
	
		            	</div>
		          </div>
		          <div class="bottom cf">
			          <?php
				      if(!empty($license)) {
				      		
				      		?>
				      		
							<div id="sm-license-<?php echo $plugin['slug'];?>" class="sm-popup-wrapper" style="display:none;">
								<div class="sm-popup-inner">
							     	<?php echo $license->form(); ?>
								</div>
							</div>
							
							<?php if(!$license->isActivated()): ?>
								
								<?php 
								if(self::$licensed_theme) {
									 echo implode('', $actions_links);
								}
								?>
								<a href="#" data-target="#sm-license-<?php echo $plugin['slug'];?>" class="button button-primary sm-popup">Validate License</a>
								<a href="<?php echo $license->product()->buy_url;?>" class="button" target="_blank">Buy a License</a>
								
							<?php else: ?>
								
								<span class="checkbox-wrap"><input type="checkbox" name="plugin[]" value="<?php echo esc_attr($plugin['slug']);?>"></span>
								
								<?php echo implode('', $actions_links);?>
									
								<a href="#" data-target="#sm-license-<?php echo $plugin['slug'];?>" class="sm-popup button button-primary">License Info</a>
								
							<?php endif; ?>
							
							<?php
					      	
				      }else{   
				      		
				      		?>
				      		<span class="checkbox-wrap"><input type="checkbox" name="plugin[]" value="<?php echo esc_attr($plugin['slug']);?>"></span>
					  		<?php
			          		echo implode('', $actions_links);	
			          }
			          ?>
			      </div>
		        </div>
	        </li>
	
	      <?php endforeach; ?>
	
		</ul>
	
		<?php 
			
		if(self::is_action('activate')) {
			
			if(!empty($_GET['slug'])) {
				
				$slug = $_GET['slug'];
				$plugin = self::get_plugin($slug);
				$actions = self::plugin_actions($plugin, 'urls');
				die('<meta http-equiv="refresh" content="0; url='.$actions['activate'].'">');
				exit;
			}
		}	
		
		if(self::is_action('install')) {
			
			if(!empty($_GET['slug'])) {
				
				$slug = $_GET['slug'];
				$plugin = self::get_plugin($slug);
				$actions = self::plugin_actions($plugin, 'urls');
				die('<meta http-equiv="refresh" content="0; url='.$actions['install'].'">');
			}
		}	
    
	}	
	
	public static function plugin_exists( $plugin ) {
	
	  if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin ) ) {
	    return true;
	  } else {
	    return false;
	  }
	
	}
		
	public static function plugin_active( $plugin ) {
	
	  if ( is_plugin_active( WP_PLUGIN_DIR . '/' . $plugin ) ) {
	    return true;
	  } else {
	    return false;
	  }
	
	}
	
	public static function plugin_actions($item, $type = null) {
				
		$actions      = array();
		$action_links = array();
		$action_urls = array();
		$action_ids = array();
	
		// Display the 'Install' action link if the plugin is not yet available.
		if ( ! self::tgmp()->is_plugin_installed( $item['slug'] ) ) {
			
			$actions['install'] = esc_html__( 'Install', 'slick-menu' );
			
		} else {
			
			// Display the 'Update' action link if an update is available and WP complies with plugin minimum.
			if ( false !== self::tgmp()->does_plugin_have_update( $item['slug'] ) && self::tgmp()->can_plugin_update( $item['slug'] ) ) {
				$actions['update'] = esc_html__( 'Update to v.', 'slick-menu' ).$item['available_version'];
			}else{
	
				// Display the 'Activate' action link, but only if the plugin meets the minimum version.
				if ( self::tgmp()->can_plugin_activate( $item['slug'] ) ) {
					$actions['activate'] = esc_html__( 'Activate', 'slick-menu' );
				}else{
					$actions['deactivate'] = esc_html__( 'Deactivate', 'slick-menu' );
				}
			}	
		}
	
		// Create the actual links.
		foreach ( $actions as $action => $text ) {
			
			$class = 'button';
			
			if($action == 'install' || $action == 'update') {
				$class = 'button button-primary '.$action;
			}
			
			$nonce_url = wp_nonce_url(
				add_query_arg(
					array(
						'plugin'           => urlencode( $item['slug'] ),
						'slick-menu-tgmpa-' . $action => $action . '-plugin',
					),
					self::tgmp()->get_slick_menu_tgmpa_url()
				),
				'slick-menu-tgmpa-' . $action,
				'slick-menu-tgmpa-nonce'
			);
			
			$action_ids[$action] = $action;
			$action_urls[ $action ] = $nonce_url;
			$action_links[ $action ] = sprintf(
				'<a class="'.esc_attr($class).'" href="%1$s">' . esc_html( $text ) . '</a>',
				esc_url( $nonce_url )
			);
		}
		
		$actions = array(
			'links' => array_filter( $action_links ),
			'urls' => array_filter( $action_urls ),
			'ids' => array_filter( $action_ids )
		);
		
		
		if(!empty($type) && !empty($actions[$type])) {
			return $actions[$type];
		}
		
		return $actions;
			
	}

	public static function get_plugins() {
		
		$cache_key = self::$parent_id.'_plugins';
		
		if(defined('XT_REPO_DEBUG') && XT_REPO_DEBUG) {
			$cache_key .= '-debug';
		}
	
		if ( false === ( $plugins = get_site_transient( $cache_key ) ) || self::is_action('force_plugins')) {
			
			$plugins = wp_cache_get( $cache_key );
	
		 	if($plugins === false) {
			 	
				if(self::$is_theme) {
					$key = 'theme';
				}else{
					$key = 'p';
				}
				
				$token = md5('xt-'.self::$parent_id);
				$url = XT_REPO_URL.'/?'.$key.'='.self::$parent_id.'&token='.$token;
				
				if(defined('XT_REPO_DEBUG') && XT_REPO_DEBUG) {
					$url .= '&debug=1';
				}

				$response = wp_remote_get($url);
				if ( !is_wp_error( $response ) ) {
					
					$plugins = wp_remote_retrieve_body($response);
					if(!empty($plugins)) {
						set_site_transient( $cache_key, $plugins, DAY_IN_SECONDS );	
						delete_site_transient(self::$parent_id.'_plugins_updates');
						wp_cache_set( $cache_key, $plugins );
					}	
				}
			}	
		}
		$plugins = maybe_unserialize($plugins);
		
		if(!is_array($plugins)){
			$plugins = array();
		}

		return $plugins;
	}

	public static function get_plugin($slug) {
		
		$plugins = self::get_plugins();
			
		foreach($plugins as $plugin) {
			
			if($plugin['slug'] == $slug) {
				return $plugin;
			}
		}
		
		return false;
	}
			
	public static function is_licensed_theme() {
		
		$active_theme = get_template();
				
		$cache_key = self::$parent_id.'_licensed_theme';

		if ( false === ( $licensed_theme = get_site_transient( $cache_key ) ) || self::is_action('force_plugins')) {
	
			$licensed_theme = wp_cache_get( $cache_key );
	
		 	if($licensed_theme === false) {
	
				$url = XT_LICENSED_THEMES_URL.'/?plugin='.self::$parent_id.'&theme='.$active_theme;
	
				$response = wp_remote_get($url);
				if ( !is_wp_error( $response ) ) {
					
					$licensed_theme = wp_remote_retrieve_body($response);
			
					if(!empty($licensed_theme)) {
						$licensed_theme = json_decode($licensed_theme);
						set_site_transient( $cache_key, $licensed_theme, DAY_IN_SECONDS );	
						wp_cache_set( $cache_key, $licensed_theme );
					}
				}
			}
		}

		return ($licensed_theme === 'licensed');
	}

	public static function get_market_plugin($id) {
		
		$plugins = self::get_plugins();
			
		foreach($plugins as $plugin) {
			
			if(!empty($plugin['market']) && $plugin['market']['id'] == $id) {
				return $plugin;
			}
		}
		
		return false;
	}
	
	public static function is_action($action) {
		
		return is_admin() && !empty($_GET['xt-action']) && $_GET['xt-action'] == $action;
	}
	
	public static function is_admin_page() {

		return self::tgmp()->is_slick_menu_tgmpa_page();
	}		

	static function update_notice() {
	
		$cache_key = self::$parent_id.'_plugins_updates';
		
		if(!empty($_GET['plugin'])) {
			delete_site_transient($cache_key);
			delete_site_transient(self::$parent_id.'_plugins');
			return false;
		}
			
		if ( false === ( $update_count = get_site_transient( $cache_key ) ) || !empty($_GET['plugin_status']) || self::is_action('force-plugins')) {
			
			$update_count = 0;
			
			foreach ( self::tgmp()->plugins as $slug => $plugin ) {
				if ( self::tgmp()->is_plugin_active( $slug ) && false === self::tgmp()->does_plugin_have_update( $slug ) ) {
					continue;
				}
				
				if ( self::tgmp()->is_plugin_installed( $slug ) ) {
	
					if ( self::tgmp()->does_plugin_require_update( $slug ) || false !== self::tgmp()->does_plugin_have_update( $slug ) ) {
	
						if ( current_user_can( 'install_plugins' ) ) {
							
							if ( self::tgmp()->does_plugin_require_update( $slug ) || (self::tgmp()->does_plugin_have_update( $slug ) !== false) ) {
								$update_count++;
							}
						}
					}
				}
			}
			set_site_transient( $cache_key, $update_count, 12 * HOUR_IN_SECONDS );	
		}	
		
		if($update_count > 0) {
			?>
			<script>
				(function () {
			
					var menu = document.querySelector('li.toplevel_page_slick-menu a[href="admin.php?page=<?php echo self::$menu_slug;?>"]');
					var menu_parent = menu.parentElement.parentElement.parentElement.querySelector('a .wp-menu-name');
					
					var menus = [
						menu,
						menu_parent
					];
					
					for (var i = 0 ; i < menus.length ; i++) {
						
						var _menu = menus[i];
					
						var pending = _menu.querySelector('.pending-count');
						if(pending) {
							return false;
						}
						
						var span = document.createElement("span"); 
						span.setAttribute('class', "awaiting-mod count-<?php echo $update_count; ?>");
						
						var inner_span = document.createElement("span"); 
						inner_span.setAttribute('class', "pending-count");
						
						var count = document.createTextNode(<?php echo $update_count; ?>);
						var space = document.createTextNode(' ');
						
						inner_span.appendChild(count);
						span.appendChild(inner_span);
						_menu.appendChild(space);
						_menu.appendChild(span);
					
					}

				}());
			</script>
			<?php

		}	
	}	

	public static function new_extension_notice() {
		
		if(!empty($_GET['xt_ext_dismiss_notice'])) {
			
			$notice_id = esc_attr($_GET['xt_ext_dismiss_notice']);
			self::update_dismissed_notices($notice_id);
		}
		
		foreach ( self::tgmp()->plugins as $slug => $plugin ) {
			
			$notice_id = 'new_'.$plugin['slug'];
	
			if(self::is_new_extension($plugin) && !self::is_dismissed_notices($notice_id)):?>
			
			<div class="xt-notice notice notice-warning is-dismissible">
		        <p>
			    <?php 
				echo sprintf(
					__('New <strong>Slick Menu Extension</strong> now available: <strong><a href="%1$s">%2$s</a></strong>', 'slick-menu'), 
					self::tgmp()->get_slick_menu_tgmpa_url().'&xt_ext_dismiss_notice='.$notice_id,
					$plugin['name']
				);
				?>
				</p>
		    </div>
	    
			<?php
			endif;
		}
	}
	
	public static function get_dismissed_notices() {
		
		return get_site_option('xt-extensions-dismissed-notices', array(), false);
	}
	
	public static function is_dismissed_notices($notice_id) {
		
		$notices = self::get_dismissed_notices();
		return !empty($notices[$notice_id]);
	}
	
	public static function update_dismissed_notices($notice_id) {
		
		$notices = self::get_dismissed_notices();
		$notices[$notice_id] = $notice_id;
		
		return update_site_option('xt-extensions-dismissed-notices', $notices);
	}
		
	public static function is_new_extension($plugin) {

		if(empty($plugin['date'])){
			return false;
		}
		
		$date = $plugin['date'];
		if(strtotime($date) >= strtotime('-30 days')) {
			return true;
		}
		
		return false;
	}
	
	public static function is_slickmenu_site() {
		
		if(defined('SM_DEBUG_EXTENSIONS') && SM_DEBUG_EXTENSIONS) {
			return false;
		}
		return strpos($_SERVER["HTTP_HOST"], 'slickmenu.net') !== false || strpos($_SERVER["HTTP_HOST"], 'slickmenu.test') !== false;
	}
}
		