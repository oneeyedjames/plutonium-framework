<?php
/**
 * @package plutonium
 */

namespace Plutonium;

final class Loader {
	private static $_namespaces = ['Plutonium' => __DIR__];
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

	public static function addNamespace($name, $path) {
		if ($path = realpath($path)) {
			$name = trim(str_replace(BS, DS, $name), DS);
			self::$_namespaces[$name] = $path;
		}
	}

	public static function getNamespaces() {
		return self::$_namespaces;
	}

	private static function resolveNamespace($rel_path) {
		foreach (self::getNamespaces() as $ns => $ns_path) {
			$prefix = substr($rel_path, 0, strlen($ns) + 1);
			$suffix = substr($rel_path, strlen($ns));

			if ($ns . DS == $prefix)
				return $ns_path . $suffix;
		}

		return false;
	}

	public static function getClass($files, $class, $default, $args = null) {
		if (!class_exists($class)) {
			if (is_string($files)) $files = [$files];

			foreach ($files as $file) {
				if (stripos($file, '.phar/') !== false)
					$file = 'phar://' . $file;

				if (is_file($file)) {
					require_once $file;
					if (class_exists($class)) break;
				}
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
		return self::importFile(trim(str_replace(BS, DS, $class), DS));
	}

	public static function importFile($rel_path) {
		if ($ns_path = self::resolveNamespace($rel_path)) {
			foreach (self::getExtensions() as $ext) {
				if (is_file($ns_path . $ext)) {
					require_once $ns_path . $ext;
					return true;
				}
			}
		}

		$phar_path = explode(DS, $rel_path);
		$phar_path[0] .= '.phar';
		$phar_path = implode(DS, $phar_path);

		foreach (self::getPaths() as $lib_path) {
			$real_path = realpath($lib_path);

			foreach (self::getExtensions() as $ext) {
				$phar_file = 'phar://' . $real_path . DS . $phar_path . $ext;
				$abs_path = $real_path . DS . $rel_path . $ext;

				if (is_file($phar_file)) {
					require_once $phar_file;
					return true;
				} elseif (is_file($abs_path)) {
					require_once $abs_path;
					return true;
				}
			}
		}

		return false;
	}

	public static function importDirectory($rel_path) {
		if ($ns_path = self::resolveNamespace($rel_path)) {
			foreach (self::getExtensions() as $ext) {
				foreach (glob($ns_path . DS . '*' . $ext) as $file)
					require_once $file;
			}

			return true;
		}

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
