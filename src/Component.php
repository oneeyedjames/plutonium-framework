<?php

namespace Plutonium;

abstract class Component implements Installable, Renderable {
	protected $_name = null;

	public function __construct($name) {
		$this->_name = $name;
	}

	public function __get($key) {
		switch ($key) {
			case 'name':
				return $this->_name;
			default:
				return null;
		}
	}

	public function __set($key, $value) {}

	public function __isset($key) {
		return $key == 'name';
	}

	public function __unset($key) {}
}
