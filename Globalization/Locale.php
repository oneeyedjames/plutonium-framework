<?php
namespace Plutonium\Globalization;

use Plutonium\Object;

class Locale {
	protected static $_path = null;

	protected $_language;
	protected $_country;
	protected $_phrases;

	public function __construct($config) {
		if (is_string($config))
			$config = $this->_parseString($config);
		elseif (is_array($config))
			$config = new Object($config);

		if ($config instanceof Object) {
			$this->_language = strtolower($config->language);
			$this->_country  = strtoupper($config->country);
		}

		$this->_phrases = array();

		$path = realpath(PU_PATH_BASE . '/locales');
		$file = $path . DS . $this->_language . '.xml';

		if (!$this->_loadFile($file)) {
			$message = sprintf("Could not find language resource: %s.", $this->_language);
			trigger_error($message, E_USER_WARNING);
		}

		if (!empty($this->_country)) {
			$file = $path . DS . $this->_language . '-' . $this->_country . '.xml';

			if (!$this->_loadFile($file)) {
				$message = sprintf("Could not find language resource: %s-%s.", $this->_language, $this->_country);
				trigger_error($message, E_USER_NOTICE);
			}
		}
	}

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

	public function load($name, $type) {
		$name = strtolower($name);
		$type = strtolower($type);

		switch ($type) {
			case 'themes':
			case 'modules':
			case 'widgets':
				$path = realpath(PU_PATH_BASE) . DS . $type . DS . $name . DS . 'locales';
				$file = $path . DS . $this->language . '.xml';

				if (!$this->_loadFile($file)) {
					$message = sprintf("Resource does not exist: %s", $file);
					trigger_error($message, E_USER_WARNING);
				}

				if (!empty($this->country)) {
					$path = realpath(PU_PATH_BASE . DS . $type . DS . $name);
					$file = $path . DS . 'locales' . DS . $this->name . '.xml';

					if (!$this->_loadFile($file)) {
						$message = sprintf("Resource does not exist: %s", $file);
						trigger_error($message, E_USER_WARNING);
					}
				}
				break;
			default:
				$error = sprintf("Invalid locale resource type: %s", $type);
				trigger_error($error, E_USER_WARNING);
				break;
		}
	}

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

	protected function _parseString($locale) {
		if (strpos($locale, '-') !== false) {
			list($language, $country) = explode('-', $locale, 2);
			$locale = array('language' => $language, 'country' => $country);
		} else {
			$locale = array('language' => $locale);
		}

		return new Object($locale);
	}

	public function localize($key) {
		if (!isset($this->_phrases[strtoupper($key)])) return $key;

		$match = $this->_phrases[strtoupper($key)];

		if (func_num_args() == 1) return $match;

		$args = func_get_args();
		$args[0] = $match;

		return call_user_func_array('sprintf', $args);
	}
}
