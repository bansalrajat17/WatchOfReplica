<?php
	
if (version_compare(PHP_VERSION, '5.4') < 0) {
	
    include_once __DIR__ . '/scss.old.inc.php';
    
}else if (!class_exists('scssc', false)) {

    include_once __DIR__ . '/scss.new.inc.php';
}
