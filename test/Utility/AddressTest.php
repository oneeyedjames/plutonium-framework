<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Utility\Address;

class AddressTest extends TestCase {
	/*
	 * Tests that an address object can be created from an array.
	 */
	public function testAddressArray() {
		$address = Address::newInstance([127, 0, 0, 1]);

		$this->assertEquals(0x7f000001, $address->toInt());
		$this->assertEquals('127.0.0.1', $address->toString());
	}

	/*
	 * Tests that an address object can be created from a string.
	 */
	public function testAddressString() {
		$address = Address::newInstance('127.0.0.1');

		$this->assertEquals(0x7f000001, $address->toInt());
		$this->assertEquals('127.0.0.1', $address->toString());
	}

	/*
	 * Tests that an address object can be created from an integer.
	 */
	public function testAddressInt() {
		$address = Address::newInstance(0x7F000001);

		$this->assertEquals(0x7f000001, $address->toInt());
		$this->assertEquals('127.0.0.1', $address->toString());
	}

	/*
	 * Tests that short strings are left-padded to proper length.
	 */
	public function testAddressTruncatedString() {
		$address = Address::newInstance('127.0.0');

		$this->assertEquals(0x007f0000, $address->toInt());
		$this->assertEquals('0.127.0.0', $address->toString());
	}

	/*
	 * Tests that short integers are left-padded to proper length.
	 */
	public function testAddressTruncatedInt() {
		$address = Address::newInstance(0x7F0000);

		$this->assertEquals(0x007f0000, $address->toInt());
		$this->assertEquals('0.127.0.0', $address->toString());
	}

	/*
	 * Tests that long strings are left-truncated to proper length.
	 */
	public function testAddressOverflownString() {
		$address = Address::newInstance('1.127.0.0.1');

		$this->assertEquals(0x7f000001, $address->toInt());
		$this->assertEquals('127.0.0.1', $address->toString());
	}

	/*
	 * Tests that long integers are left-truncated to proper length.
	 */
	public function testAddressOverflownInt() {
		$address = Address::newInstance(0x017F000001);

		$this->assertEquals(0x7f000001, $address->toInt());
		$this->assertEquals('127.0.0.1', $address->toString());
	}
}
