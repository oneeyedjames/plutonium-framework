<?php

namespace Plutonium\Parser;

class LocaleParser extends AbstractParser {
	public function transTag($args) {
		return $this->application->locale->localize($args['phrase']);
	}
}
