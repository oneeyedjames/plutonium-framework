<?php
/**
 * @package plutonium\http
 */

namespace Plutonium\Http;

use Plutonium\Collection\AccessibleObject;

class Response {
	/**
	 * @ignore interal variable
	 */
	protected $_theme_output  = null;

	/**
	 * @ignore interal variable
	 */
	protected $_module_output = null;

	/**
	 * @ignore interal variable
	 */
	protected $_widget_output = array();

	/**
	 * Returns the complete theme output.
	 * @return string Complete theme output
	 */
	public function getThemeOutput() {
		return $this->_theme_output;
	}

	/**
	 * Sets the rendered theme output.
	 * @param string $output Rendered theme output
	 */
	public function setThemeOutput($output) {
		$this->_theme_output = $output;
	}

	/**
	 * Returns the complete module output.
	 * Expected args:
	 *   - module_start: inserted before module
	 *   - module_close: inserted after module
	 * @param object $args ArrayAccess object
	 * @return string Complete module output
	 */
	public function getModuleOutput($args = null) {
		if (is_null($args))
			$args = new AccessibleObject();

		return $args->get('module_start', '')
			 . $this->_module_output
			 . $args->get('module_close', '');
	}

	/**
	 * Sets the rendered module output.
	 * @param string $output Rendered module output
	 */
	public function setModuleOutput($output) {
		$this->_module_output = $output;
	}

	/**
	 * Returns the number of widgets rendered in the named theme location.
	 * @param string $location Theme location name
	 * @return integer Number of widgets
	 */
	public function getWidgetCount($location) {
		return (int) @count($this->_widget_output[$location]);
	}

	/**
	 * Returns the collective widget output for the named theme location.
	 * Expected args:
	 *   - widget_start: inserted before each widget
	 *   - widget_close: inserted after each widget
	 *   - widget_delim: inserted between each widget
	 * @param string $location Theme location name
	 * @param object $args ArrayAccess object
	 * @return string Collective widget output
	 */
	public function getWidgetOutput($location, $args = null) {
		if (is_null($args))
			$args = new AccessibleObject();

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

	/**
	 * Adds the rendered widget output to the named theme location.
	 * @param string $location Theme location name
	 * @param string $output Rendered widget output
	 */
	public function setWidgetOutput($location, $output) {
		$this->_widget_output[$location][] = $output;
	}
}
