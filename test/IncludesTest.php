<?php

use PHPUnit\Framework\TestCase;

class IncludesTest extends TestCase {
	public function testConstants() {
		$this->assertNotEmpty(DS);
		$this->assertNotEmpty(PS);
		$this->assertNotEmpty(LS);
		$this->assertNotEmpty(FS);
		$this->assertNotEmpty(BS);

		$this->assertNotEmpty(PU_PATH_ROOT);
		$this->assertNotEmpty(PU_PATH_BASE);

		// $this->assertNotEmpty(PU_URL_ROOT);
		// $this->assertNotEmpty(PU_URL_BASE);

		// $this->assertTrue(defined('PU_URL_PATH'));
	}
}
