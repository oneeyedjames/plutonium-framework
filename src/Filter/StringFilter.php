<?php
/**
 * @package plutonium\filter
 */

namespace Plutonium\Filter;

/**
 * Reference implementation for common string filter types.
 * Supported types:
 *   - Alpha
 *   - AlNum
 *   - Digit
 *   - Hexit
 *   - LCase
 *   - UCase
 */
class StringFilter extends AbstractFilter {
	/**
	 * Returned string will only contain alpha characters.
	 * @param string $value Any string value
	 * @return string String of alpha characters
	 */
	public function alphaFilter($value) {
		return preg_replace('/[^A-Z]/i', '', $value);
	}

	/**
	 * Returned string will only contain alphanumeric characters.
	 * @param string $value Any string value
	 * @return string String of alphanumeric characters
	 */
	public function alnumFilter($value) {
		return preg_replace('/[^A-Z0-9]/i', '', $value);
	}

	/**
	 * Returned string will only contain decimal numerals (0-9).
	 * @param string $value Any string value
	 * @return string String of decimal numerals
	 */
	public function digitFilter($value) {
		return preg_replace('/[^0-9]/', '', $value);
	}

	/**
	 * Returned string will only contain hexadecimal numerals(0-9, A-F).
	 * @param string $value Any string value
	 * @return string String of hexadecimal numerals
	 */
	public function hexitFilter($value) {
		return preg_replace('/[^A-F0-9]/i', '', $value);
	}

	/**
	 * Returned string will only contain lower-case alpha characters.
	 * @param string $value Any string value
	 * @return string String of lower-case alpha characters
	 */
	public function lcaseFilter($value) {
		return preg_replace('/[^a-z]/', '', $value);
	}

	/**
	 * Returned string will only contain upper-case alpha characters.
	 * @param string $value Any string value
	 * @return string String of upper-case alpha characters
	 */
	public function ucaseFilter($value) {
		return preg_replace('/[^A-Z]/', '', $value);
	}
}
