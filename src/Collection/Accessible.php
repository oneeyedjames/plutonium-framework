<?php
/**
 * @package plutonium
 */

namespace Plutonium;

/**
 * Read-only interface for collections of key-value pairs
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
}
