<?php
/**
 * @package plutonium\utility
 */

namespace Plutonium\Utility;

/**
 * Utility class for handling IPv4 addresses.
 */
class Address {
	/**
	 * Generates a new class instance.
	 * TODO move logic to public constructor
	 * @param mixed $ip IP address as integer, string, or array of octets
	 * @return object Address object
	 */
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

	/**
	 * Parses an integer value into an array of octets.
	 * @param integer $int IP address as integer
	 * @return array Array of 4 integers (range 0-255)
	 */
	public static function parseInt($int) {
		$octets = array();

		for ($i = 0; $i < 4; $i++) {
			$factor = pow(0x100, $i);
			$mask   = 0xFF * $factor;

			$octets[$i] = ($int & $mask) / $factor;
		}

		return array_reverse($octets);
	}

	/**
	 * Parses an IPv4 string into an array of octets.
	 * @param string $str IP address as string
	 * @return array Array of 4 integers (range 0-255)
	 */
	public static function parseString($str) {
		$octets = array_slice(explode('.', $str), -4);

		foreach ($octets as &$octet)
			$octet = intval($octet) & 0xFF;

		while (count($octets) < 4)
			array_unshift($octets, 0x00);

		return $octets;
	}

	/**
	 * @ignore internal variable
	 */
	protected $_octets = array();

	/**
	 * @param array $octets Array of 4 integers (range 0-255)
	 */
	protected function __construct($octets = array()) {
		$this->_octets = $octets;
	}

	/**
	 * Formats address as an integer.
	 * @return integer IP address as integer
	 */
	public function toInt() {
		$octets = array_reverse($this->_octets);

		$int = 0;

		for ($i = 0; $i < 4; $i++)
			$int += $octets[$i] * pow(0x100, $i);

		return $int;
	}

	/**
	 * Formats address as a string.
	 * @return string IP address as string
	 */
	public function toString() {
		return implode('.', $this->_octets);
	}
}
