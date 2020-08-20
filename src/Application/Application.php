<?php
/**
 * @package plutonium\application
 */

namespace Plutonium\Application;

use Plutonium\Executable;
use Plutonium\Collection\MutableObject;

use Plutonium\Event\Broadcaster;

use Plutonium\Http\Session;
use Plutonium\Http\Request;
use Plutonium\Http\Response;

use Plutonium\Globalization\Locale;

/**
 * @property-read object $config MutableObject containing configuration settings
 * @property-read object $theme The active Theme object
 * @property-read object $module The active Module object
 * @property-read array $widgets An array of active Widget objects
 * @property-read object $session The active Session object
 * @property-read object $request The active Request object
 * @property-read object $response The active Response object
 * @property-read object $document The active Document object
 * @property-read object $locale The active Locale object
 */
class Application implements Executable {
	/**
	 * @ignore internal variable
	 */
	protected static $_path = null;

	/**
	 * Returns absolute path to directory where module definitions are kept.
	 *
	 * @return string Absolute path to modules directory
	 */
	public static function getPath() {
		if (is_null(self::$_path) && defined('PU_PATH_BASE'))
			self::$_path = realpath(PU_PATH_BASE . '/application');

		return self::$_path;
	}

	/**
	 * @ignore internal variable
	 */
	protected $_config = null;

	/**
	 * @ignore internal variable
	 */
	protected $_broadcaster = null;

	/**
	 * @ignore internal variable
	 */
	protected $_theme   = null;

	/**
	 * @ignore internal variable
	 */
	protected $_module  = null;

	/**
	 * @ignore internal variable
	 */
	protected $_widgets = array();

	/**
	 * @ignore internal variable
	 */
	protected $_session = null;

	/**
	 * @ignore internal variable
	 */
	protected $_request = null;

	/**
	 * @ignore internal variable
	 */
	protected $_response = null;

	/**
	 * @ignore internal variable
	 */
	protected $_document = null;

	/**
	 * @ignore internal variable
	 */
	protected $_locale = null;

	/**
	 * @param object $config MutableObject containing configuration settings
	 */
	public function __construct($config) {
		$this->_config = $config;

		$this->_broadcaster = new Broadcaster();
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'config':
				return $this->_config;
			case 'theme':
				return $this->_getTheme($this->_config);
			case 'module':
				return $this->_getModule($this->request);
			case 'widgets':
				return $this->_widgets;
			case 'session':
				return $this->_getSession();
			case 'request':
				return $this->_getRequest($this->_config);
			case 'response':
				return $this->_getResponse();
			case 'document':
				return $this->_getDocument($this->_config, $this->request);
			case 'locale':
				return $this->_getLocale($this->_config);
		}
	}

	/**
	 * @ignore internal method
	 */
	protected function _getTheme($config) {
		if (is_null($this->_theme) && !is_null($config))
			$this->_theme = Theme::newInstance($this, $config->theme);

		return $this->_theme;
	}

	/**
	 * @ignore internal method
	 */
	protected function _getModule($request) {
		if (is_null($this->_module) && !is_null($request))
			$this->_module = Module::newInstance($this, $request->module);

		return $this->_module;
	}

	/**
	 * @ignore internal method
	 */
	protected function _getSession() {
		if (is_null($this->_session))
			$this->_session = new Session();

		return $this->_session;
	}

	/**
	 * @ignore internal method
	 */
	protected function _getRequest($config) {
		if (is_null($this->_request) && !is_null($config))
			$this->_request = new Request($config->system);

		return $this->_request;
	}

	/**
	 * @ignore internal method
	 */
	protected function _getResponse() {
		if (is_null($this->_response))
			$this->_response = new Response();

		return $this->_response;
	}

	/**
	 * @ignore internal method
	 */
	protected function _getDocument($config, $request) {
		if (is_null($this->_document) && !is_null($config)) {
			$format = !is_null($request) ? $request->get('format', 'html') : 'html';

			$args = new MutableObject(array(
				'application' => $this,
				'locale'      => $config->locale,
				'timezone'    => $config->timezone
			));

			$this->_document = Document::newInstance($format, $args);
		}

		return $this->_document;
	}

	/**
	 * @ignore internal method
	 */
	protected function _getLocale($config) {
		if (is_null($this->_locale) && !is_null($config))
			$this->_locale = new Locale($config->locale);

		return $this->_locale;
	}

	/**
	 * Initializes application and its components.
	 */
	public function initialize() {
		$this->module->initialize();
		$this->broadcastEvent('app_init', $this);
	}

	/**
	 * Executes application and its components. Output is streamed to STDOUT.
	 */
	public function execute() {
		$this->module->execute();
		$this->broadcastEvent('app_exec', $this);

		$this->response->setModuleOutput($this->module->render());

		foreach ($this->widgets as $location => $widgets) {
			foreach ($widgets as $position => $widget) {
				$this->response->setWidgetOutput($location, $widget->render());
			}
		}

		$this->response->setThemeOutput($this->theme->render());

		echo $this->document->render();
	}

	/**
	 * Adds the named widget to the named theme location.
	 *
	 * @param string $location Name of the theme location
	 * @param string $name Name of the widget
	 */
	public function addWidget($location, $name) {
		$this->_widgets[$location][] = Widget::newInstance($this, $name);
	}

	/**
	 * Adds an application-wide event listener.
	 *
	 * @param object $listener An instance of Plutonium\Event\Listener
	 */
	public function addEventListener($listener) {
		$this->_broadcaster->addListener($listener);
	}

	/**
	 * Broadcasts an application-wide event.
	 *
	 * @param string $event Event name
	 * @param mixed $data OPTIONAL event data
	 */
	public function broadcastEvent($event, $data = null) {
		$this->_broadcaster->broadcast($event, $data);
	}
}
