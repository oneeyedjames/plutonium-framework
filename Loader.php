<?php

namespace Plutonium;

final class Loader {
	private static $_registered = false;

	private static function register() {
		if (!self::$_registered)
			spl_autoload_register(array(__CLASS__, 'import'));
	}

	public static function addPath($path) {
		set_include_path(get_include_path() . PS . realpath($path));
	}

	public static function getPaths() {
		return explode(PS, get_include_path());
	}

	public static function addExtension($extension) {
		spl_autoload_extensions(spl_autoload_extensions() . ',' . $extension);
	}

	public static function getExtensions() {
		return explode(',', spl_autoload_extensions());
	}

	public static function getClass($files, $class, $default, $args = null) {
		if (!class_exists($class)) {
			if (is_array($files)) {
				foreach ($files as $file) {
					if (is_file($file)) {
						require_once $file;
						if (class_exists($class)) break;
					}
				}
			} elseif (is_file($files)) {
				require_once $files;
			}
		}

		$type = class_exists($class) ? $class : $default;

		return is_null($args) ? new $type() : new $type($args);
	}

	public static function load($class) {
		self::register();
		self::import($class);
	}

	public static function autoload($path = null) {
		self::register();
		self::addPath($path);
	}

	public static function import($class) {
		return self::importFile(str_replace(BS, DS, $class));
	}

	public static function importFile($rel_path) {
		foreach (self::getPaths() as $lib_path) {
			foreach (self::getExtensions() as $ext) {
				$abs_path = $lib_path . DS . $rel_path . $ext;

				if (is_file($abs_path)) {
					require_once $abs_path;
					return true;
				}
			}
		}

		return false;
	}

	public static function importDirectory($rel_path) {
		foreach (self::getPaths() as $lib_path) {
			if ($abs_path = realpath($lib_path . DS . $rel_path)) {
				foreach (self::getExtensions() as $ext) {
					foreach (glob($abs_path . DS . '*' . $ext) as $file)
						require_once $file;
				}

				return true;
			}
		}

		return false;
	}
}
