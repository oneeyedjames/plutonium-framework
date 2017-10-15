<?php

namespace Plutonium\Application;

use Plutonium\Component;

abstract class ApplicationComponent extends Component {
	protected $_application = null;

	public function __construct($type, $args) {
		parent::__construct($args->name);

		$this->_application = $args->application;
		$this->_application->locale->load($args->name, "{$type}s");
	}

	public function __get($key) {
		switch ($key) {
			case 'application':
				return $this->_application;
			default:
				return parent::__get($key);
		}
	}
}
