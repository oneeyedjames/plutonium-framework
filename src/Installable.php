<?php
/**
 * @package plutonium
 */

namespace Plutonium;

interface Installable {
	public function install();
	public function uninstall();
}
