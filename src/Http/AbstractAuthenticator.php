<?php
/**
 * @package plutonium\http
 */

namespace Plutonium\Http;

/**
 * @property-read mixed $identity Unique user identifier
 * @property-read boolean $authenticated Whether use is authenticated
 */
abstract class AbstractAuthenticator {
	/**
	 * @ignore internal variable
	 */
	protected $_identity;

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'identity':
				return $this->_identity;
			case 'authenticated':
				return (bool) $this->_identity;
		}
	}

	/**
	 * Returns the authenticated user identifier, or FALSE on failure
	 * @return mixed Unique user identifier
	 */
	public function authenticate() {
		if (is_null($this->_identity)) {
			if ($credential = $this->getCredential()) {
				$this->_identity = $this->getIdentity($credential);
			} else {
				$this->_identity = false;
			}
		}

		return $this->_identity;
	}

	/**
	 * Returns the scheme-specific user credential from the current request.
	 * @return mixed Scheme-specific user credential
	 */
	abstract public function getCredential();

	/**
	 * Locates a user for the given credential.
	 * @param mixed $credential Scheme-specific user credential
	 * @return mixed Unique user identifier, NULL on failure
	 */
	abstract public function getIdentity($credential);
}
