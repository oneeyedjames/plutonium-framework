<?php
/**
 * @package plutonium\http
 */

namespace Plutonium\Http;

/**
 * @property-read array $header JWT Header parameters
 * @property-read array $payload JWT Claims
 * @property-read string $signature JWT signature
 * @property-read boolean $expired Whether the JWT has expired
 */
class Jwt {
	/**
	 * Parses a JWT string into an object.
	 * @param string $raw JWT string
	 * @return object JWT object
	 */
	public static function parse($raw) {
		list($header, $payload, $signature) = explode('.', $raw);

		$jwt = new self();
		$jwt->_header = json_decode(self::decode($header), true);
		$jwt->_payload = json_decode(self::decode($payload), true);
		$jwt->_signature = self::decode($signature);

		return $jwt;
	}

	/**
	 * Base64Url encodes a raw string.
	 * @param string $data Raw string
	 * @return string Base64Url-encoded string
	 */
	protected static function encode($data) {
		$data = base64_encode($data);
		$data = strtr($data, '+/', '-_');
		$data = rtrim($data, '=');

		return $data;
	}

	/**
	 * Decodes a Base64Url-encoded string.
	 * @param string $data Base64Url-encoded string
	 * @return string Raw string
	 */
	protected static function decode($data) {
		$size = strlen($data) % 4;
		$data = strtr($data, '-_', '+/');
		$data = str_pad($data, $size, '=', STR_PAD_RIGHT);
		$data = base64_decode($data);

		return $data;
	}

	/**
	 * @ignore interal variable
	 */
	private $_header = ['typ' => 'JWT'];

	/**
	 * @ignore interal variable
	 */
	private $_payload = [];

	/**
	 * @ignore interal variable
	 */
	private $_signature = null;

	/**
	 * @param string $alg OPTIONAL Signature algorithm
	 */
	public function __construct($alg = 'HS256') {
		$this->_header['alg'] = $alg;
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'header': return $this->_header;
			case 'payload': return $this->_payload;
			case 'signature': return $this->_signature;
			case 'expired':
				if ($time = @$this->_payload['exp'])
					return $time < time();

				return false;
			default:
				return @$this->_payload[$key];
		}
	}

	/**
	 * @ignore magic method
	 */
	public function __set($key, $value) {
		$this->_payload[$key] = $value;
	}

	/**
	 * @ignore magic method
	 */
	public function __toString() {
		return implode('.', [
			self::encode(json_encode($this->header)),
			self::encode(json_encode($this->payload)),
			self::encode($this->signature)
		]);
	}

	/**
	 * Populates this JWT's signature based on the given key.
	 * @param string $key Secret key for signature
	 */
	public function sign($key) {
		return $this->_signature = $this->createSignature($key);
	}

	/**
	 * Determines whether this JWT's signature is valid for the given key.
	 * @param string $key Secret key for signature
	 * @return boolean Whether the signature is valid
	 */
	public function validate($key) {
		return $this->createSignature($key) == $this->signature
			&& !is_null($this->signature);
	}

	/**
	 * Calculates this JWT's signature with the given key.
	 * @param string $key Secret key for signature
	 * @return string JWT signature
	 */
	protected function createSignature($key) {
		$data = implode('.', [
			self::encode(json_encode($this->header)),
			self::encode(json_encode($this->payload))
		]);

		switch (strtoupper($this->header['alg'])) {
			case 'HS256':
				return hash_hmac('sha256', $data, $key, true);
		}
	}
}
