<?php
/**
 * @package plutonium\application
 */

namespace Plutonium\Application;

use function Plutonium\Functions\filepath;
use function Plutonium\Functions\cleanpath;

class ApplicationComponentLocator {
	private $_base_path;

	public function __construct($base_path) {
		$this->_base_path = cleanpath($base_path);
	}

	public function getPath($name, $phar = false) {
		if (!defined('PU_PATH_BASE')) return null;

		$name = strtolower($name) . ($phar ? '.phar' : '');
		$path = PU_PATH_BASE . DS . $this->_base_path . DS . $name;

		return filepath($path);
	}

	public function getFile($name, $file, $phar = false) {
		$path = $this->getPath($name, $phar);
		$file = cleanpath($file);

		return filepath($path . DS . $file);
	}

	public function locateFile($name, $files) {
		$path = $this->getPath($name);
		$phar = $this->getPath($name, true);

		if (is_string($files))
			$files = array_slice(func_get_args(), 1);

		if (is_file($phar)) {
			foreach ($files as $file) {
				$file = $this->getFile($name, $file, true);
				if (is_file('phar://' . $file)) return $file;
			}
		} elseif (is_dir($path)) {
			foreach ($files as $file) {
				$file = $this->getFile($name, $file);
				if (is_file($file)) return $file;
			}
		}

		return false;
	}
}
