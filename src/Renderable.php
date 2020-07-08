<?php
/**
 * @package plutonium
 */

namespace Plutonium;

interface Renderable {
	public function render();

	/**
	 * @param string $text
	 */
	public function localize($text);
}
