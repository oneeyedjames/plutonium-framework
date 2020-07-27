<?php
/**
 * @package plutonium\http
 */

namespace Plutonium\Http;

/**
 * @property-read mixed $identity Unique user identifier
 * @property-read boolean $authenticated Whether use is authenticated
 */
class CookieAuthenticator extends BearerAuthenticator {
	/**
	 * @ignore internal variable
	 */
	private $_cookie;

	/**
	 * @param string $secret JWT signing key
	 * @param string $cookie Cookie name
	 */
	public function __construct($secret, $cookie) {
		parent::__construct($secret);

		$this->_cookie = $cookie;
	}

	/**
	 * Returns JWT object.
	 * @return object JWT object
	 */
	public function getCredential() {
		if ($cookie = $_COOKIE[$this->_cookie]) {
			return Jwt::parse($cookie);
		}
	}
}
