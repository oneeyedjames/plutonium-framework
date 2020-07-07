<?php
/**
 * @package plutonium\http
 */

namespace Plutonium\Http;

use Plutonium\Accessible;

class Session implements Accessible {
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

	public function get($key, $default = null, $ns = 'pu') {
		return $this->has($key, $ns) ? $this->_namespaces[$ns][$key] : $default;
	}

	public function set($key, $value = null, $ns = 'pu') {
		$this->_namespaces[$ns][$key] = $value;
	}

	public function def($key, $value = null, $ns = 'pu') {
		if (!$this->has($key, $ns)) $this->set($key, $value, $ns);
	}

	public function has($key, $ns = 'pu') {
		return isset($this->_namespaces[$ns][$key]);
	}

	public function del($key, $ns = 'pu') {
		unset($this->_namespaces[$ns][$key]);
	}

	public function toArray($ns = 'pu') {
		return isset($this->_namespaces[$ns]) ? $this->_namespaces[$ns] : array();
	}
}
