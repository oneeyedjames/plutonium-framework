<?php
/**
 * @package plutonium\collection
 */

namespace Plutonium\Collection;

/**
 * Reference implementation of Mutable interface.
 *
 * Key-value pairs can be accessed as object properties:
 *   - $object->key
 *
 * Or as array offsets:
 *   - $object['key']
 *
 * Objects can also be used in foreach loops and passed to the count() function.
 */
class MutableCollection extends AccessibleCollection implements Mutable {
	/**
	 * @param mixed Array or Collection of key-value pairs
	 */
	public function __construct($data = null) {
		parent::__construct($data);
		$this->_readonly = false;
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
	 * Adds or updates a key-value pair in the collection.
	 * @param string $key Unique key
	 * @param mixed $value Value to associate with key
	 */
	public function set($key, $value = null) {
		$this[$key] = $value;
	}

	/**
	 * Removes the specified key-value pair from the collection.
	 * @param string $key Unique key
	 */
	public function del($key) {
		unset($this[$key]);
	}
}
