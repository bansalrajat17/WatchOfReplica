<?php
/**
 * Fontello Pack
 *
 * @package SM_Icon_Picker
 * 
 * @author  Joshua F. Rountree <joshua@swodev.com>
 */


require_once dirname( __FILE__ ) . '/font.php';

/**
 * Icon type: Fontello
 *
 * @version 0.1.0
 */
class SM_Icon_Picker_Type_Fontello extends SM_Icon_Picker_Type_Font {

	/**
	 * Stylesheet URI
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $stylesheet_uri;

	/**
	 * Icon pack directory path
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $dir;

	/**
	 * Icon pack directory url
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $url;

	/**
	 * Items
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $items;
}
