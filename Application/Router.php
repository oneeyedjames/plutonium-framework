<?php

namespace Plutonium\Application;

class Router {
	protected $_module = null;

	public function __construct($module) {
		$this->_module = $module;
	}

	public function match($path) {
		$path = empty($path) ? array() : explode(FS, trim($path, FS));
		$vars = array();

		if (isset($path[0]))
			$vars['resource'] = $path[0];

		if (isset($path[1])) {
			if (is_numeric($path[1])) {
				$vars['id'] = intval($path[1]);

				if (isset($path[2]))
					$vars['layout'] = $path[2];
				else
					$vars['layout'] = 'details';
			} else {
				$vars['layout'] = $path[1];
			}
		} else {
			$vars['layout'] = 'default';
		}

		return $vars;
	}

	public function build($args) {
		$path = '';

		if (isset($args->resource)) {
			$path = $args->resource;

			if (isset($args->id))     $path .= FS . $args->id;
			if (isset($args->layout)) $path .= FS . $args->layout;
		}

		return $path;
	}
}
