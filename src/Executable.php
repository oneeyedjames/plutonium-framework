<?php
/**
 * @package plutonium
 */

namespace Plutonium;

interface Executable {
	public function initialize();
	public function execute();
}
