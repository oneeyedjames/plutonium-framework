<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Loader;

class LoaderTest extends TestCase {
	/*
	 * Tests that classes and interfaces can be loaded from source files.
	 */
	public function testImport() {
		Loader::autoload(PU_PATH_LIB);

		$this->assertContains(PU_PATH_LIB, Loader::getPaths());

		$this->assertTrue(Loader::import('\Plutonium\AccessObject'));
		$this->assertTrue(interface_exists('\Plutonium\Accessible', false));
	}
}
