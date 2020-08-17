<?php
/**
 * @package plutonium\functions\array
 */

namespace Plutonium\Functions;

/**
 * Returns whether the given variable is an array containing only string keys.
 * @param mixed $var
 * @return boolean
 */
function is_assoc($var) {
	if (!is_array($var) || empty($var)) return false;

	return (bool) count(array_filter(array_keys($var), 'is_string'));
}

/**
 * Returns whether the given variable is an array containing only keys 'min'
 * and/or 'max'.
 * @param mixed $var
 * @return boolean
 */
function is_range($var) {
	if (!is_array($var) || empty($var)) return false;

	foreach (array_keys($var) as $key)
		if (!in_array($key, array('min', 'max'))) return false;

	return true;
}

/**
 * Returns the last item in an array.
 * @param array $array
 * @return mixed
 */
function array_peek(&$array) {
	$values = array_values($array);
	return $values[count($array) - 1];
}

/**
 * Returns whether the given variable is an object implementing the Traversable
 * interface.
 * @param mixed $var
 * @return boolean
 */
function is_traversable($var) {
	return is_object($var) && ($var instanceof \Traversable);
}
