<?php
/**
 * @package plutonium
 */

namespace Plutonium;

use Plutonium\Collection\Collection;
use Plutonium\Collection\ArrayLike;

class Exception extends \ErrorException implements \ArrayAccess, Collection {
	use ArrayLike;

	public function __construct(
		$message = '',
		$code = -1,
		$severity = E_USER_ERROR,
		$data = []
	) {
		parent::__construct($message, $code, $severity);
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
	public function __set($key, $value) {}

	/**
	 * @ignore magic method
	 */
	public function __isset($key) {
		return isset($this[$key]);
	}

	/**
	 * @ignore magic method
	 */
	public function __unset($key) {}

	/**
	 * @ignore interface method
	 */
	public function offsetSet($key, $value) {}

	/**
	 * @ignore interface method
	 */
	public function offsetUnset($key) {}
}
