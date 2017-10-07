<?php

namespace Plutonium\Parser;

class LocaleParser extends AbstractParser {
	public function transTag($args) {
		return $this->_application->locale->localize($args['phrase']);
	}
}
