<?php
/**
 * @package plutonium
 */

namespace Plutonium;

abstract class Component implements Installable, Renderable {
	protected $_name = null;

	public function __construct($name) {
		$this->_name = $name;
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'name':
				return $this->_name;
			default:
				return null;
		}
	}

	/**
	 * @ignore magic method
	 */
	public function __set($key, $value) {}

	/**
	 * @ignore magic method
	 */
	public function __isset($key) {
		return $key == 'name';
	}

	/**
	 * @ignore magic method
	 */
	public function __unset($key) {}
}
