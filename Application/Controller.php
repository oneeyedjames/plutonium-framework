<?php

namespace Plutonium\Application;

use Plutonium\Executable;

class Controller implements Executable {
	protected $_name     = null;
	protected $_module   = null;
	protected $_redirect = null;

	public function __construct($args) {
		$this->_name   = $args->name;
		$this->_module = $args->module;
	}

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

	public function __set($key, $value) {
		switch ($key) {
			case 'redirect':
				$this->_redirect = $value;
				break;
		}
	}

	public function initialize() {
		$this->module->application->broadcastEvent('ctrl_init', $this);
	}

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

	public function getModel($name = null) {
		return $this->module->getModel($name);
	}

	public function getView() {
		return $this->module->getView();
	}
}
