<?php
/**
 * @package plutonium\parser
 */

namespace Plutonium\Parser;

class UtilityParser extends AbstractParser {
	/**
	 * @param object $application The active Application object
	 * @param object $args AccessibleCollection containing timezone
	 */
	public function __construct($application, $args) {
		parent::__construct($application);
		date_default_timezone_set($args->timezone);
	}

	/**
	 * Replaces a &lt;pu:date&gt; tag with a formatted date string.
	 * Expected attributes:
	 *   - format: a strftime() compatible format string
	 *   - time: OPTIONAL UNIX timestamp, current time is used if omitted
	 * @param array $args The tag attributes
	 * @param string Formatted date
	 */
	public function dateTag($args) {
		$time   = isset($args['time']) ? strtotime($args['time']) : time();
		$format = isset($args['format']) ? $args['format'] : 'date_format_long';

		$regex = '/^(date|time|datetime)_format_(long|short|system)$/';

		if (preg_match($regex, $format))
			$format = $this->application->locale->localize($format);

		return strftime($format, $time);
	}
}
