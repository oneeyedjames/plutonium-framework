<?php
/**
 * @package plutonium\http
 */

namespace Plutonium\Http;

/**
 * @property-read mixed $identity Unique user identifier
 * @property-read boolean $authenticated Whether use is authenticated
 */
class BearerAuthenticator extends AbstractAuthenticator {
	/**
	 * @ignore interal variable
	 */
	protected $_secret;

	/**
	 * @param string $secret JWT signing key
	 */
	public function __construct($secret) {
		$this->_secret = $secret;
	}

	/**
	 * Returns JWT object.
	 * @return object JWT object
	 */
	public function getCredential() {
		if ($header = $_SERVER['HTTP_AUTHORIZATION']) {
			list($type, $data) = explode(' ', $header);

			if (strtoupper($type) == 'BEARER') {
				return Jwt::parse($data);
			}
		}
	}

	/**
	 * Validates the JWT and returns the subject.
	 * @param array $credential JWT object
	 * @return mixed Unique user identifier
	 */
	public function getIdentity($credential) {
		if ($credential->validate($this->_secret) && !$credential->expired) {
			return $credential->sub;
		}

		return false;
	}
}
