<?php
/**
 * @package plutonium\application
 */

namespace Plutonium\Application;

use Plutonium\AccessObject;
use Plutonium\Loader;

use Plutonium\Database\Table;

use function Plutonium\Functions\filepath;

class Theme extends ApplicationComponent {
	protected static $_locator = null;

	public static function getLocator() {
		if (is_null(self::$_locator))
			self::$_locator = new ApplicationComponentLocator('themes');

		return self::$_locator;
	}

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

	protected $_layout = null;
	protected $_format = null;
	protected $_output = null;

	protected $_message_start = '<div class="pu-message">';
	protected $_message_close = '</div>';

	protected $_module_start = '<div class="pu-module">';
	protected $_module_close = '</div>';

	protected $_widget_start = '<div class="pu-widget">';
	protected $_widget_close = '</div>';
	protected $_widget_delim = LS;

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

	public function install() {
		$table = Table::getInstance('themes');

		$themes = $table->find(['slug' => $this->name]);

		if (empty($themes)) {
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

	public function localize($text) {
		return $this->application->locale->localize($text);
	}

	public function hasWidgets($location) {
		return $this->countWidgets($location) > 0;
	}

	public function countWidgets($location) {
		return $this->application->response->getWidgetCount($location);
	}
}
