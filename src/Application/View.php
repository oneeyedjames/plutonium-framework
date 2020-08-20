<?php
/**
 * @package plutonium\application
 */

namespace Plutonium\Application;

use Plutonium\Renderable;

/**
 * Base class for all application controller objects.
 * Child classes must provide a method for each supported action. For instance,
 * an action named 'Default' must provide a method with the signature
 * defaultAction().
 * @property-read string $name Resource name
 * @property-read object $module The active Module object
 * @property-read string $output Rendered view output
 * @property string $layout Current layout name
 * @property string $format Current format name
 */
class View implements Renderable {
	/**
	 * @ignore internal variable
	 */
	protected $_name = null;

	/**
	 * @ignore internal variable
	 */
	protected $_vars = null;

	/**
	 * @ignore internal variable
	 */
	protected $_layout = null;

	/**
	 * @ignore internal variable
	 */
	protected $_format = null;

	/**
	 * @ignore internal variable
	 */
	protected $_output = null;

	/**
	 * @ignore internal variable
	 */
	protected $_module = null;

	/**
	 * Expected args
	 *   - name: resource name
	 *   - module: active Module object
	 * @param object $args MutableObject
	 */
	public function __construct($args) {
		$this->_name = $args->name;
		$this->_vars = array();

		$this->_module = $args->module;
		$this->_layout = $args->module->request->get('layout', 'default');
		$this->_format = $args->module->request->get('format', 'html');
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'name':
			case 'module':
			case 'layout':
			case 'format':
				return $this->{"_$key"};
			case 'output':
				return $this->render();
			default:
				return $this->getVar($key);
		}
	}

	/**
	 * @ignore magic method
	 */
	public function __set($key, $value) {
		switch ($key) {
			case 'layout':
				$this->_layout = $value;
				break;
			case 'format':
				$this->_format = $value;
				break;
			default:
				$this->setVal($key, $value);
				break;
		}
	}

	/**
	 * Renders the view and returns output.
	 * @return string Rendered view output
	 */
	public function render() {
		if (is_null($this->_output)) {
			$layout = strtolower($this->layout);
			$format = strtolower($this->format);

			$method = $layout . 'Layout';

			if (method_exists($this, $method))
				call_user_func(array($this, $method));

			if ($file = $this->getLayout()) {
				if (stripos($file, '.phar') !== false)
					$file = 'phar://' . $file;

				ob_start();

				include $file;

				$this->_output = ob_get_contents();

				ob_end_clean();
			} else {
				$message = sprintf("Layout file not found");
				trigger_error($message, E_USER_ERROR);
			}

			$this->module->application->broadcastEvent('view_render', $this);
		}

		return $this->_output;
	}

	/**
	 * Returns file path for layout template matching the current request.
	 * @return string Absolute file path
	 */
	public function getLayout() {
		$layout = strtolower($this->layout);
		$format = strtolower($this->format);

		$name = strtolower($this->name);
		$path = 'views' . DS . $name . DS . 'layouts';

		$files = [
			$path . DS . $layout . '.' . $format . '.php',
			$path . DS . 'default.' . $format . '.php'
		];

		return Module::getLocator()->locateFile($this->module->name, $files);
	}

	/**
	 * Translates text according to the acive Locale object.
	 * @param string $text Original text
	 * @return string Translated text
	 */
	public function localize($text) {
		return $this->module->localize($text);
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
	 * Retrieves a named key-value pair. Default value is returned if key is not
	 * set.
	 * @param string $key Unique key
	 * @param mixed $default OPTIONAL Default value for key
	 * @return mixed Value for key
	 */
	public function getVar($key, $default = null) {
		return isset($this->_vars[$key]) ? $this->_vars[$key] : $default;
	}

	/**
	 * Creates or updates a key-value pair by value.
	 * @param string $key Unique key
	 * @param mixed $var value for key
	 */
	public function setVal($key, $var) {
		$this->_vars[$key] = $var;
	}

	/**
	 * Creates or updates a key-value pair by reference.
	 * @param string $key Unique key
	 * @param mixed $var value for key
	 */
	public function setRef($key, &$var) {
		$this->_vars[$key] = $var;
	}
}
