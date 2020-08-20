<?php
/**
 * @package plutonium\application
 */

namespace Plutonium\Application;

use Plutonium\Component;

/**
 * @property-read object $application The active Application object
 * @property-read string $name The component name
 */
abstract class ApplicationComponent extends Component {
	protected $_application = null;

	/**
	 * @param string $type The component type
	 * @param object $args MutableObject containing name and application object
	 */
	public function __construct($type, $args) {
		parent::__construct($args->name);

		$this->_application = $args->application;
		$this->_application->locale->load($args->name, "{$type}s");
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'application':
				return $this->_application;
			default:
				return parent::__get($key);
		}
	}
}
