<?php
/**
 * @package plutonium\event
 */

namespace Plutonium\Event;

class Event {
	protected $_name;
	protected $_data;

	public function __construct($name, $data = null) {
		$this->_name = $name;
		$this->_data = $data;
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'name':
			case 'data':
				return $this->{"_$key"};
		}
	}
}
