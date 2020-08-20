<?php
/**
 * @package plutonium\application
 */

namespace Plutonium\Application;

use Plutonium\Collection\MutableCollection;
use Plutonium\Loader;
use Plutonium\Renderable;

abstract class Document extends MutableCollection implements Renderable {
	/**
	 * @ignore internal variable
	 */
	protected static $_path = null;

	/**
	 * Returns the file path for documents.
	 * @return string Absolute file path
	 */
	public static function getPath() {
		if (is_null(self::$_path) && defined('PU_PATH_BASE'))
			self::$_path = realpath(Application::getPath() . '/documents');

		return self::$_path;
	}

	/**
	 * Returns a new Document object. Base class is used if no matching custom
	 * class can be found.
	 * @param string $format Document format
	 * @param object $args MutableCollection of constructor args
	 * @return object Document object
	 */
	public static function newInstance($format, $args) {
		$name = strtolower($format);
		$type = ucfirst($name) . 'Document';
		$file = self::getPath() . DS . $name . '.php';

		return Loader::getClass($file, $type, __CLASS__, $args);
	}

	/**
	 * @ignore internal variable
	 */
	protected $_application = null;

	/**
	 * @ignore internal variable
	 */
	protected $_title = null;

	/**
	 * Expected args
	 *   - application: active Application object
	 * @param object $args MutableCollection
	 */
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
