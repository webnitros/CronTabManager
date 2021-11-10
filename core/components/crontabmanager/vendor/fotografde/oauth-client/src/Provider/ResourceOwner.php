<?php


namespace GetPhoto\Oauth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * ResourceOwner class for fotograf.de
 *
 * @author navihtot ivan.toth@fotograf.de
 */
class ResourceOwner implements ResourceOwnerInterface {
	/**
	 * Raw response
	 *
	 * @var array
	 */
	protected $response;

	/**
	 * Creates new resource owner.
	 *
	 * @param array $response
	 */
	public function __construct(array $response = array()) {
		$this->response = $response;
	}

	/**
	 * Get user id
	 *
	 * @return string|null
	 */
	public function getId() {
		return $this->response['user_data']['id'] ?: null;
	}

	/**
	 * Get user name
	 *
	 * @return string|null
	 */
	public function getName() {
		return $this->response['user_data']['name'] ?: null;
	}

	/**
	 * Get user email
	 *
	 * @return string|null
	 */
	public function getEmail() {
		return $this->response['user_data']['email'] ?: null;
	}

	/**
	 * Return all of the owner details available as an array.
	 *
     * @deprecated Dont use this anymore as it provides just user data and not all owner data
	 * @return array
	 */
	public function toArray() {
		return $this->response['user_data'];
	}

    /**
     * Get user data
     *
     * @return array
     */
	public function getUserData() {
        return $this->response['user_data'];
    }

    /**
     * Get scopes
     *
     * @return mixed
     */
    public function getScopes() {
        return $this->response['scopes'];
    }


}
