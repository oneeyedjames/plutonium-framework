<?php
/**
 * @package plutonium
 */

namespace Plutonium;

interface Renderable {
	public function render();
	public function localize($text);
}
