<?php
/**
 * @package plutonium\event
 */

namespace Plutonium\Event;

/**
 * @property-read string $name Event name
 * @property-read mixed $data User-defined data
 */
class Event {
	/**
	 * @ignore internal variable
	 */
	protected $_name;

	/**
	 * @ignore internal variable
	 */
	protected $_data;

	/**
	 * @param string $name Event name
	 * @param mixed $data OPTIONAL User-defined data
	 */
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
