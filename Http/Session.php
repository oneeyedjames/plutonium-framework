<?php

namespace Plutonium\Http;

use Plutonium\Accessible;

class Session implements Accessible {
	protected $_namespaces = array();

	public function __construct() {
		if (session_status() == PHP_SESSION_NONE)
			session_start();

		$this->_namespaces =& $_SESSION;
	}

	public function __get($key) {
		return $this->get($key);
	}

	public function __set($key, $value) {
		$this->set($key, $value);
	}

	public function __isset($key) {
		return $this->has($key);
	}

	public function __unset($key) {
		$this->del($key);
	}

	public function get($key, $default = null, $namespace = 'default') {
		return $this->has($key, $namespace) ? $this->_namespaces[$namespace][$key] : $default;
	}

	public function set($key, $value = null, $namespace = 'default') {
		$this->_namespaces[$namespace][$key] = $value;
	}

	public function def($key, $value = null, $namespace = 'default') {
		if (!$this->has($key, $namespace)) $this->set($key, $value, $namespace);
	}

	public function has($key, $namespace = 'default') {
		return isset($this->_namespaces[$namespace][$key]);
	}

	public function del($key, $namespace = 'default') {
		unset($this->_namespaces[$namespace][$key]);
	}

	public function toArray($namespace = 'default') {
		return isset($this->_namespaces[$namespace]) ? $this->_namespaces[$namespace] : array();
	}
}
