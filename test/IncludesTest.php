<?php

use PHPUnit\Framework\TestCase;

class IncludesTest extends TestCase {
	public function testConstants() {
		$this->assertNotEmpty(DS);
		$this->assertNotEmpty(PS);
		$this->assertNotEmpty(LS);
		$this->assertNotEmpty(FS);
		$this->assertNotEmpty(BS);

		$this->assertNotEmpty(PU_PATH_BASE);
		$this->assertNotEmpty(PU_PATH_LIB);
	}
}
