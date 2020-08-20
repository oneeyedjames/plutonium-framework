<?php
/**
 * @package plutonium\application
 */

namespace Plutonium\Application;

use Plutonium\AccessObject;
use Plutonium\Loader;

use Plutonium\Database\Table;

/**
 * @property-read string $layout Current layout name
 * @property-read string $format Current format name
 * @property-read string $output Rendered widget output
 * @property-read object $application The active Application object
 * @property-read string $name The component name
 */
class Widget extends ApplicationComponent {
	/**
	 * @ignore internal variable
	 */
	protected static $_locator = null;

	/**
	 * Returns an ApplicationComponentLocator for widgets.
	 * @return object ApplicationComponentLocator
	 */
	public static function getLocator() {
		if (is_null(self::$_locator))
			self::$_locator = new ApplicationComponentLocator('widgets');

		return self::$_locator;
	}

	/**
	 * Returns metadata about the named widget.
	 * @param string $name Component name
	 * @return object AccessObject of metadata
	 */
	public static function getMetadata($name) {
		$file = self::getLocator()->getFile($name, 'widget.php');

		$name = strtolower($name);
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

	/**
	 * Returns a new Widget object. Base class is used if no matching custom
	 * class can be found.
	 * @param object $application The active Application object
	 * @param string $name Component name
	 * @return object Widget object
	 */
	public static function newInstance($application, $name) {
		$file = self::getLocator()->getFile($name, 'widget.php');
		$phar = self::getLocator()->getFile($name, 'widget.php', true);

		$name = strtolower($name);
		$type = ucfirst($name) . 'Widget';
		$args = new AccessObject(array(
			'application' => $application,
			'name' => $name
		));

		return Loader::getClass([$phar, $file], $type, __CLASS__, $args);
	}

	/**
	 * @ignore internal variable
	 */
	protected $_vars = null;

	/**
	 * @ignore internal variable
	 */
	protected $_layout = null;

	/**
	 * @ignore internal variable
	 */
	protected $_format = null;

	/**
	 * @ignore internal variable
	 */
	protected $_output = null;

	/**
	 * Expected args
	 *   - name: component name
	 *   - application: active Application object
	 * @param object $args AccessObject
	 */
	public function __construct($args) {
		parent::__construct('widget', $args);

		$this->_vars   = array();
		$this->_layout = 'default';
		$this->_format = 'html';
	}

	/**
	 * @ignore magic method
	 */
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

	/**
	 * @ignore magic method
	 */
	public function __set($key, $value) {
		$this->setVal($key, $value);
	}

	/**
	 * Creates database record for widget.
	 */
	public function install() {
		$table = Table::getInstance('widgets');

		$widgets = $table->find(['slug' => $this->name]);

		if (empty($widgets)) {
			$meta = new AccessObject(self::getMetadata($this->name));
			$meta->def('package', ucfirst($this->name) . ' Widget');

			$table->make(array(
				'name'    => $meta['package'],
				'slug'    => $this->name,
				'descrip' => $meta['description']
			))->save();
		}
	}

	/**
	 * Does not do anything yet.
	 */
	public function uninstall() {
		// TODO method stub
	}

	/**
	 * Renders the widget and returns output.
	 * @return string Rendered widget output
	 */
	public function render() {
		if (is_null($this->_output)) {
			if ($file = $this->getLayout()) {
				if (stripos($file, '.phar') !== false)
					$file = 'phar://' . $file;

				ob_start();

				include $file;

				$this->_output = ob_get_contents();

				ob_end_clean();
			} else {
				$message = sprintf("Layout file not found");
				trigger_error($message, E_USER_ERROR);
			}

			$this->application->broadcastEvent('widget_render', $this);
		}

		return $this->_output;
	}

	/**
	 * Returns file path for layout template matching the given request.
	 * @param object Request object
	 * @return string Absolute file path
	 */
	public function getLayout($request = null) {
		if (is_null($request))
			$request = $this->application->request;

		$layout = strtolower($this->layout);
		$format = strtolower($request->get('format', $this->format));

		$files = [
			'layouts/' . $layout . '.' . $format . '.php',
			'layouts/default.' . $format . '.php'
		];

		return self::getLocator()->locateFile($this->name, $files);
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
	 * Retrieves a named key-value pair. Default value is returned if key is not
	 * set.
	 * @param string $key Unique key
	 * @param mixed $default OPTIONAL Default value for key
	 * @return mixed Value for key
	 */
	public function getVar($key) {
		return $this->_vars[$key];
	}

	/**
	 * Creates or updates a key-value pair by value.
	 * @param string $key Unique key
	 * @param mixed $var value for key
	 */
	public function setVal($key, $var) {
		$this->_vars[$key] = $var;
	}

	/**
	 * Creates or updates a key-value pair by reference.
	 * @param string $key Unique key
	 * @param mixed $var value for key
	 */
	public function setRef($key, &$var) {
		$this->_vars[$key] = $var;
	}
}
