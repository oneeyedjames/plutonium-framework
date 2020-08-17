<?php
/**
 * @package plutonium
 */

namespace Plutonium\Collection;

use function Plutonium\Functions\is_traversable;

/**
 * Satisfies Interfaces:
 *   - ArrayAccess
 *   - Iterator
 *   - Traversable
 *   - Countable
 *   - JsonSerializable
 */
trait ArrayLike {
	private static $_immutable = 'Cannot set value on immutable collection.';

	public static function normalize($data) {
		if (is_traversable($data)) {
			return iterator_to_array($data);
		} elseif (is_object($data)) {
			return get_object_vars($data);
		} elseif (is_array($data)) {
			return $data;
		}

		return [];
	}

	protected $_readonly = false;

	/**
	 * @ignore internal variable
	 */
	protected $_vars;

	/**
	 * @ignore library interface method
	 */
	public function offsetGet($key) {
		if (isset($this->_vars[$key]))
			return $this->_vars[$key];
	}

	/**
	 * @ignore library interface method
	 */
	public function offsetSet($key, $value) {
		if ($this->_readonly)
			return trigger_error(self::$_immutable, E_USER_ERROR);

		$this->_vars[$key] = $value;
	}

	/**
	 * @ignore library interface method
	 */
	public function offsetExists($key) {
		return isset($this->_vars[$key]);
	}

	/**
	 * @ignore library interface method
	 */
	public function offsetUnset($key) {
		if ($this->_readonly)
			return trigger_error(self::$_immutable, E_USER_ERROR);

		if (isset($this->_vars[$key]))
			unset($this->_vars[$key]);
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

	/**
	 * @ignore library interface method
	 */
	public function jsonSerialize() {
		return $this->_vars;
	}
}
