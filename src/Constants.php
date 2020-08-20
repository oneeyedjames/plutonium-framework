<?php

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('LS', PHP_EOL);
define('BS', '\\');
define('FS', '/');

function define_default_constants() {
	$root = str_replace(BS, FS, realpath($_SERVER['DOCUMENT_ROOT']));
	$base = str_replace(BS, FS, dirname(realpath($_SERVER['SCRIPT_FILENAME'])));

	defined('PU_PATH_ROOT') or define('PU_PATH_ROOT', $root);
	defined('PU_PATH_BASE') or define('PU_PATH_BASE', $base);
}
