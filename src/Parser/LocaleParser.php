<?php
/**
 * @package plutonium\parser
 */

namespace Plutonium\Parser;

class LocaleParser extends AbstractParser {
	/**
	 * Replaces a &lt;pu:trans&gt; tag with a translated string.
	 * Expected attributes:
	 *   - phrase: original text for translation
	 * @param array $args Tag attributes
	 */
	public function transTag($args) {
		return $this->application->locale->localize($args['phrase']);
	}
}
