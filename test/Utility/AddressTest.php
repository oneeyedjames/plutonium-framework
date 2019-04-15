<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Utility\Address;

class AddressTest extends TestCase {
	public function testAddressArray() {
		$address = Address::newInstance([127, 0, 0, 1]);

		$this->assertEquals(0x7f000001, $address->toInt());
		$this->assertEquals('127.0.0.1', $address->toString());
	}

	public function testAddressString() {
		$address = Address::newInstance('127.0.0.1');

		$this->assertEquals(0x7f000001, $address->toInt());
		$this->assertEquals('127.0.0.1', $address->toString());
	}

	public function testAddressInt() {
		$address = Address::newInstance(0x7F000001);

		$this->assertEquals(0x7f000001, $address->toInt());
		$this->assertEquals('127.0.0.1', $address->toString());
	}

	public function testAddressTruncatedString() {
		$address = Address::newInstance('127.0.0');

		$this->assertEquals(0x007f0000, $address->toInt());
		$this->assertEquals('0.127.0.0', $address->toString());
	}

	public function testAddressTruncatedInt() {
		$address = Address::newInstance(0x7F0000);

		$this->assertEquals(0x007f0000, $address->toInt());
		$this->assertEquals('0.127.0.0', $address->toString());
	}

	public function testAddressOverflownString() {
		$address = Address::newInstance('1.127.0.0.1');

		$this->assertEquals(0x7f000001, $address->toInt());
		$this->assertEquals('127.0.0.1', $address->toString());
	}

	public function testAddressOverflownInt() {
		$address = Address::newInstance(0x017F000001);

		$this->assertEquals(0x7f000001, $address->toInt());
		$this->assertEquals('127.0.0.1', $address->toString());
	}
}
