<?php
/**
 * @package plutonium\collection
 */

namespace Plutonium\Collection;

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
class AccessibleCollection implements Accessible, Collection, \ArrayAccess {
	use ArrayLike;

	/**
	 * @param mixed Array or Collection of key-value pairs
	 */
	public function __construct($data = null) {
		$this->_vars = self::normalize($data);
		$this->_readonly = true;
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		return $this[$key];
	}

	/**
	 * @ignore magic method
	 */
	public function __set($key, $value) {
		$this[$key] = $value;
	}

	/**
	 * @ignore magic method
	 */
	public function __isset($key) {
		return isset($this[$key]);
	}

	/**
	 * @ignore magic method
	 */
	public function __unset($key) {
		unset($this[$key]);
	}

	/**
	 * Checks whether the collection contains a non-NULL value for the key.
	 * @param string $key Unique key
	 * @return boolean TRUE is non-NULL value is present, FALSE otherwise
	 */
	public function has($key) {
		return isset($this[$key]);
	}

	/**
	 * Retrieves the value associated with the specified key. Default value is
	 * returned if the key is not set.
	 * @param string $key Unique key
	 * @param mixed $default OPTIONAL Default value to return if key is not set
	 * @return mixed Value associated with key, if set, default otherwise
	 */
	public function get($key, $default = null) {
		return isset($this[$key]) ? $this[$key] : $default;
	}
}
