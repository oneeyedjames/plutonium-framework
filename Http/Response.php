<?php

namespace Plutonium\Http;

use Plutonium\Object;

class Response {
	protected $_theme_output  = null;
	protected $_module_output = null;
	protected $_widget_output = array();

	public function getThemeOutput() {
		return $this->_theme_output;
	}

	public function setThemeOutput($output) {
		$this->_theme_output = $output;
	}

	public function getModuleOutput($args = null) {
		if (is_null($args))
			$args = new Object();

		return $args->get('module_start', '')
			 . $this->_module_output
			 . $args->get('module_close', '');
	}

	public function setModuleOutput($output) {
		$this->_module_output = $output;
	}

	public function getWidgetCount($location) {
		return (int) @count($this->_widget_output[$location]);
	}

	public function getWidgetOutput($location, $args = null) {
		if (is_null($args))
			$args = new Object();

		if (isset($this->_widget_output[$location])) {
			$outputs = array();

			foreach (array_keys($this->_widget_output[$location]) as $position) {
				$outputs[] = $args->get('widget_start', '')
						   . $this->_widget_output[$location][$position]
						   . $args->get('widget_close', '');
			}

			return implode($args->get('widget_delim', ''), $outputs);
		}

		return null;
	}

	public function setWidgetOutput($location, $output) {
		$this->_widget_output[$location][] = $output;
	}
}
