<?php

namespace Plutonium\Http;

use Plutonium\AccessObject;

class Url extends AccessObject {
	protected static $_scheme = null;
	protected static $_domain = null;
	protected static $_path   = null;

	public static function initialize($base_url) {
		$parts = parse_url($base_url);

		if (isset($parts['scheme'])) self::$_scheme = $parts['scheme'];
		if (isset($parts['host']))   self::$_domain = $parts['host'];
		if (isset($parts['path']))   self::$_path   = trim($parts['path'], FS);
	}

	public static function newInstance($request) {
		$vars = $request->toArray('post') + $request->toArray('get');

		return new self($vars);
	}

	public static function buildQuery($vars) {
		return empty($vars) ? '' : '?' . http_build_query($vars);
	}

	public function has($key) {
		switch ($key) {
			case 'query': return true;
			default: return parent::has($key);
		}
	}

	public function get($key, $default = null) {
		switch ($key) {
			case 'query': return self::buildQuery($this->_vars);
			default: return parent::get($key, $default);
		}
	}

	public function set($key, $value = null) {
		switch ($key) {
			case 'query': trigger_error("Cannot write to readonly parameter: $key", E_USER_WARNING);
			default: return parent::set($key, $value);
		}
	}

	public function def($key, $value = null) {
		switch ($key) {
			case 'query': return;
			default: return parent::def($key, $value);
		}
	}

	public function del($key) {
		switch ($key) {
			case 'query': return;
			default: return parent::del($key);
		}
	}

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

	public function __toString() {
		return $this->toString();
	}
}
