<?php
use Leafo\ScssPhp\Compiler;

include_once __DIR__ . '/src/Base/Range.php';
include_once __DIR__ . '/src/Block.php';
include_once __DIR__ . '/src/Colors.php';
include_once __DIR__ . '/src/Compiler.php';
include_once __DIR__ . '/src/Compiler/Environment.php';
include_once __DIR__ . '/src/Exception/CompilerException.php';
include_once __DIR__ . '/src/Exception/ParserException.php';
include_once __DIR__ . '/src/Exception/ServerException.php';
include_once __DIR__ . '/src/Formatter.php';
include_once __DIR__ . '/src/Formatter/Compact.php';
include_once __DIR__ . '/src/Formatter/Compressed.php';
include_once __DIR__ . '/src/Formatter/Crunched.php';
include_once __DIR__ . '/src/Formatter/Debug.php';
include_once __DIR__ . '/src/Formatter/Expanded.php';
include_once __DIR__ . '/src/Formatter/Nested.php';
include_once __DIR__ . '/src/Formatter/OutputBlock.php';
include_once __DIR__ . '/src/Node.php';
include_once __DIR__ . '/src/Node/Number.php';
include_once __DIR__ . '/src/Parser.php';
include_once __DIR__ . '/src/Type.php';
include_once __DIR__ . '/src/Util.php';
include_once __DIR__ . '/src/Version.php';
include_once __DIR__ . '/src/Server.php';


if(!function_exists('scss_new_compiler')) {
	
	function scss_new_compiler() {

		$scss = new Compiler();
		return $scss;
	}
}	