<?php
/**
 * @package plutonium\application
 */

namespace Plutonium\Application;

use Plutonium\Executable;
use Plutonium\Renderable;
use Plutonium\Collection\MutableObject;
use Plutonium\Loader;

use Plutonium\Database\Table;

/**
 * @property-read string $path File path for this component
 * @property-read string $resource The active resource name
 * @property-read object $request The active Request object
 * @property-read object $application The active Application object
 * @property-read string $name The component name
 */
class Module extends ApplicationComponent implements Executable {
	/**
	 * @ignore internal variable
	 */
	protected static $_path = null;

	/**
	 * @ignore internal variable
	 */
	protected static $_default_resource = null;

	/**
	 * @ignore internal variable
	 */
	protected static $_locator = null;

	/**
	 * Returns an ApplicationComponentLocator for modules.
	 * @return object ApplicationComponentLocator
	 */
	public static function getLocator() {
		if (is_null(self::$_locator))
			self::$_locator = new ApplicationComponentLocator('modules');

		return self::$_locator;
	}

	/**
	 * Returns the file path for modules.
	 * @return string Absolute file path
	 */
	public static function getPath() {
		if (is_null(self::$_path) && defined('PU_PATH_BASE'))
			self::$_path = realpath(PU_PATH_BASE . '/modules');

		return self::$_path;
	}

	/**
	 * Returns metadata about the named module.
	 * @param string $name Component name
	 * @return object MutableObject of metadata
	 */
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

	/**
	 * Returns a new Module object. Base class is used if no matching custom
	 * class can be found.
	 * @param object $application The active Application object
	 * @param string $name Component name
	 * @return object Module object
	 */
	public static function newInstance($application, $name) {
		$name = strtolower($name);
		$phar = self::getPath() . DS . $name . '.phar';
		$file = self::getPath() . DS . $name . DS . 'module.php';
		$type = ucfirst($name) . 'Module';
		$args = new MutableObject(array(
			'application' => $application,
			'name'        => $name
		));

		return Loader::getClass([$phar, $file], $type, __CLASS__, $args);
	}

	/**
	 * @ignore internal variable
	 */
	protected $_resource = null;

	/**
	 * @ignore internal variable
	 */
	protected $_router = null;

	/**
	 * @ignore internal variable
	 */
	protected $_controller = null;

	/**
	 * @ignore internal variable
	 */
	protected $_models = array();

	/**
	 * @ignore internal variable
	 */
	protected $_view = null;

	/**
	 * Expected args
	 *   - name: component name
	 *   - application: active Application object
	 * @param object $args MutableObject
	 */
	public function __construct($args) {
		parent::__construct('module', $args);
	}

	/**
	 * @ignore magic method
	 */
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

	/**
	 * Creates database record for module and tables for resources.
	 */
	public function install() {
		$table = Table::getInstance('modules');

		$modules = $table->find(['slug' => $this->name]);

		if (empty($modules)) {
			$meta = new MutableObject(self::getMetadata($this->name));
			$meta->def('package', ucfirst($this->name) . ' Module');

			$table->make(array(
				'name'    => $meta['package'],
				'slug'    => $this->name,
				'descrip' => $meta['description']
			))->save();
		}

		$models = [];

		$phar = self::getPath() . DS . $this->name . '.phar';

		if (is_file($phar)) {
			if (($dir = opendir('phar://' . $phar . DS . 'models')) !== false) {
				while (($file = readdir($dir)) !== false) {
					$name = pathinfo($file, PATHINFO_FILENAME);
					$ext = pathinfo($file, PATHINFO_EXTENSION);

					if ($ext == 'xml') $models[] = $name;
				}

				closedir($dir);
			}
		} else {
			$path = self::getPath() . DS . $this->name
				  . DS . 'models' . DS . '*.xml';

			foreach (glob($path) as $file)
				$models[] = basename($file, '.xml');
		}

		foreach ($models as $name) {
			$table = $this->getModel($name)->getTable();
			$table->create();
		}
	}

	/**
	 * Does not do anything yet.
	 */
	public function uninstall() {
		// TODO method stub
	}

	/**
	 * Performs any initialization prior to execution.
	 */
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

		$vars = $this->getRouter()->match($this->request->uri);

		foreach ($vars as $key => $value)
			$this->request->def($key, $value);

		$this->request->def('resource', self::$_default_resource);

		$this->_resource = $this->request->resource;

		$this->getController()->initialize();
		$this->application->broadcastEvent('mod_init', $this);
	}

	/**
	 * Executes the action named in the HTTP request and sends HTTP redirect
	 * header if redirect URL is set.
	 */
	public function execute() {
		$this->getController()->execute();
		$this->application->broadcastEvent('mod_exec', $this);
	}

	/**
	 * Renders the module and returns output.
	 * @return string Rendered module output
	 */
	public function render() {
		$output = $this->getView()->render();
		$this->application->broadcastEvent('mod_render', $this);
		return $output;
	}

	/**
	 * Translates text according to the acive Locale object.
	 * @param string $text Original text
	 * @return string Translated text
	 */
	public function localize($text) {
		return $this->application->locale->localize($text);
	}

	/**
	 * Returns the active Router object.
	 * @return object Router object
	 */
	public function getRouter() {
		if (is_null($this->_router)) {
			$type = ucfirst($this->name) . 'Router';
			$file = $this->path . DS . 'router.php';
			$phar = $this->path . '.phar';

			$this->_router = Loader::getClass([$phar, $file],
				$type, 'Plutonium\Application\Router', $this);
		}

		return $this->_router;
	}

	/**
	 * Returns the active Controller object.
	 * @return object Controller object
	 */
	public function getController() {
		if (is_null($this->_controller)) {
			$name = strtolower($this->_resource);
			$type = ucfirst($name) . 'Controller';
			$path = 'controllers' . DS . $name . '.php';
			$file = $this->path . DS . $path;
			$phar = $this->path . '.phar';

			$args = new MutableObject(array(
				'module' => $this,
				'name'   => $name
			));

			$this->_controller = Loader::getClass([$phar, $file],
				$type, 'Plutonium\Application\Controller', $args);
		}

		return $this->_controller;
	}

	/**
	 * Returns the named Model object.
	 * @param string $name OPTIONAL Resource name
	 * @return object Model object
	 */
	public function getModel($name = null) {
		$name = strtolower(is_null($name) ? $this->_resource : $name);

		if (empty($this->_models[$name])) {
			$type = ucfirst($name) . 'Model';
			$path = 'models' . DS . $name . '.php';
			$file = $this->path . DS . $path;
			$phar = $this->path . '.phar';

			$args = new MutableObject(array(
				'module' => $this,
				'name'   => $name
			));

			$this->_models[$name] = Loader::getClass([$phar, $file],
				$type, 'Plutonium\Application\Model', $args);
		}

		return $this->_models[$name];
	}

	/**
	 * Returns the active View object.
	 * @return object View object
	 */
	public function getView() {
		if (is_null($this->_view)) {
			$name = strtolower($this->_resource);
			$type = ucfirst($name) . 'View';
			$path = 'views' . DS . $name . DS . 'view.php';
			$file = $this->path . DS . $path;
			$phar = $this->path . '.phar';

			$args = new MutableObject(array(
				'module' => $this,
				'name'   => $name
			));

			$this->_view = Loader::getClass([$phar, $file],
				$type, 'Plutonium\Application\View', $args);
		}

		return $this->_view;
	}

	/**
	 * Returns a URL for the current request.
	 * @return string URL string
	 */
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
