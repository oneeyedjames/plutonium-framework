<?php
/**
 * @package plutonium\application
 */

namespace Plutonium\Application;

use Plutonium\Collection\MutableCollection;

class Router {
	/**
	 * @ignore internal variable
	 */
	protected $_module = null;

	/**
	 * @param object $module The active Module object
	 */
	public function __construct($module) {
		$this->_module = $module;
	}

	/**
	 * Parses the requested resource, id, and layout from the given URL path.
	 * @param string $path Request URL path
	 * @return object MutableCollection of request parameters
	 */
	public function match($path) {
		$vars = new MutableCollection();

		$path = trim($path, FS);
		$path = empty($path) ? array() : explode(FS, $path);

		if (isset($path[0]))
			$vars->resource = $path[0];

		if (isset($path[1])) {
			if (is_numeric($path[1])) {
				$vars['id'] = intval($path[1]);
				$vars['layout'] = isset($path[2]) ? $path[2] : 'item';
			} else {
				$vars['layout'] = $path[1];
			}
		} else {
			$vars['layout'] = 'default';
		}

		return $vars;
	}

	/**
	 * Constructs a URL string for the given request parameters
	 * @param object $args MutableCollection of request parameters
	 * @return string URL string
	 */
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
