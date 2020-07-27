<?php
/**
 * @package plutonium\http
 */

namespace Plutonium\Http;

/**
 * @property-read mixed $identity Unique user identifier
 * @property-read boolean $authenticated Whether use is authenticated
 */
class BasicAuthenticator extends AbstractAuthenticator {
	/**
	 * @ignore interal variable
	 */
	protected $_func;

	/**
	 * Callback must accept two string arguments:
	 *   - $username
	 *   - $password
	 * @param callable $func
	 */
	public function __construct($func) {
		$this->_func = $func;
	}

	/**
	 * Returns username and password in associative array.
	 * @return array Username and password
	 */
	public function getCredential() {
		if ($header = $_SERVER['HTTP_AUTHORIZATION']) {
			list($type, $data) = explode(' ', $header);

			if (strtoupper($type) == 'BASIC') {
				$data = base64_decode($data);
				list($username, $password) = explode(':', $data);

				return compact('username', 'password');
			}
		}
	}

	/**
	 * Locates a user for the given username and password.
	 * @param array $credential Username and password
	 * @return mixed Unique user identifier
	 */
	public function getIdentity($credential) {
		if (!is_callable($this->_func)) return false;

		extract($credential);

		return call_user_func($this->_func, $username, $password);
	}
}
