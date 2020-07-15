<?php
/**
 * @package plutonium\filter
 */

namespace Plutonium\Filter;

/**
 * Reference implementation for common scalar data filter types.
 * Supported types:
 *   - Bool, Boolean
 *   - Int, Integer
 *   - Float, Double
 *   - String
 */
class TypeFilter extends AbstractFilter {
	/**
	 * Returns the boolean equivalent of the given scalar value.
	 * @param scalar $value Any scalar value
	 * @return boolean The boolean equivalent, NULL if value is not scalar
	 */
	public function boolFilter($value) {
		return is_scalar($value) ? (bool) $value : null;
	}

	/**
	 * Alias of boolFilter()
	 * @param scalar $value Any scalar value
	 * @return boolean The boolean equivalent, NULL if value is not scalar
	 */
	public function booleanFilter($value) {
		return $this->boolFilter($value);
	}

	/**
	 * Returns the integer equivalent of the given scalar value.
	 * @param scalar $value Any scalar value
	 * @return integer The integer equivalent, NULL if value is not scalar
	 */
	public function intFilter($value) {
		return is_scalar($value) ? (int) $value : null;
	}

	/**
	 * Alias of intFilter()
	 * @param scalar $value Any scalar value
	 * @return integer The integer equivalent, NULL if value is not scalar
	 */
	public function integerFilter($value) {
		return $this->intFilter($value);
	}

	/**
	 * Returns the float equivalent of the given scalar value.
	 * @param scalar $value Any scalar value
	 * @return float The float equivalent, NULL if value is not scalar
	 */
	public function floatFilter($value) {
		return is_scalar($value) ? (float) $value : null;
	}

	/**
	 * Alias of floatFilter()
	 * @param scalar $value Any scalar value
	 * @return float The float equivalent, NULL if value is not scalar
	 */
	public function doubleFilter($value) {
		return $this->floatFilter($value);
	}

	/**
	 * Returns the string equivalent of the given scalar value.
	 * @param scalar $value Any scalar value
	 * @return string The string equivalent, NULL if value is not scalar
	 */
	public function stringFilter($value) {
		return is_scalar($value) ? (string) $value : null;
	}

	/**
	 * Returns the array equivalent of the given array or object.
	 * @param mixed Any array or object
	 * @return array The array equivalent, NULL if value is not array or object
	 */
	public function arrayFilter($value) {
		if (is_array($value))
			return $value;
		elseif (is_object($value))
			return (array) $value;
		else
			return null;
	}

	/**
	 * Returns the object equivalent of the given array or object.
	 * @param mixed Any array or object
	 * @return object The object equivalent, NULL if value is not array or object
	 */
	public function objectFilter($value) {
		return is_array($value) || is_object($value) ? (object) $value : null;
	}
}
