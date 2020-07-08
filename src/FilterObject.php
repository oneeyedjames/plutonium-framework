<?php
/**
 * @package plutonium
 */

namespace Plutonium;

use Plutonium\Filter\TypeFilter;
use Plutonium\Filter\StringFilter;

/**
 * Extension of AccessObject with type-casting convenience methods.
 */
class FilterObject extends AccessObject {
	/**
	 * @ignore internal variable
	 */
	protected $_filters = array();

	/**
	 * @param mixed Array or AccessObject of key-value pairs
	 */
	public function __construct($data = null) {
		parent::__construct($data);

		$this->_filters[] = new TypeFilter();
		$this->_filters[] = new StringFilter();
	}

	/**
	 * Retrieves the filtered value associated with the specified key. Default
	 * value is filtered if the key is not set. Original value is returned if a
	 * matching filter cannot be found.
	 *
	 * @param string $key Unique key
	 * @param mixed $default OPTIONAL Default value to filter
	 * @param string $type OPTIONAL name of filter to apply
	 * @return mixed Filtered value associated with key
	 */
	public function get($key, $default = null, $type = null) {
		$value = parent::get($key, $default);

		if (is_string($type)) {
			foreach ($this->_filters as $filter) {
				if ($filter->canHandle($type))
					return $filter->filter($value, $type);
			}
		}

		return $value;
	}

	/**
	 * Retrieves a boolean value associated with the specified key. Default
	 * value is returned if the key is not set.
	 *
	 * @param string $key Unique key
	 * @param boolean $default OPTIONAL Default value to filter
	 * @return boolean Filtered value associated with key
	 */
	public function getBool($key, $default = null) {
		return $this->get($key, $default, 'bool');
	}

	/**
	 * Retrieves an integer value associated with the specified key. Default
	 * value is returned if the key is not set.
	 *
	 * @param string $key Unique key
	 * @param int $default OPTIONAL Default value to filter
	 * @return int Filtered value associated with key
	 */
	public function getInt($key, $default = null) {
		return $this->get($key, $default, 'int');
	}

	/**
	 * Retrieves a float value associated with the specified key. Default
	 * value is returned if the key is not set.
	 *
	 * @param string $key Unique key
	 * @param float $default OPTIONAL Default value to filter
	 * @return float Filtered value associated with key
	 */
	public function getFloat($key, $default = null) {
		return $this->get($key, $default, 'float');
	}

	/**
	 * Retrieves a string value associated with the specified key. Default
	 * value is returned if the key is not set.
	 *
	 * @param string $key Unique key
	 * @param string $default OPTIONAL Default value to filter
	 * @return string Filtered value associated with key
	 */
	public function getString($key, $default = null) {
		return $this->get($key, $default, 'string');
	}

	/**
	 * Retrieves an array value associated with the specified key. Default
	 * value is returned if the key is not set.
	 *
	 * @param string $key Unique key
	 * @param array $default OPTIONAL Default value to filter
	 * @return array Filtered value associated with key
	 */
	public function getArray($key, $default = null) {
		return $this->get($key, $default, 'array');
	}

	/**
	 * Retrieves an object value associated with the specified key. Default
	 * value is returned if the key is not set.
	 *
	 * @param string $key Unique key
	 * @param object $default OPTIONAL Default value to filter
	 * @return object Filtered value associated with key
	 */
	public function getObject($key, $default = null) {
		return $this->get($key, $default, 'object');
	}

	/**
	 * Retrieves a string value associated with the specified key. Default
	 * value is returned if the key is not set. Returned value will only contain
	 * alpha characters.
	 *
	 * @param string $key Unique key
	 * @param string $default OPTIONAL Default value to filter
	 * @return string Filtered value associated with key
	 */
	public function getAlpha($key, $default = null) {
		return $this->get($key, $default, 'alpha');
	}

	/**
	 * Retrieves a string value associated with the specified key. Default
	 * value is returned if the key is not set. Returned value will only contain
	 * alpha-numeric characters.
	 *
	 * @param string $key Unique key
	 * @param string $default OPTIONAL Default value to filter
	 * @return string Filtered value associated with key
	 */
	public function getAlnum($key, $default = null) {
		return $this->get($key, $default, 'alnum');
	}

	/**
	 * Retrieves a string value associated with the specified key. Default
	 * value is returned if the key is not set. Returned value will only contain
	 * decimal numerals.
	 *
	 * @param string $key Unique key
	 * @param string $default OPTIONAL Default value to filter
	 * @return string Filtered value associated with key
	 */
	public function getDigit($key, $default = null) {
		return $this->get($key, $default, 'digit');
	}

	/**
	 * Retrieves a string value associated with the specified key. Default
	 * value is returned if the key is not set. Returned value will only contain
	 * hexadecimal numerals.
	 *
	 * @param string $key Unique key
	 * @param string $default OPTIONAL Default value to filter
	 * @return string Filtered value associated with key
	 */
	public function getHexit($key, $default = null) {
		return $this->get($key, $default, 'hexit');
	}

	/**
	 * Retrieves a string value associated with the specified key. Default
	 * value is returned if the key is not set. Returned value will only contain
	 * lower-case alpha characters.
	 *
	 * @param string $key Unique key
	 * @param string $default OPTIONAL Default value to filter
	 * @return string Filtered value associated with key
	 */
	public function getLower($key, $default = null) {
		return $this->get($key, $default, 'lcase');
	}

	/**
	 * Retrieves a string value associated with the specified key. Default
	 * value is returned if the key is not set. Returned value will only contain
	 * upper-case alpha characters.
	 *
	 * @param string $key Unique key
	 * @param string $default OPTIONAL Default value to filter
	 * @return string Filtered value associated with key
	 */
	public function getUpper($key, $default = null) {
		return $this->get($key, $default, 'ucase');
	}
}
