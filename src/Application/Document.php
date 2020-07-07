<?php
/**
 * @package plutonium\application
 */

namespace Plutonium\Application;

use Plutonium\AccessObject;
use Plutonium\Loader;
use Plutonium\Renderable;

abstract class Document extends AccessObject implements Renderable {
	protected static $_path = null;

	public static function getPath() {
		if (is_null(self::$_path) && defined('PU_PATH_BASE'))
			self::$_path = realpath(Application::getPath() . '/documents');

		return self::$_path;
	}

	public static function newInstance($format, $args) {
		$name = strtolower($format);
		$type = ucfirst($name) . 'Document';
		$file = self::getPath() . DS . $name . '.php';

		return Loader::getClass($file, $type, __CLASS__, $args);
	}

	protected $_application = null;

	protected $_title = null;

	public function __construct($args) {
		$this->_application = $args->application;
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'application':
				return $this->_application;
			case 'title':
				return $this->_title;
		}
	}

	/**
	 * @ignore magic method
	 */
	public function __set($key, $value) {
		switch ($key) {
			case 'title':
				$this->_title = $value;
		}
	}
}
