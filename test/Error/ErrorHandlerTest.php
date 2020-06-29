<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Error\ErrorHandler;

class ErrorHandlerTest extends TestCase {
	public function setUp() {
		$this->handler = new ErrorHandlerMock;

		ErrorHandler::register($this->handler);

		$this->assertEquals(0, $this->handler->level);
		$this->assertNull($this->handler->message);
		$this->assertNull($this->handler->file);
		$this->assertEquals(0, $this->handler->line);
	}

	public function tearDown() {
		ErrorHandler::reset();

		unset($this->handler);
	}

	public function testTriggerError() {
		trigger_error('Test Error Message', E_USER_ERROR);

		$this->assertEquals(E_USER_ERROR, $this->handler->level);
		$this->assertEquals('Test Error Message', $this->handler->message);
		$this->assertEquals(__FILE__, $this->handler->file);
		$this->assertGreaterThan(0, $this->handler->line);
		$this->assertTrue(is_int($this->handler->line));
	}

	public function testTriggerWarning() {
		trigger_error('Test Warning Message', E_USER_WARNING);

		$this->assertEquals(E_USER_WARNING, $this->handler->level);
		$this->assertEquals('Test Warning Message', $this->handler->message);
		$this->assertEquals(__FILE__, $this->handler->file);
		$this->assertGreaterThan(0, $this->handler->line);
		$this->assertTrue(is_int($this->handler->line));
	}

	public function testTriggerNotice() {
		trigger_error('Test Notice Message', E_USER_NOTICE);

		$this->assertEquals(E_USER_NOTICE, $this->handler->level);
		$this->assertEquals('Test Notice Message', $this->handler->message);
		$this->assertEquals(__FILE__, $this->handler->file);
		$this->assertGreaterThan(0, $this->handler->line);
		$this->assertTrue(is_int($this->handler->line));
	}
}

class ErrorHandlerMock extends ErrorHandler {
	var $level   = 0;
	var $message = null;
	var $file    = null;
	var $line    = 0;

	public function handleError($message, $file, $line) {
		return $this->setData(E_USER_ERROR, $message, $file, $line);
	}

	public function handleWarning($message, $file, $line) {
		return $this->setData(E_USER_WARNING, $message, $file, $line);
	}

	public function handleNotice($message, $file, $line) {
		return $this->setData(E_USER_NOTICE, $message, $file, $line);
	}

	private function setData($level, $message, $file, $line) {
		$this->level   = $level;
		$this->message = $message;
		$this->file    = $file;
		$this->line    = $line;

		return true;
	}
}
