<?php
/**
 * @package plutonium
 */

namespace Plutonium;

/**
 * Collection of utility methods for auto-loading class/interface structures.
 */
final class Loader {
	/**
	 * @ignore internal variable
	 */
	private static $_namespaces = ['Plutonium' => __DIR__];

	/**
	 * @ignore internal variable
	 */
	private static $_registered = false;

	/**
	 * Registers import method for autoloading.
	 */
	private static function register() {
		if (!self::$_registered)
			spl_autoload_register(array(__CLASS__, 'import'));
	}

	/**
	 * Adds a directory to include path.
	 * Application root is added by default.
	 * @param string $path Relative file path
	 */
	public static function addPath($path) {
		set_include_path(get_include_path() . PS . realpath($path));
	}

	/**
	 * Returns array of directories in include path.
	 * @return array Array of absolute directory paths
	 */
	public static function getPaths() {
		return explode(PS, get_include_path());
	}

	/**
	 * Add a file extension for source files in include path.
	 * 'php' is added by default.
	 * @param string $extension File extension
	 */
	public static function addExtension($extension) {
		spl_autoload_extensions(spl_autoload_extensions() . ',' . $extension);
	}

	/**
	 * Returns array of file extensions for source files in include path.
	 * @return array Array of file extensions
	 */
	public static function getExtensions() {
		return explode(',', spl_autoload_extensions());
	}

	/**
	 * Associates a custom namespace with a specific directory, rather than
	 * general include path.
	 * @param string $name The fully-qualified namespace
	 * @param string $path The directory to associate with the namespace
	 */
	public static function addNamespace($name, $path) {
		if ($path = realpath($path)) {
			$name = trim(str_replace(BS, DS, $name), DS);
			self::$_namespaces[$name] = $path;
		}
	}

	/**
	 * Returns array of registered namespaces and their associated directories.
	 * @return array Associative array of namespaces and directories
	 */
	public static function getNamespaces() {
		return self::$_namespaces;
	}

	/**
	 * Resolves a relative path for a nested namespace into the absolute path
	 * within the directory of the parent namespace.
	 * @param string $rel_path The relative file path
	 * @return string The absolute file path
	 */
	private static function resolveNamespace($rel_path) {
		foreach (self::getNamespaces() as $ns => $ns_path) {
			$prefix = substr($rel_path, 0, strlen($ns) + 1);
			$suffix = substr($rel_path, strlen($ns));

			if ($ns . DS == $prefix)
				return $ns_path . $suffix;
		}

		return false;
	}

	/**
	 * Instantiates the named class if a definition can be found in the given
	 * list of files. Otherwise instantiates the named fallback class. Array of
	 * positional arguments, if provided, is passed into class constructor.
	 * @param array Array of absolute file paths
	 * @param string $class Name of class to instantiate, if found
	 * @param string $default Name of fallback class to instantiate
	 * @param array $args OPTIONAL array of constructor arguments
	 * @return object Instance of found or fallback class
	 */
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

	/**
	 * Registers import method for autoloading and imports named class.
	 * @param string $class Fully-qualified class name
	 */
	public static function load($class) {
		self::register();
		self::import($class);
	}

	/**
	 * Registers import method for autoloading and adds directory, if included,
	 * to include path.
	 * @param string $path Relative file path
	 */
	public static function autoload($path = null) {
		self::register();
		self::addPath($path);
	}

	/**
	 * Searches for and imports a source file matching the class name. If the
	 * class name matches a registered namespace, the associated directory will
	 * be preferred over the general include path.
	 * @param string $class Fully-qualified class name
	 * @return boolean Whether a matching source file could be found
	 */
	public static function import($class) {
		return self::importFile(trim(str_replace(BS, DS, $class), DS));
	}

	/**
	 * Searches for and imports a source file matching the relative path. If the
	 * path matches a registered namespace, the associated directory will be
	 * preferred over the general include path.
	 * @param string $rel_path A relative file path
	 * @return boolean Whether a matching source file could be found
	 */
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

	/**
	 * Searches for and imports all source files matching the relative path. If
	 * the path matches a registered namespace, the associated directory will be
	 * preferred over the general include path.
	 * @param string $rel_path A relative file path
	 * @return boolean Whether a matching directory could be found
	 */
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
