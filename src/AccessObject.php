<?php
/**
 * @package plutonium
 */

namespace Plutonium;

use function Plutonium\Functions\is_assoc;

class AccessObject implements Accessible, \ArrayAccess, \Iterator, \Countable {
	protected $_vars;

	// Constructor
	public function __construct($data = null) {
		$this->_vars = array();

		if ($data instanceof AccessObject) {
			$this->_vars = $data->_vars;
		} elseif (is_assoc($data)) {
			foreach ($data as $key => $value)
				$this->set($key, $value);
		}
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		return $this->get($key);
	}

	/**
	 * @ignore magic method
	 */
	public function __set($key, $value) {
		$this->set($key, $value);
	}

	/**
	 * @ignore magic method
	 */
	public function __isset($key) {
		return $this->has($key);
	}

	/**
	 * @ignore magic method
	 */
	public function __unset($key) {
		$this->del($key);
	}

	// Plutonium\Accessible methods
	public function has($key) {
		return array_key_exists($key, $this->_vars);
	}

	public function get($key, $default = null) {
		return $this->has($key) ? $this->_vars[$key] : $default;
	}

	public function set($key, $value = null) {
		$this->_vars[$key] = is_assoc($value) ? new self($value) : $value;
	}

	public function def($key, $value = null) {
		if (!$this->has($key)) $this->_vars[$key] = $value;
	}

	public function del($key) {
		if ($this->has($key)) unset($this->_vars[$key]);
	}

	// ArrayAccess methods
	public function offsetGet($key) {
		return $this->get($key);
	}

	public function offsetSet($key, $value) {
		$this->set($key, $value);
	}

	public function offsetExists($key) {
		return $this->has($key);
	}

	public function offsetUnset($key) {
		$this->del($key);
	}

	// Iterator methods
	public function current() {
		return current($this->_vars);
	}

	public function key() {
		return key($this->_vars);
	}

	public function next() {
		return next($this->_vars);
	}

	public function rewind() {
		return reset($this->_vars);
	}

	public function valid() {
		return key($this->_vars) !== null;
	}

	// Countable method
	public function count() {
		return count($this->_vars);
	}
}
