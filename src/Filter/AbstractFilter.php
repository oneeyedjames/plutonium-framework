<?php
/**
 * @package plutonium\filter
 */

namespace Plutonium\Filter;

/**
 * Base class for all filter objects.
 * Child classes must provide a method for each supported filter type. For
 * instance, a filter type named 'Alpha' must provide a method with the
 * signature alphaFilter($value).
 */
abstract class AbstractFilter {
	/**
	 * Filters the given value according to the named filter type. If the filter
	 * type is not supported, the orignal value is returned.
	 * @param mixed $value Any value
	 * @return mixed The filtered value
	 */
	public function filter($value, $type) {
		if ($callback = $this->canHandle($type))
			return call_user_func($callback, $value);

		return $value;
	}

	/**
	 * Determines whether or not the object supports the given filter type.
	 * @param string $type Filter type name
	 * @return boolean Whether the filter type is supported
	 */
	public function canHandle($type) {
		$method = strtolower($type) . 'Filter';

		if (method_exists($this, $method))
			return array($this, $method);

		return false;
	}
}
