<?php
/**
 * @package plutonium\parser
 */

namespace Plutonium\Parser;

use Plutonium\AccessObject;
use Plutonium\Application\Widget;

class ThemeParser extends AbstractParser {
	public function headTag($args) {
		return $this->application->document->getHeader();
	}

	public function moduleTag($args) {
		$out_args = new AccessObject(array(
			'module_start' => $this->application->theme->module_start,
			'module_close' => $this->application->theme->module_close
		));

		return $this->application->response->getModuleOutput($out_args);
	}

	public function widgetsTag($args) {
		$out_args = new AccessObject(array(
			'widget_start' => $this->application->theme->widget_start,
			'widget_close' => $this->application->theme->widget_close,
			'widget_delim' => $this->application->theme->widget_delim
		));

		return $this->application->response->getWidgetOutput($args['location'], $out_args);
	}

	public function widgetTag($args) {
		if (!isset($args['name'])) return '';

		$widget = Widget::newInstance($this->application, $args['name']);

		unset($args['name']);

		foreach ($args as $key => $value)
			$widget->setVal($key, $value);

		return $widget->render();
	}

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
