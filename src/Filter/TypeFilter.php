<?php
/**
 * @package plutonium\filter
 */

namespace Plutonium\Filter;

class TypeFilter extends AbstractFilter {
	public function boolFilter($value) {
		return is_scalar($value) ? (bool) $value : null;
	}

	public function booleanFilter($value) {
		return $this->boolFilter($value);
	}

	public function intFilter($value) {
		return is_scalar($value) ? (int) $value : null;
	}

	public function integerFilter($value) {
		return $this->intFilter($value);
	}

	public function floatFilter($value) {
		return is_scalar($value) ? (float) $value : null;
	}

	public function doubleFilter($value) {
		return $this->floatFilter($value);
	}

	public function stringFilter($value) {
		return is_scalar($value) ? (string) $value : null;
	}

	public function arrayFilter($value) {
		if (is_array($value))
			return $value;
		elseif (is_object($value))
			return (array) $value;
		else
			return null;
	}

	public function objectFilter($value) {
		return is_array($value) || is_object($value) ? (object) $value : null;
	}
}
