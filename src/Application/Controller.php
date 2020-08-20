<?php
/**
 * @package plutonium\application
 */

namespace Plutonium\Application;

use Plutonium\Executable;

/**
 * Base class for all application controller objects.
 * Child classes must provide a method for each supported action. For instance,
 * an action named 'Default' must provide a method with the signature
 * defaultAction().
 * @property-read string $name Resource name
 * @property-read object $module The active Module object
 * @property-read object $request The active Request object
 * @property string $redirect URL for redirect after execution
 */
class Controller implements Executable {
	/**
	 * @ignore internal variable
	 */
	protected $_name = null;

	/**
	 * @ignore internal variable
	 */
	protected $_module = null;

	/**
	 * @ignore internal variable
	 */
	protected $_redirect = null;

	/**
	 * Expected args
	 *   - name: resource name
	 *   - module: active Module object
	 * @param object $args MutableCollection
	 */
	public function __construct($args) {
		$this->_name   = $args->name;
		$this->_module = $args->module;
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'name':
				return $this->_name;
			case 'module':
				return $this->_module;
			case 'request':
				return $this->_module->request;
			case 'redirect':
				return $this->_redirect;
		}
	}

	/**
	 * @ignore magic method
	 */
	public function __set($key, $value) {
		switch ($key) {
			case 'redirect':
				$this->_redirect = $value;
				break;
		}
	}

	/**
	 * Performs any initialization prior to execution.
	 */
	public function initialize() {
		$this->module->application->broadcastEvent('ctrl_init', $this);
	}

	/**
	 * Executes the action named in the HTTP request and sends HTTP redirect
	 * header if redirect URL is set. If the named action is not supported, no
	 * execution is performed.
	 */
	public function execute() {
		$request = $this->module->request;

		$action = strtolower($request->get('action', 'default'));
		$method = $action . 'Action';

		if (method_exists($this, $method))
			call_user_func(array($this, $method));

		$this->module->application->broadcastEvent('ctrl_exec', $this);

		if (!empty($this->redirect))
			header('Location: ' . $this->redirect);
	}

	/**
	 * Returns the named Model object.
	 * @param string $name OPTIONAL Resource name
	 * @return object Model object
	 */
	public function getModel($name = null) {
		return $this->module->getModel($name);
	}

	/**
	 * Returns the active View object.
	 * @return object View object
	 */
	public function getView() {
		return $this->module->getView();
	}
}
