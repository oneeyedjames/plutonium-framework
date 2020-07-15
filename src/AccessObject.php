<?php
/**
 * @package plutonium
 */

namespace Plutonium;

use function Plutonium\Functions\is_assoc;

/**
 * Reference implementation of Accessible interface.
 *
 * Key-value pairs can be accessed as object properties:
 *   - $object->key
 *
 * Or as array offsets:
 *   - $object['key']
 *
 * Objects can also be used in foreach loops and passed to the count() function.
 */
class AccessObject implements Accessible, \ArrayAccess, \Iterator, \Countable {
	/**
	 * @ignore internal variable
	 */
	protected $_vars;

	/**
	 * @param mixed Array or AccessObject of key-value pairs
	 */
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

	/**
	 * Checks whether the collection contains a non-NULL value for the key.
	 * @param string $key Unique key
	 * @return boolean TRUE is non-NULL value is present, FALSE otherwise
	 */
	public function has($key) {
		return array_key_exists($key, $this->_vars);
	}

	/**
	 * Retrieves the value associated with the specified key. Default value is
	 * returned if the key is not set.
	 * @param string $key Unique key
	 * @param mixed $default OPTIONAL Default value to return if key is not set
	 * @return mixed Value associated with key, if set, default otherwise
	 */
	public function get($key, $default = null) {
		return $this->has($key) ? $this->_vars[$key] : $default;
	}

	/**
	 * Adds or updates a key-value pair in the collection.
	 * @param string $key Unique key
	 * @param mixed $value Value to associate with key
	 */
	public function set($key, $value = null) {
		$this->_vars[$key] = is_assoc($value) ? new self($value) : $value;
	}

	/**
	 * Adds a key-value pair to the collection ONLY IF the key is not set.
	 * @param string $key Unique key
	 * @param mixed $value Value to associate with key
	 */
	public function def($key, $value = null) {
		if (!$this->has($key)) $this->_vars[$key] = $value;
	}

	/**
	 * Removes the specified key-value pair from the collection.
	 * @param string $key Unique key
	 */
	public function del($key) {
		if ($this->has($key)) unset($this->_vars[$key]);
	}

	/**
	 * @ignore library interface method
	 */
	public function offsetGet($key) {
		return $this->get($key);
	}

	/**
	 * @ignore library interface method
	 */
	public function offsetSet($key, $value) {
		$this->set($key, $value);
	}

	/**
	 * @ignore library interface method
	 */
	public function offsetExists($key) {
		return $this->has($key);
	}

	/**
	 * @ignore library interface method
	 */
	public function offsetUnset($key) {
		$this->del($key);
	}

	/**
	 * @ignore library interface method
	 */
	public function current() {
		return current($this->_vars);
	}

	/**
	 * @ignore library interface method
	 */
	public function key() {
		return key($this->_vars);
	}

	/**
	 * @ignore library interface method
	 */
	public function next() {
		return next($this->_vars);
	}

	/**
	 * @ignore library interface method
	 */
	public function rewind() {
		return reset($this->_vars);
	}

	/**
	 * @ignore library interface method
	 */
	public function valid() {
		return key($this->_vars) !== null;
	}

	/**
	 * @ignore library interface method
	 */
	public function count() {
		return count($this->_vars);
	}
}
