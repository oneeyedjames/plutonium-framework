<?php

namespace Plutonium\Http;

use Plutonium\AccessObject;
use Plutonium\Accessible;

class Request implements Accessible {
	protected static $_method_map = array(
		'GET'  => array('HEAD', 'OPTIONS'),
		'POST' => array('PUT', 'DELETE')
	);

	protected static function isMapped($method, $alias) {
		return in_array($method, self::$_method_map[$alias]);
	}

	protected $_uri    = null;
	protected $_method = null;
	protected $_hashes = array();

	public function __construct() {
		$this->_uri    = $_SERVER['REQUEST_URI'];
		$this->_method = $_SERVER['REQUEST_METHOD'];
		$this->_hashes = array(
			'request' => $_REQUEST,
			'get'     => $_GET,
			'post'    => $_POST,
			//'files'   => $_FILES,
			'cookies' => $_COOKIE,
			'headers' => array()
		);

		switch ($this->_method) {
			case 'GET':
			case 'POST':
				$hash = strtolower($this->_method);

				if ($method = $this->get('_method', false, $hash)) {
					if (self::isMapped($method, $this->_method)) {
						$this->del('_method', $hash);
						$this->del('_method');

						$this->_method = $method;
					}
				}
				break;
			case 'PUT':
				parse_str(file_get_contents('php://input'), $query);
				$this->_hashes['request'] = array_merge_recursive($_REQUEST, $query);
				break;
		}

		foreach ($_SERVER as $key => $value) {
			if (strtoupper($key) == 'HTTP_HOST') {
				list($host, $port) = array_pad(explode(':', $value, 2), 2, 80);

				$this->set('Host', $host, 'headers');
				$this->set('Port', intval($port), 'headers');
			} elseif (stripos($key, 'HTTP_') === 0) {
				$words = str_replace('_', ' ', substr($key, 5));
				$words = ucwords(strtolower($words));
				$words = str_replace(' ', '-', $words);

				$this->set($words, $value, 'headers');
			}
		}

		$path = explode(FS, trim(parse_url($this->uri, PHP_URL_PATH), FS));

		if (!empty($path)) {
			$last =& $path[count($path) - 1];

			if (($pos = strrpos($last, '.')) !== false) {
				$this->format = substr($last, $pos + 1);
				$last = substr($last, 0, $pos);
			}
		}

		$this->_uri = FS . implode(FS, $path);
	}

	// public function parseHost($host, $base) {
	// 	$args = new AccessObject();
	//
	// 	if ($base == $host) return $args;
	//
	// 	if (substr($host, -strlen($base)) == $base) {
	// 		$diff = explode('.', trim(substr($host, 0, -strlen($base)), '.'));
	//
	// 		if ($host = array_pop($diff))
	// 			$args->host = $host;
	//
	// 		if ($module = array_pop($diff))
	// 			$args->module = $module;
	// 	}
	// }

	// protected function _initFormat() {
	// 	if (isset($_SERVER['HTTP_ACCEPT'])) {
	// 		$type = $this->_parseAcceptHeader($_SERVER['HTTP_ACCEPT']);
	//
	// 		foreach ($type as $match => $group) {
	// 			foreach ($group as $type) {
	// 				switch ($type) {
	// 					case 'text/plain':
	// 						$this->def('format', 'txt');
	// 						return;
	// 					case 'text/html':
	// 					case 'application/xhtml+xml':
	// 						$this->def('format', 'html');
	// 						return;
	// 					case 'text/xml':
	// 					case 'application/xml': // unofficial
	// 						$this->def('format', 'xml');
	// 						return;
	// 					case 'text/json': // unofficial
	// 					case 'application/json':
	// 						$this->def('format', 'json');
	// 						return;
	// 					case 'application/rss+xml':
	// 						$this->def('format', 'rss');
	// 						return;
	// 					case 'application/atom+xml':
	// 						$this->def('format', 'atom');
	// 						return;
	// 				}
	// 			}
	// 		}
	// 	}
	// }

	// protected function _initLanguage() {
	// 	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	// 		var_dump($_SERVER['HTTP_ACCEPT_LANGUAGE'],
	// 			$this->_parseAcceptHeader($_SERVER['HTTP_ACCEPT_LANGUAGE']));
	// 	}
	// }

	// protected function _initEncoding() {
	// 	if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
	// 		var_dump($_SERVER['HTTP_ACCEPT_ENCODING'],
	// 			$this->_parseAcceptHeader($_SERVER['HTTP_ACCEPT_ENCODING']));
	// 	}
	// }

	/**
	 * Helper function for parsing HTTP Accept and Accept-____ headers
	 */
	// protected function _parseAcceptHeader($header) {
	// 	$result = array();
	//
	// 	$list = explode(',', $header);
	//
	// 	foreach ($list as $item) {
	// 		$params = array();
	//
	// 		if (strpos($item, ';') !== false) {
	// 			$query = explode(';', $item);
	//
	// 			$name = trim(array_shift($query));
	//
	// 			foreach ($query as $param) {
	// 				list($key, $value) = explode('=', $param);
	// 				$params[trim($key)] = trim($value);
	// 			}
	// 		} else {
	// 			$name = trim($item);
	// 		}
	//
	// 		$q = floatval(isset($params['q']) ? $params['q'] : 1);
	//
	// 		$result[sprintf("%1.3f", $q)][] = $name;
	//
	// 		// TODO manage remaining parameters
	// 		// unset($params['q']);
	// 	}
	//
	// 	krsort($result);
	//
	// 	return $result;
	// }

	public function __get($key) {
		switch ($key) {
			case 'uri':
				return $this->_uri;
			case 'method':
				return $this->_method;
			default:
				return $this->get($key);
		}
	}

	public function __set($key, $value) {
		switch ($key) {
			case 'uri':
			case 'method':
				break;
			default:
				$this->set($key, $value);
				break;
		}
	}

	public function __isset($key) {
		switch ($key) {
			case 'uri':
			case 'method':
				return true;
			default:
				return $this->has($key);
		}
	}

	public function __unset($key) {
		switch ($key) {
			case 'uri':
			case 'method':
				break;
			default:
				$this->del($key);
				break;
		}
	}

	public function has($key, $hash = 'request') {
		return isset($this->_hashes[$hash][$key]);
	}

	public function get($key, $default = null, $hash = 'request') {
		return $this->has($key, $hash) ? $this->_hashes[$hash][$key] : $default;
	}

	public function set($key, $value = null, $hash = 'request') {
		$this->_hashes[$hash][$key] = $value;
	}

	public function def($key, $value = null, $hash = 'request') {
		if (!$this->has($key, $hash)) $this->set($key, $value, $hash);
	}

	public function del($key, $hash = 'request') {
		unset($this->_hashes[$hash][$key]);
	}

	public function toArray($hash = 'request') {
		return isset($this->_hashes[$hash]) ? $this->_hashes[$hash] : null;
	}
}
