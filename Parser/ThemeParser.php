<?php

namespace Plutonium\Parser;

use Plutonium\Object;

class ThemeParser extends AbstractParser {
	public function headTag($args) {
		return $this->_application->document->getHeader();
	}

	public function moduleTag($args) {
		$out_args = new Object(array(
			'module_start' => $this->_application->theme->module_start,
			'module_close' => $this->_application->theme->module_close
		));

		return $this->_application->response->getModuleOutput($out_args);
	}

	public function widgetsTag($args) {
		$out_args = new Object(array(
			'widget_start' => $this->_application->theme->widget_start,
			'widget_close' => $this->_application->theme->widget_close,
			'widget_delim' => $this->_application->theme->widget_delim
		));

		return $this->_application->response->getWidgetOutput($args['location'], $out_args);
	}

	public function messageTag($args) {
		$session = $this->_application->session;
		$theme   = $this->_application->theme;

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
