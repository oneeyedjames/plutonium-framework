<?php
/**
 * @package plutonium\utility
 */

namespace Plutonium\Utility;

class Address {
	public static function newInstance($ip) {
		$octets = array();

		if (is_int($ip))
			$octets = self::parseInt($ip);
		elseif (is_string($ip))
			$octets = self::parseString($ip);
		elseif (is_array($ip))
			$octets = $ip;

		return new self($octets);
	}

	public static function parseInt($int) {
		$octets = array();

		for ($i = 0; $i < 4; $i++) {
			$factor = pow(0x100, $i);
			$mask   = 0xFF * $factor;

			$octets[$i] = ($int & $mask) / $factor;
		}

		return array_reverse($octets);
	}

	public static function parseString($str) {
		$octets = array_slice(explode('.', $str), -4);

		foreach ($octets as &$octet)
			$octet = intval($octet) & 0xFF;

		while (count($octets) < 4)
			array_unshift($octets, 0x00);

		return $octets;
	}

	protected $_octets = array();

	protected function __construct($octets = array()) {
		$this->_octets = $octets;
	}

	public function toInt() {
		$octets = array_reverse($this->_octets);

		$int = 0;

		for ($i = 0; $i < 4; $i++)
			$int += $octets[$i] * pow(0x100, $i);

		return $int;
	}

	public function toString() {
		return implode('.', $this->_octets);
	}
}
