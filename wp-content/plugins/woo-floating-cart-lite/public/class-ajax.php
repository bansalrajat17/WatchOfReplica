<?php
if(class_exists('WC_AJAX')) {
	
	class Woo_Floating_Cart_AJAX extends WC_AJAX {
	
	    /**
	     - Hook in ajax handlers.
	     */
	    public static function init() {
	        add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
	        add_action( 'template_redirect', array( __CLASS__, 'do_wc_ajax' ), 0 );
	        self::add_ajax_events();
	    }
	
	    /**
	     - Add custom ajax events here
	     */
	    public static function add_ajax_events() {
	        // woocommerce_EVENT => nopriv
	        $ajax_events = array(
	            'woofc_update_cart' => true,
	        );
	        foreach ( $ajax_events as $ajax_event => $nopriv ) {
	            add_action( 'wp_ajax_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
	            if ( $nopriv ) {
	                add_action( 'wp_ajax_nopriv_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
	                // WC AJAX can be used for frontend ajax requests
	                add_action( 'wc_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
	            }
	        }
	    }
	
	
		public static function get_refreshed_fragments_raw() {
			        
	        WC()->cart->calculate_totals();
			
	        // Get mini cart
	        ob_start();
	        woocommerce_mini_cart();
	        $mini_cart = ob_get_clean();
	        // Fragments and mini cart are returned
	        $data = array(
	            'fragments' => 
	                apply_filters( 
	                'woocommerce_add_to_cart_fragments', 
	                array(
	                    'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
	                )
	            ),
	            'cart_hash' => apply_filters(
	            	'woocommerce_add_to_cart_hash', 
	                WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', 
	                WC()->cart->get_cart_for_session() 
	            )
	        );
	
			$list = woo_floating_cart_template('parts/cart/list', array(), true);
			$total = woofc_checkout_total();
			$count = WC()->cart->get_cart_contents_count();
			
			$data['fragments']['woofc'] = array(
				'subtotal' => $total,
				'total_items' => $count,
			);	
			
			if(empty($_COOKIE['woofc_last_removed'])) {
				$data['fragments']['.woofc-list'] = $list;
			}
			$data['fragments']['.woofc-checkout span.amount'] = '<span class="amount">'.$total.'</span>';
			$data['fragments']['.woofc-count li:nth-child(1)'] = '<li>'.$count.'</li>';
			$data['fragments']['.woofc-count li:nth-child(2)'] = '<li>'.($count + 1).'</li>';
	        
	        
	        /**
	         - Used 'return' here instead of 'wp_send_json()';
	         */
	        return ( $data ); 
	    }
	    
	    /**
	     - Removes item from the cart then returns a new fragment
	     */
	    public static function woofc_update_cart() {
			
			$type = filter_var($_POST['type'], FILTER_SANITIZE_STRING);
			
			$cart_item_key = null;
			
			if(!empty($_POST['cart_item_key'])) {
				$cart_item_key = filter_var($_POST['cart_item_key'], FILTER_SANITIZE_STRING);
			}
	
			if($type == 'update' && !empty($cart_item_key)) {
			
				$cart_item_qty = intval($_POST['cart_item_qty']);  
				WC()->cart->set_quantity($cart_item_key, $cart_item_qty, true); 
		
			}else if($type == 'remove' && !empty($cart_item_key)) {
				
				WC()->cart->remove_cart_item($cart_item_key); 
				
			}else if($type == 'undo' && !empty($cart_item_key)) {
	
				WC()->cart->restore_cart_item($cart_item_key);
				
			}
	
			$fragments = self::get_refreshed_fragments_raw();
	
			wp_send_json($fragments);
	
	    }
	}
	
	$woofc_ajax = new Woo_Floating_Cart_AJAX();
	$woofc_ajax->init();	
}