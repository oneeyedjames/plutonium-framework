<?php
/**
 * @package plutonium\collection
 */

namespace Plutonium\Collection;

/**
 * Read-write interface for collections of key-value pairs
 */
interface Mutable extends Accessible {
	/**
	 * Adds a key-value pair to the collection ONLY IF the key is not set.
	 * @param string $key Unique key
	 * @param mixed $value Value to associate with key
	 */
	public function def($key, $value = null);

	/**
	 * Adds or updates a key-value pair in the collection.
	 * @param string $key Unique key
	 * @param mixed $value Value to associate with key
	 */
	public function set($key, $value = null);

	/**
	 * Removes the specified key-value pair from the collection.
	 * @param string $key Unique key
	 */
	public function del($key);
}
