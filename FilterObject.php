<?php

namespace Plutonium;

use Plutonium\Filter\TypeFilter;
use Plutonium\Filter\StringFilter;

class FilterObject extends Object {
	protected $_filters = array();

	public function __construct($data = null) {
		parent::__construct($data);

		$this->_filters[] = new TypeFilter();
		$this->_filters[] = new StringFilter();
	}

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

	public function getBool($key, $default = null) {
		return $this->get($key, $default, 'bool');
	}

	public function getInt($key, $default = null) {
		return $this->get($key, $default, 'int');
	}

	public function getFloat($key, $default = null) {
		return $this->get($key, $default, 'float');
	}

	public function getString($key, $default = null) {
		return $this->get($key, $default, 'string');
	}

	public function getArray($key, $default = null) {
		return $this->get($key, $default, 'array');
	}

	public function getObject($key, $default = null) {
		return $this->get($key, $default, 'object');
	}

	public function getAlpha($key, $default = null) {
		return $this->get($key, $default, 'alpha');
	}

	public function getAlnum($key, $default = null) {
		return $this->get($key, $default, 'alnum');
	}

	public function getDigit($key, $default = null) {
		return $this->get($key, $default, 'digit');
	}

	public function getHexit($key, $default = null) {
		return $this->get($key, $default, 'hexit');
	}

	public function getLower($key, $default = null) {
		return $this->get($key, $default, 'lcase');
	}

	public function getUpper($key, $default = null) {
		return $this->get($key, $default, 'ucase');
	}
}
