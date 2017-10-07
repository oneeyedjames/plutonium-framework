<?php

namespace Plutonium\Application;

use Plutonium\Component;
use Plutonium\Visible;
use Plutonium\Object;
use Plutonium\Loader;

class Widget extends Component implements Visible {
	protected static $_path = null;

	public static function getPath() {
		if (is_null(self::$_path) && defined('PU_PATH_BASE'))
			self::$_path = realpath(PU_PATH_BASE . '/widgets');

		return self::$_path;
	}

	public static function setPath($path) {
		self::$_path = $path;
	}

	public static function getMetadata($name) {
		$name = strtolower($name);
		$file = self::getPath() . DS . $name . DS . 'widget.php';
		$type = ucfirst($name) . 'Widget';
		$meta = array();

		if (is_file($file)) {
			require_once $file;

			$ref = new \ReflectionClass($type);

		    $header = $ref->getDocComment();
		    $header = trim(preg_replace('/(^\/\*\*|\*\/)/ms', '', trim($header)));

		    $lines = preg_split('/\n|\r\n?/', $header);

		    array_walk($lines, function(&$value, $key) {
		        $value = preg_replace('/^\s*\*\s/', '', $value);
		    });

		    foreach ($lines as $line) {
		        if ('@' == $line[0]) {
		            list($key, $value) = explode(' ', substr($line, 1), 2);
		            $meta[$key] = trim($value);
		        } else {
		            $meta['description'][] = trim($line);
		        }
		    }

		    $meta['description'] = implode(PHP_EOL, $meta['description']);
		}

		return $meta;
	}

	public static function newInstance($application, $name) {
		$name = strtolower($name);
		$file = self::getPath() . DS . $name . DS . 'widget.php';
		$type = ucfirst($name) . 'Widget';
		$args = new Object(array(
			'application' => $application,
			'name' => $name
		));

		return Loader::getClass($file, $type, __CLASS__, $args);
	}

	protected $_vars = null;

	protected $_layout = null;
	protected $_format = null;
	protected $_params = null;
	protected $_output = null;

	public function __construct($args) {
		parent::__construct('widget', $args);

		$this->_vars   = array();
		$this->_layout = 'default';
		$this->_format = 'html';
		$this->_params = $args->params instanceof Object ? $args->params
					   : new Object($args->params);
	}

	public function __get($key) {
		switch ($key) {
			case 'application':
			case 'name':
				return parent::__get($key);
			default:
				return $this->getVar($key);
		}
	}

	public function __set($key, $value) {
		$this->setVal($key, $value);
	}

	public function install() {
		// TODO method stub
	}

	public function display() {
		$request = $this->application->request;

		$name   = strtolower($this->name);
		$layout = strtolower($this->_layout);
		$format = strtolower($request->get('format', $this->_format));

		$file = self::getPath() . DS . $name . DS
		      . 'layouts' . DS . $layout . '.' . $format . '.php';

		if (is_file($file)) {
			ob_start();

			include $file;

			$this->_output = ob_get_contents();

			ob_end_clean();
		} else {
			$message = sprintf("Resource does not exist: %s.", $file);
			trigger_error($message, E_USER_ERROR);
		}

		return $this->_output;
	}

	public function getVar($key) {
		return $this->_vars[$key];
	}

	public function setVal($key, $var) {
		$this->_vars[$key] = $var;
	}

	public function setRef($key, &$var) {
		$this->_vars[$key] = $var;
	}
}
