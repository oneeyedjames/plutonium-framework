<?php

namespace Plutonium\Application;

use function Plutonium\Functions\filepath;

class ApplicationComponentLocator {
	private $_path;

	public function __construct($path) {
		$this->_path = trim(str_replace([FS, BS], DS, $path), DS);
	}

	public function getPath($name, $phar = false) {
		if (!defined('PU_PATH_BASE')) return null;

		$name = strtolower($name) . ($phar ? '.phar' : '');
		$path = PU_PATH_BASE . DS . $this->_path . DS . $name;

		return filepath($path);
	}

	public function getFile($name, $file, $phar = false) {
		$path = $this->getPath($name, $phar);
		$file = trim(str_replace([FS, BS], DS, $file), DS);

		return filepath($path . DS . $file);
	}
}