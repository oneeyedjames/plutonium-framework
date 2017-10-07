<?php

namespace Plutonium;

abstract class Component implements Visible {
	protected $_application = null;

	protected $_name = null;

	public function __construct($type, $args) {
		$this->_application = $args->application;
		$this->_application->locale->load($args->name, "{$type}s");

		$this->_name = $args->name;
	}

	public function __get($key) {
		switch ($key) {
			case 'application':
				return $this->_application;
			case 'name':
				return $this->_name;
			default:
				return null;
		}
	}

	public abstract function install();
}
