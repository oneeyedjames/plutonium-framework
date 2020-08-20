<?php
/**
 * @package plutonium\globalization
 */

namespace Plutonium\Globalization;

use Plutonium\Collection\AccessibleObject;

use function Plutonium\Functions\filepath;

/**
 * @property-read string $name Locale string
 * @property-read string $language Language code
 * @property-read string $country Country code
 */
class Locale {
	/**
	 * @ignore internal variable
	 */
	protected static $_path = null;

	/**
	 * Parses a locale string into its languag and country components.
	 * @param string $localse Locale string
	 * @return object AccessibleObject containing language and country
	 */
	public static function parse($locale) {
		if ($locale instanceof AccessibleObject)
			return $locale;

		if (is_string($locale)) {
			if (strpos($locale, '-') !== false) {
				list($language, $country) = explode('-', $locale, 2);
				$locale = array('language' => $language, 'country' => $country);
			} else {
				$locale = array('language' => $locale);
			}
		}

		return is_array($locale) ? new AccessibleObject($locale) : null;
	}

	/**
	 * @ignore internal variable
	 */
	protected $_language;

	/**
	 * @ignore internal variable
	 */
	protected $_country;

	/**
	 * @ignore internal variable
	 */
	protected $_phrases;

	/**
	 * @param mixed $config Locale name or AccessibleObject containing language
	 * and country
	 */
	public function __construct($config) {
		$config = self::parse($config);

		if ($config instanceof AccessibleObject) {
			$this->_language = strtolower($config->language);
			$this->_country  = strtoupper($config->country);
		}

		$this->_phrases = [];

		$this->load(null, 'application');
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'name':
				$name = $this->_language;

				if (!empty($this->_country))
					$name .= '-' . $this->_country;

				return $name;
			case 'language':
				return $this->_language;
			case 'country':
				return $this->_country;
		}
	}

	/**
	 * Returns a translated string for the given original text. Original text is
	 * returned if no translation is available.
	 * @param string $key Original text
	 * @return string Translated text
	 */
	public function localize($key) {
		if (!isset($this->_phrases[strtoupper($key)])) return $key;

		$match = $this->_phrases[strtoupper($key)];

		if (func_num_args() == 1) return $match;

		$args = func_get_args();
		$args[0] = $match;

		return call_user_func_array('sprintf', $args);
	}

	/**
	 * Loads translation resources from specified component.
	 * @param string $name Component name
	 * @param string $type Component type
	 */
	public function load($name, $type) {
		$name = strtolower($name);
		$type = strtolower($type);

		switch ($type) {
			case 'application':
				$path = PU_PATH_BASE . DS . 'application' . DS . 'locales';
				$this->_loadPath($path);
				break;
			case 'themes':
			case 'modules':
			case 'widgets':
				$path = PU_PATH_BASE . DS . $type . DS . $name . DS . 'locales';
				$this->_loadPath($path);
				break;
			default:
				$error = sprintf("Invalid locale resource type: %s", $type);
				trigger_error($error, E_USER_NOTICE);
				break;
		}
	}

	/**
	 * @ignore internal method
	 */
	protected function _loadPath($path) {
		$file = filepath($path) . DS . $this->language . '.xml';

		if (!$this->_loadFile($file)) {
			$message = sprintf("Could not find language resource: %s.", $this->language);
			trigger_error($message, E_USER_WARNING);
		}

		if (!empty($this->country)) {
			$locale = $this->language . '-' . $this->country;
			$file = filepath($path) . DS . $name . '.xml';

			if (!$this->_loadFile($file)) {
				$message = sprintf("Could not find language resource: %s.", $locale);
				trigger_error($message, E_USER_NOTICE);
			}
		}
	}

	/**
	 * @ignore internal method
	 */
	protected function _loadFile($file) {
		if (is_file($file)) {
			$xml = simplexml_load_file($file);

			foreach ($xml->phrase as $phrase) {
				$attributes = $phrase->attributes();

				$key   = strtoupper($attributes['key']);
				$value = (string) $attributes['value'];

				$this->_phrases[$key] = $value;
			}

			return true;
		}

		return false;
	}
}
