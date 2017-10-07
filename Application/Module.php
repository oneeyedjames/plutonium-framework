<?php

namespace Plutonium\Application;

use Plutonium\Component;
use Plutonium\Executable;
use Plutonium\Visible;
use Plutonium\Object;
use Plutonium\Loader;

use Plutonium\Database\Table;

class Module extends Component implements Executable, Visible {
	protected static $_path = null;

	protected static $_default_resource = null;

	public static function getPath() {
		if (is_null(self::$_path) && defined('PU_PATH_BASE'))
			self::$_path = realpath(PU_PATH_BASE . '/modules');

		return self::$_path;
	}

	public static function getMetadata($name) {
		$name = strtolower($name);
		$file = self::getPath() . DS . $name . DS . 'module.php';
		$type = ucfirst($name) . 'Module';
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
		$type = ucfirst($name) . 'Module';
		$file = self::getPath() . DS . $name . DS . 'module.php';
		$args = new Object(array(
			'application' => $application,
			'name'        => $name
		));

		return Loader::getClass($file, $type, __CLASS__, $args);
	}

	protected $_resource = null;
	protected $_output   = null;

	protected $_router = null;

	protected $_controller = null;
	protected $_models     = array();
	protected $_view       = null;

	public function __construct($args) {
		parent::__construct('module', $args);
	}

	public function __get($key) {
		switch ($key) {
			case 'path':
				return self::getPath() . DS . strtolower($this->name);
			case 'resource':
				return $this->_resource;
			case 'request':
				return $this->application->request;
			default:
				return parent::__get($key);
		}
	}

	public function install() {
		$table = Table::getInstance('modules');

		$modules = $table->find(array(
			'slug' => $this->name
		));

		if (empty($modules)) {
			$table->make(array(
				'name'    => ucfirst($this->name),
				'slug'    => $this->name,
				'descrip' => 'A new module',
				'default' => 0
			))->save();
		}

		$path = self::getPath() . DS . $this->name
			  . DS . 'models' . DS . 'tables' . DS . '*.xml';

		foreach (glob($path) as $file) {
			$table = $this->getModel(basename($file, '.xml'))->getTable();
			$table->create();
		}
	}

	public function initialize() {
		switch ($this->request->method) {
			case 'POST':
				$this->request->def('action', 'create');
				break;
			case 'PUT':
				$this->request->def('action', 'update');
				break;
			case 'DELETE':
				$this->request->def('action', 'delete');
				break;
		}

		$vars = $this->getRouter()->match($this->request->path);

		foreach ($vars as $key => $value)
			$this->request->set($key, $value);

		$this->request->def('resource', self::$_default_resource);

		$this->_resource = $this->request->resource;

		$this->getController()->initialize();
	}

	public function execute() {
		$this->getController()->execute();
	}

	public function display() {
		return $this->_output = $this->getView()->display();
	}

	public function getRouter() {
		if (is_null($this->_router)) {
			$type = ucfirst($this->name) . 'Router';
			$file = $this->path . DS . 'router.php';

			$this->_router = Loader::getClass($file, $type, 'Plutonium\Application\Router', $this);
		}

		return $this->_router;
	}

	public function getController() {
		if (is_null($this->_controller)) {
			$name = strtolower($this->_resource);
			$type = ucfirst($name) . 'Controller';
			$file = $this->path . DS . 'controllers' . DS . $name . '.php';

			$args = new Object(array(
				'module' => $this,
				'name'   => $name
			));

			$this->_controller = Loader::getClass($file, $type, 'Plutonium\Application\Controller', $args);
		}

		return $this->_controller;
	}

	public function getModel($name = null) {
		$name = strtolower(is_null($name) ? $this->_resource : $name);

		if (empty($this->_models[$name])) {
			$type = ucfirst($name) . 'Model';
			$file = $this->path . DS . 'models' . DS . $name . '.php';

			$args = new Object(array(
				'module' => $this,
				'name'   => $name
			));

			$this->_models[$name] = Loader::getClass($file, $type, 'Plutonium\Application\Model', $args);
		}

		return $this->_models[$name];
	}

	public function getView() {
		if (is_null($this->_view)) {
			$name = strtolower($this->_resource);
			$type = ucfirst($name) . 'View';
			$file = $this->path . DS . 'views' . DS . $name . DS . 'view.php';

			$args = new Object(array(
				'module' => $this,
				'name'   => $name
			));

			$this->_view = Loader::getClass($file, $type, 'Plutonium\Application\View', $args);
		}

		return $this->_view;
	}

	public function getPermalink() {
		$request = $this->application->request;
		$config  = $this->application->config->system;

		$host = $request->module . '.' . $request->host . '.' . $config->hostname;
		$path = $this->getRouter()->build($request);

		if (!empty($path))
			$path .= '.' . $request->get('format', 'html');

		$link = $config->scheme . '://' . $host . '/' . $path;

		return $link;
	}
}