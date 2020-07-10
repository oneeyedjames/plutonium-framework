<?php
/**
 * @package plutonium\http
 */

namespace Plutonium\Http;

use Plutonium\AccessObject;

/**
 * @property-read string $query URL query string
 */
class Url extends AccessObject {
	/**
	 * @ignore interal variable
	 */
	protected static $_scheme = null;

	/**
	 * @ignore interal variable
	 */
	protected static $_domain = null;

	/**
	 * @ignore interal variable
	 */
	protected static $_path = null;

	/**
	 * Parse the URL scheme, base hostname, and root path from the given URL.
	 * @param string $base_url URL string
	 */
	public static function initialize($base_url) {
		$parts = parse_url($base_url);

		if (isset($parts['scheme'])) self::$_scheme = $parts['scheme'];
		if (isset($parts['host']))   self::$_domain = $parts['host'];
		if (isset($parts['path']))   self::$_path   = trim($parts['path'], FS);
	}

	/**
	 * Builds a URL object representing the given Request object.
	 * @param object $request The active Request object
	 * @return object URL object
	 */
	public static function newInstance($request) {
		$vars = $request->toArray('post') + $request->toArray('get');

		return new self($vars);
	}

	/**
	 * Builds a query string based on the provided parameters.
	 * @param array $vars Query parameters
	 * @return string Query string
	 */
	public static function buildQuery($vars) {
		return empty($vars) ? '' : '?' . http_build_query($vars);
	}

	/**
	 * @ignore magic method
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * Determines if the named query parameter is set.
	 * @param string $key Parameter name
	 * @return boolean Whether the parameter is set
	 */
	public function has($key) {
		switch ($key) {
			case 'query': return true;
			default: return parent::has($key);
		}
	}

	/**
	 * Retrieves the named query parameter. Default value is returned if
	 * parameter is not set.
	 * @param string $key Parameter name
	 * @param mixed $default OPTIONAL Default parameter value
	 * @return mixed Parameter value
	 */
	public function get($key, $default = null) {
		switch ($key) {
			case 'query': return self::buildQuery($this->_vars);
			default: return parent::get($key, $default);
		}
	}

	/**
	 * Creates or updates the named query parameter.
	 * @param string $key Parameter name
	 * @param mixed $value OPTIONAL Parameter value
	 */
	public function set($key, $value = null) {
		switch ($key) {
			case 'query': trigger_error("Cannot write to readonly parameter: $key", E_USER_WARNING);
			default: return parent::set($key, $value);
		}
	}

	/**
	 * Creates the named query parameter if it is not already set.
	 * @param string $key Parameter name
	 * @param mixed $value OPTIONAL Parameter value
	 */
	public function def($key, $value = null) {
		switch ($key) {
			case 'query': return;
			default: return parent::def($key, $value);
		}
	}

	/**
	 * Removes the named query parameter.
	 * @param string $key Parameter name
	 */
	public function del($key) {
		switch ($key) {
			case 'query': return;
			default: return parent::del($key);
		}
	}

	/**
	 * Returns the URL formatted as a string.
	 * @return string URL string
	 */
	public function toString() {
		$fqdn = self::$_domain;

		if (isset($this->host))   $fqdn = $this->host  . '.' . $fqdn;
		if (isset($this->module)) $fqdn = $this->module . '.' . $fqdn;

		$path = empty(self::$_path) ? '' : FS . self::$_path;

		if ($this->has('resource')) $path .= FS . $this->get('resource');
		if ($this->has('id'))       $path .= FS . $this->get('id');
		if ($this->has('layout'))   $path .= FS . $this->get('layout');

		$vars = $this->_vars;
		unset($vars['resource'], $vars['id'], $vars['layout']);

		$query = self::buildQuery($vars);

		return self::$_scheme . '://' . $fqdn . $path . $query;
	}
}
