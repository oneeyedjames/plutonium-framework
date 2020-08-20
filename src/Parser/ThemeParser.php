<?php
/**
 * @package plutonium\parser
 */

namespace Plutonium\Parser;

use Plutonium\Collection\AccessibleCollection;
use Plutonium\Application\Widget;

class ThemeParser extends AbstractParser {
	/**
	 * Replaces a &lt;pu:head&gt; tag with the active document object's header.
	 * @param array $args Tag attributes
	 */
	public function headTag($args) {
		return $this->application->document->getHeader();
	}

	/**
	 * Replaces a &lt;pu:module&gt; tag with the active Module object's output.
	 * @param array $args Tag attributes
	 */
	public function moduleTag($args) {
		$out_args = new AccessibleCollection(array(
			'module_start' => $this->application->theme->module_start,
			'module_close' => $this->application->theme->module_close
		));

		return $this->application->response->getModuleOutput($out_args);
	}

	/**
	 * Replaces a &lt;pu:widgets&gt; tag with the output of all Widget objects
	 * for a given theme location.
	 * Expected attributes:
	 *   - location: theme location name
	 * @param array $args Tag attributes
	 */
	public function widgetsTag($args) {
		$out_args = new AccessibleCollection(array(
			'widget_start' => $this->application->theme->widget_start,
			'widget_close' => $this->application->theme->widget_close,
			'widget_delim' => $this->application->theme->widget_delim
		));

		return $this->application->response->getWidgetOutput($args['location'], $out_args);
	}

	/**
	 * Replaces a &lt;pu:widget&gt; tag with a named Widget object's output.
	 * Expected attributes:
	 *   - name: widget component name
	 *   - any widget-specific parameters
	 * @param array $args Tag attributes
	 */
	public function widgetTag($args) {
		if (!isset($args['name'])) return '';

		$widget = Widget::newInstance($this->application, $args['name']);

		unset($args['name']);

		foreach ($args as $key => $value)
			$widget->setVal($key, $value);

		return $widget->render();
	}

	/**
	 * Replaces a &lt;pu:message&gt; tag with the active Session object's
	 * message property. Message property will be unset afterward.
	 * @param array $args Tag attributes
	 */
	public function messageTag($args) {
		$session = $this->application->session;
		$theme   = $this->application->theme;

		if ($session->has('message')) {
			$output = $theme->message_start
					. $session->message
					. $theme->message_close;

			$session->del('message');

			return $output;
		}

		return '';
	}
}
