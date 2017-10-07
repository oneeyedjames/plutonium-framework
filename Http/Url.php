<?php

namespace Plutonium\Http;

use Plutonium\Object;

class Url extends Object {
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

	public function __construct($vars = null) {
		parent::__construct($vars);
	}

	public function query() {
		return empty($this->_vars) ? '' : '?' . http_build_query($this->_vars);
	}

	public function toString() {
		$host = self::$_domain;

		if (isset($this->module))
			$host = $this->module . '.' . $host;

		// TODO add site name

		$url = self::$_scheme . '://' . $host . FS . self::$_path;

		if ($this->has('resource')) $url .= FS . $this->get('resource');
		if ($this->has('id'))       $url .= FS . $this->get('id');
		if ($this->has('layout'))   $url .= FS . $this->get('layout');
		if ($this->has('format'))   $url .= FS . $this->get('format');

		return $url;
	}
}
