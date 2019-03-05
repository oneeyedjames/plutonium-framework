<?php

namespace Plutonium\Event;

class Event {
	protected $_name;
	protected $_data;

	public function __construct($name, $data) {
		$this->_name = $name;
		$this->_data = $data;
	}

	public function __get($key) {
		switch ($key) {
			case 'name':
			case 'data':
				return $this->{"_$key"};
		}
	}
}