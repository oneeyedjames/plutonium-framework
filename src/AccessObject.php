<?php
/**
 * @package plutonium
 */

namespace Plutonium;

use Plutonium\ArrayLike;

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
class AccessObject
implements Accessible, \ArrayAccess, \Iterator, \Countable, \JsonSerializable {
	use ArrayLike;

	/**
	 * @param mixed Array or AccessObject of key-value pairs
	 */
	public function __construct($data = null) {
		$this->_vars = self::normalize($data);
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

	/**
	 * Adds or updates a key-value pair in the collection.
	 * @param string $key Unique key
	 * @param mixed $value Value to associate with key
	 */
	public function set($key, $value = null) {
		$this[$key] = $value;
	}

	/**
	 * Adds a key-value pair to the collection ONLY IF the key is not set.
	 * @param string $key Unique key
	 * @param mixed $value Value to associate with key
	 */
	public function def($key, $value = null) {
		if (!isset($this[$key])) $this[$key] = $value;
	}

	/**
	 * Removes the specified key-value pair from the collection.
	 * @param string $key Unique key
	 */
	public function del($key) {
		unset($this[$key]);
	}
}
