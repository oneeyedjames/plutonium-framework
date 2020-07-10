<?php
/**
 * @package plutonium\http
 */

namespace Plutonium\Http;

use Plutonium\Accessible;

class Session implements Accessible {
	/**
	 * @ignore internal variable
	 */
	protected $_namespaces = array();

	public function __construct() {
		if (session_status() == PHP_SESSION_NONE)
			session_start();

		$this->_namespaces =& $_SESSION;
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
	 * Retrieves the named key-value pair from the given namespace. Default
	 * value is returned if key is not set in namespace.
	 * @param string $key Unique key
	 * @param mixed $default OPTIONAL Default value for key
	 * @param string $hash OPTIONAL Namespace
	 * @return mixed Value for key
	 */
	public function get($key, $default = null, $ns = 'pu') {
		return $this->has($key, $ns) ? $this->_namespaces[$ns][$key] : $default;
	}

	/**
	 * Creates or updates the named key-value pair in the namespace.
	 * @param string $key Unique key
	 * @param mixed $value OPTIONAL value for key
	 * @param string $hash OPTIONAL Namespace
	 */
	public function set($key, $value = null, $ns = 'pu') {
		$this->_namespaces[$ns][$key] = $value;
	}

	/**
	 * Creates the named key-value pair in the given namespace if it does not
	 * already exist.
	 * @param string $key Unique key
	 * @param mixed $value OPTIONAL value for key
	 * @param string $hash OPTIONAL Namespace
	 */
	public function def($key, $value = null, $ns = 'pu') {
		if (!$this->has($key, $ns)) $this->set($key, $value, $ns);
	}

	/**
	 * Determines if the named key is set in the given namespace.
	 * @param string $key Unique key
	 * @param string $ns OPTIONAL Namespace
	 * @return boolean Whether key is set
	 */
	public function has($key, $ns = 'pu') {
		return isset($this->_namespaces[$ns][$key]);
	}

	/**
	 * Removes the named key-value pair from the given namespace.
	 * @param string $key Unique key
	 * @param string $hash OPTIONAL Namespace
	 */
	public function del($key, $ns = 'pu') {
		unset($this->_namespaces[$ns][$key]);
	}

	/**
	 * Returns all key-value pairs in the given namespace as an array.
	 * @return array Key-value pairs from namespace
	 */
	public function toArray($ns = 'pu') {
		return isset($this->_namespaces[$ns]) ? $this->_namespaces[$ns] : array();
	}
}
