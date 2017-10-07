<?php

namespace Plutonium\Parser;

class UtilityParser extends AbstractParser {
	public function __construct($application, $args) {
		parent::__construct($application);
		date_default_timezone_set($args->timezone);
	}

	public function dateTag($args) {
		$time   = isset($args['time']) ? strtotime($args['time']) : time();
		$format = isset($args['format']) ? $args['format'] : 'date_format_long';

		$regex = '/^(date|time|datetime)_format_(long|short|system)$/';

		if (preg_match($regex, $format))
			$format = $this->_application->locale->localize($format);

		return strftime($format, $time);
	}
}
