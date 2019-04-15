<?php

namespace Plutonium\Application;

use Plutonium\AccessObject;
use Plutonium\Loader;

use Plutonium\Database\Table;

class Widget extends ApplicationComponent {
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
		$phar = self::getPath() . DS . $name . '.phar';
		$file = self::getPath() . DS . $name . DS . 'widget.php';
		$type = ucfirst($name) . 'Widget';
		$args = new AccessObject(array(
			'application' => $application,
			'name' => $name
		));

		return Loader::getClass([$phar, $file], $type, __CLASS__, $args);
	}

	protected $_vars = null;

	protected $_layout = null;
	protected $_format = null;
	protected $_output = null;

	public function __construct($args) {
		parent::__construct('widget', $args);

		$this->_vars   = array();
		$this->_layout = 'default';
		$this->_format = 'html';
	}

	public function __get($key) {
		switch ($key) {
			case 'application':
			case 'name':
				return parent::__get($key);
			case 'layout':
			case 'format':
				return $this->{"_$key"};
			case 'output':
				return $this->render();
			default:
				return $this->getVar($key);
		}
	}

	public function __set($key, $value) {
		$this->setVal($key, $value);
	}

	public function install() {
		$table = Table::getInstance('widgets');

		$widgets = $table->find(['slug' => $this->name]);

		if (empty($widgets)) {
			$meta = new AccessObject(self::getMetadata($this->name));
			$meta->def('package', ucfirst($this->name) . ' Module');

			$table->make(array(
				'name'    => $meta['package'],
				'slug'    => $this->name,
				'descrip' => $meta['description']
			))->save();
		}
	}

	public function uninstall() {
		// TODO method stub
	}

	public function render() {
		if (is_null($this->_output)) {
			$request = $this->application->request;

			$name   = strtolower($this->name);
			$layout = strtolower($this->layout);
			$format = strtolower($request->get('format', $this->format));

			$path = 'layouts' . DS . $layout . '.' . $format . '.php';
			$file = self::getPath() . DS . $name . DS . $path;
			$phar = self::getPath() . DS . $name . '.phar';

			if (is_file($phar)) $file = 'phar://' . $phar . DS . $path;

			if (is_file($file)) {
				ob_start();

				include $file;

				$this->_output = ob_get_contents();

				ob_end_clean();
			} else {
				$message = sprintf("Resource does not exist: %s.", $file);
				trigger_error($message, E_USER_ERROR);
			}

			$this->application->broadcastEvent('widget_render', $this);
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
