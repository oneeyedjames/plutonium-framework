<?php
/**
 * @package plutonium\application
 */

namespace Plutonium\Application;

use Plutonium\AccessObject;
use Plutonium\Loader;

use Plutonium\Database\Table;

use function Plutonium\Functions\filepath;

/**
 * @property-read string $message_start HTML markup before the message area
 * @property-read string $message_close HTML markup after the message area
 * @property-read string $module_start HTML markup before the module
 * @property-read string $module_close HTML markup after the module
 * @property-read string $widget_start HTML markup before each widget
 * @property-read string $widget_close HTML markup after each widget
 * @property-read string $widget_delim HTML markup between each widget
 * @property-read string $layout Current layout name
 * @property-read string $format Current format name
 * @property-read string $output Rendered theme output
 * @property-read object $application The active Application object
 * @property-read string $name The component name
 */
class Theme extends ApplicationComponent {
	/**
	 * @ignore internal variable
	 */
	protected static $_locator = null;

	/**
	 * Returns an ApplicationComponentLocator for themes.
	 * @return object ApplicationComponentLocator
	 */
	public static function getLocator() {
		if (is_null(self::$_locator))
			self::$_locator = new ApplicationComponentLocator('themes');

		return self::$_locator;
	}

	/**
	 * Returns metadata about the named theme.
	 * @param string $name Component name
	 * @return object AccessObject of metadata
	 */
	public static function getMetadata($name) {
		$file = self::getLocator()->getFile($name, 'theme.php');

		$name = strtolower($name);
		$type = ucfirst($name) . 'Theme';
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
	 * Returns a new Theme object. Base class is used if no matching custom
	 * class can be found.
	 * @param object $application The active Application object
	 * @param string $name Component name
	 * @return object Theme object
	 */
	public static function newInstance($application, $name) {
		$file = self::getLocator()->getFile($name, 'theme.php');
		$phar = self::getLocator()->getFile($name, 'theme.php', true);

		$name = strtolower($name);
		$type = ucfirst($name) . 'Theme';
		$args = new AccessObject(array(
			'application' => $application,
			'name' => $name
		));

		return Loader::getClass([$phar, $file], $type, __CLASS__, $args);
	}

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
	 * Child classes may override this value to change the markup before the
	 * message area.
	 * @var string HTML markup
	 */
	protected $_message_start = '<div class="pu-message">';

	/**
	 * Child classes may override this value to change the markup after the
	 * message area.
	 * @var string HTML markup
	 */
	protected $_message_close = '</div>';

	/**
	 * Child classes may override this value to change the markup before the
	 * module.
	 * @var string HTML markup
	 */
	protected $_module_start = '<div class="pu-module">';

	/**
	 * Child classes may override this value to change the markup after the
	 * module.
	 * @var string HTML markup
	 */
	protected $_module_close = '</div>';

	/**
	 * Child classes may override this value to change the markup before each
	 * widget.
	 * @var string HTML markup
	 */
	protected $_widget_start = '<div class="pu-widget">';

	/**
	 * Child classes may override this value to change the markup after each
	 * widget.
	 * @var string HTML markup
	 */
	protected $_widget_close = '</div>';

	/**
	 * Child classes may override this value to change the markup between each
	 * widget.
	 * @var string HTML markup
	 */
	protected $_widget_delim = LS;

	/**
	 * Expected args
	 *   - name: component name
	 *   - application: active Application object
	 * @param object $args AccessObject
	 */
	public function __construct($args) {
		parent::__construct('theme', $args);

		$this->_layout = 'default';
		$this->_format = 'html';
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'message_start':
			case 'message_close':
			case 'module_start':
			case 'module_close':
			case 'widget_start':
			case 'widget_close':
			case 'widget_delim':
			case 'layout':
			case 'format':
				return $this->{"_$key"};
			case 'output':
				return $this->render();
			default:
				return parent::__get($key);
		}
	}

	/**
	 * Creates database record for theme.
	 */
	public function install() {
		$table = Table::getInstance('themes');

		$themes = $table->find(['slug' => $this->name]);

		if (empty($themes)) {
			$meta = new AccessObject(self::getMetadata($this->name));
			$meta->def('package', ucfirst($this->name) . ' Theme');

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
	 * Renders the theme and returns output.
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

			$this->application->broadcastEvent('theme_render', $this);
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

		$layout = strtolower($request->get('layout', $this->layout));
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
	 * Returns whether any widgets are loaded in the named location.
	 * @param string $location Location name
	 * @return boolean Whether widgets are loaded
	 */
	public function hasWidgets($location) {
		return $this->countWidgets($location) > 0;
	}

	/**
	 * Returns the number of widgets loaded in the named location.
	 * @param string $location Location name
	 * @return integer Number of widgets
	 */
	public function countWidgets($location) {
		return $this->application->response->getWidgetCount($location);
	}
}
