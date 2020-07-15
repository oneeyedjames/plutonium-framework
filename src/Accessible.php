<?php
/**
 * @package plutonium
 */

namespace Plutonium;

/**
 * Standardized interface for collections of key-value pairs
 */
interface Accessible {
	/**
	 * Checks whether the collection contains a non-NULL value for the key.
	 * @param string $key Unique key
	 * @return boolean TRUE is non-NULL value is present, FALSE otherwise
	 */
	public function has($key);

	/**
	 * Retrieves the value associated with the specified key. Default value is
	 * returned if the key is not set.
	 * @param string $key Unique key
	 * @param mixed $default OPTIONAL Default value to return if key is not set
	 * @return mixed Value associated with key, if set, default otherwise
	 */
	public function get($key, $default = null);

	/**
	 * Adds or updates a key-value pair in the collection.
	 * @param string $key Unique key
	 * @param mixed $value Value to associate with key
	 */
	public function set($key, $value = null);

	/**
	 * Adds a key-value pair to the collection ONLY IF the key is not set.
	 * @param string $key Unique key
	 * @param mixed $value Value to associate with key
	 */
	public function def($key, $value = null);

	/**
	 * Removes the specified key-value pair from the collection.
	 * @param string $key Unique key
	 */
	public function del($key);
}
