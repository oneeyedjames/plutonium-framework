<?php

namespace Plutonium;

use Plutonium\Loader;
use Plutonium\Visible;
use Plutonium\Application\Application;

abstract class Document extends Object implements Visible {
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

	protected $_type = null;
	protected $_lang = null;

	protected $_title   = null;
	protected $_descrip = null;

	public function __construct($args) {
		$this->_application = $args->application;
	}

	public function __get($key) {
		switch ($key) {
			case 'application':
				return $this->_application;
			case 'title':
				return $this->_title;
		}
	}

	public function __set($key, $value) {
		switch ($key) {
			case 'title':
				$this->_title = $value;
		}
	}
}
