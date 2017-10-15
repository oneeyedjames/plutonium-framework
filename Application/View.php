<?php

namespace Plutonium\Application;

use Plutonium\Renderable;

class View implements Renderable {
	protected $_name   = null;
	protected $_vars   = null;
	protected $_layout = null;
	protected $_format = null;
	protected $_output = null;
	protected $_module = null;

	public function __construct($args) {
		$this->_name = $args->name;
		$this->_vars = array();

		$this->_module = $args->module;

		$this->_layout = $this->_module->request->get('layout', 'default');
		$this->_format = $this->_module->request->get('format', 'html');
	}

	public function __get($key) {
		switch ($key) {
			case 'name':
				return $this->_name;
			case 'layout':
				return $this->_layout;
			case 'format':
				return $this->_format;
			default:
				return $this->getVar($key);
		}
	}

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

	public function render() {
		$path   = $this->_module->path;
		$name   = strtolower($this->_name);
		$layout = strtolower($this->_layout);
		$format = strtolower($this->_format);

		$method = $layout . 'Layout';

		if (method_exists($this, $method))
			call_user_func(array($this, $method));

		$file = $path . DS . 'views' . DS . $name . DS
			  . 'layouts' . DS . $layout . '.' . $format . '.php';

		if (is_file($file)) {
			ob_start();

			include $file;

			$this->_output = ob_get_contents();

			ob_end_clean();
		} else {
			$message = sprintf("Resource does not exist: %s.", $file);
			trigger_error($message, E_USER_ERROR);
		}

		return $this->_output;
	}

	public function localize($text) {
		return $this->_module->application->locale->localize($text);
	}

	public function getModel($name = null) {
		return $this->_module->getModel($name);
	}

	public function getVar($key, $default = null) {
		return isset($this->_vars[$key]) ? $this->_vars[$key] : $default;
	}

	public function setVal($key, $var) {
		$this->_vars[$key] = $var;
	}

	public function setRef($key, &$var) {
		$this->_vars[$key] = $var;
	}
}
