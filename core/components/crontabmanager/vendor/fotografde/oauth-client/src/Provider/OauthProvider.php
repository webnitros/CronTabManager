<?php

namespace GetPhoto\Oauth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use InvalidArgumentException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use League\OAuth2\Client\Grant\AbstractGrant;
use GetPhoto\Oauth2\Client\Grant\PasswordFtp;
use GetPhoto\Oauth2\Client\Grant\Impersonate;
use Psr\Http\Message\ResponseInterface;
use GetPhoto\Oauth2\Client\Log\LoggerInterface;
use GetPhoto\Oauth2\Client\Log\Logger;
use GetPhoto\Oauth2\Client\Cache\CacherInterface;
use GetPhoto\Oauth2\Client\Cache\Cacher;

/**
 * OauthProvider for fotograf.de
 *
 * @author navihtot ivan.toth@fotograf.de
 */
class OauthProvider extends AbstractProvider {

	use BearerAuthorizationTrait;

	const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'resource_owner_id';
	/**
	 * Domain
	 *
	 * @var string
	 */
	public $domain = 'https://auth.fotocdn.de';

	/**
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * @var CacherInterface
	 */
	protected $cacher;

	protected $cacheOn = true;
	protected $cachePrefix = '';
	protected $cacheSafeInterval = 5;

	 /**
     * Constructs an OAuth 2.0 service provider.
     *
     * @param array $options An array of options to set on this provider.
     *     Options include `clientId`, `clientSecret`, `redirectUri`, and `state`.
     *
     *     Additional for fde:
     *     		`cacheOn` bool - wheather to use cache or not in getAccessTokenSmart (default is true)
	 *          `cacheSafeInterval` float - how many minutes before expiration to renew token (default is 5)
	 *          `cachePrefix` string - important to set if using provider across same system but different use case (default is
	 *          						'');
     *
     * @param array $collaborators An array of collaborators that may be used to
     *     override this provider's default behavior. Collaborators include
     *     `grantFactory`, `requestFactory`, `httpClient`, and `randomFactory`.
     *     Individual providers may introduce more collaborators, as needed.
     *
     *     Additional for fde:
     *     		`logger`
     *     		`cacher`
     *
     */
	public function __construct(array $options = [], array $collaborators = []) {

		parent::__construct($options, $collaborators);

		//password_ftp grant
		$this->grantFactory->setGrant('password_ftp', new PasswordFtp);

		//impersonate grant
		$this->grantFactory->setGrant('impersonate', new Impersonate);

		//setting logger
		if (empty($collaborators['logger'])) {
			$collaborators['logger'] = new Logger();
		}
		$this->setLogger($collaborators['logger']);

		//setting cacher
		if (empty($collaborators['cacher'])) {
			$collaborators['cacher'] = new Cacher();
		}
		$this->setCacher($collaborators['cacher']);

	}

	/**
	 * Sets Logger instance
	 *
	 * @param  LoggerInterface $logger
	 * @return self
	 */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Sets Cacher insstance
     *
     * @param  CacherInterface $cacher
     * @return self
     */
    public function setCacher(CacherInterface $cacher)
    {
        $this->cacher = $cacher;
        return $this;
    }

    /**
     * Get cacher instance
     *
     * @return CacherInterface
     */
    public function getCacher() {
    	return $this->cacher;
    }

    /**
     * Get logger instance
     *
     * @return  LoggerInterface
     */
    public function getLogger() {
    	return $this->logger;
    }


	/**
	 * Get authorization url to begin OAuth flow
	 *
	 * @return string
	 */
	public function getBaseAuthorizationUrl() {
		return $this->domain . '/oauth/authorize';
	}

	/**
	 * Get access token url to retrieve token
	 *
	 * @param  array $params
	 *
	 * @return string
	 */
	public function getBaseAccessTokenUrl(array $params) {
		return $this->domain . '/oauth/access_token';
	}

	/**
	 * Get provider url to fetch user details
	 *
	 * @param  AccessToken $token
	 *
	 * @return string
	 */
	public function getResourceOwnerDetailsUrl(AccessToken $token) {
		return $this->domain . '/oauth/resource_owner';
	}

	/**
	 * Get logout url
	 *
	 * @return string
	 */
	public function getLogoutUrl() {
		return $this->domain . '/oauth/logout';
	}

	/**
	 * Get the default scopes used by this provider.
	 *
	 * This should not be a complete list of all scopes, but the minimum
	 * required for the provider user interface!
	 *
	 * @return array
	 */
	protected function getDefaultScopes() {
		return [];
	}

	/**
	 * Check a provider response for errors.
	 *
	 * @link   https://developer.github.com/v3/#client-errors
	 * @throws IdentityProviderException
	 * @param  ResponseInterface $response
	 * @param  string $data Parsed response data
	 * @return void
	 */
	protected function checkResponse(ResponseInterface $response, $data) {
		if ($response->getStatusCode() >= 400) {
			throw new IdentityProviderException(
				isset($data['error_description']) ? $data['error_description'] : $response->getReasonPhrase(),
				$response->getStatusCode(),
				$response
			);
		}
	}

	/**
	 * Generate a user object from a successful user details request.
	 *
	 * @param array $response
	 * @param AccessToken $token
	 * @return League\OAuth2\Client\Provider\ResourceOwnerInterface
	 */
	protected function createResourceOwner(array $response, AccessToken $token) {
		return new ResourceOwner($response);
	}

	/**
	 * Get access token smart way
	 *
	 * Gets token and uses cache not to hit server too many times
	 *
	 * @param  mixed $grant
	 * @param  array  $options same as in getAccessToken with additional:
	 *                         'cacheOn' bool - wheather to use cache or not (default is true)
	 *                         'cacheSafeInterval' float - how many minutes before expiration to renew token (default is 5)
	 *                         'cachePrefix' string - override of value set in constructor, important to set if using provider *												across same system but different use case (default is '');
	 * @return string Access Token
	 */
	public function getAccessTokenSmart($grant, array $options = []) {

		$cacheOn = $this->cacheOn;
 		if (isset($options['cacheOn'])) {
			$cacheOn = (bool)$options['cacheOn'];
		}
		$cacheSafeInterval = floatval($this->cacheSafeInterval);
		if (isset($options['cacheSafeInterval'])) {
			$cacheSafeInterval = floatval($options['cacheSafeInterval']);
		}

		if (isset($options['cachePrefix'])) {
			$this->getCacher()->setPrefix($options['cachePrefix']);
		}
		else {
			$this->getCacher()->setPrefix($this->cachePrefix);
		}

		try {

			//return cached token if not expired
			if ($cacheOn) {
				$cachedToken = $this->getCacher()->read('token');
				$cachedTokenExpire = $this->getCacher()->read('token_expire');

				if (!empty($cachedToken) && !empty($cachedTokenExpire)) {
					//if token does not expire in next 5 minutes -> reuse it
					if (time() + $cacheSafeInterval  * 60 < (int) $cachedTokenExpire)  {
						return $cachedToken;
					}
				}
			}

			//get new token
			$token = $this->getAccessToken($grant, $options);

			//try one more time
			if (empty($token)) {
				$token = $this->getAccessToken($grant, $options);
				if (empty($token)) {
					throw new \Exception('Fetching token from oauth server failed.');
				}
			}

			//cache token
			if ($cacheOn) {
				$this->getCacher()->write('token', $token->getToken());
				$this->getCacher()->write('token_expire', $token->getExpires());
			}

			//set prefix again to the value from constructor
			$this->getCacher()->setPrefix($this->cachePrefix);

			return $token->getToken();
		}
		catch (\Exception $e) {
			//set prefix again to the value from constructor
			$this->getCacher()->setPrefix($this->cachePrefix);

			if (!empty($this->logger)) {
				$this->logger->write(
					'oauth_provider_' . $this->cachePrefix,
					"Failed to get access token \n" .
					"\t \t Exception class: " .get_class($e) . "\n" .
					"\t \t msg: " . $e->getMessage() . "\n" .
					"\t \t code: " . $e->getCode() . "\n"
				);
			}
			return false;
		}
	}

	/**
	 * Clear token cache
	 *
	 * Call this when you want to forget cached tokens
	 * @param  string $prefix - prefix for cache
	 * @return bool success
	 */
	public function clearTokenCache($prefix = '') {
		if (!empty($prefix)) {
			$this->getCacher()->setPrefix($prefix);
		}
		else {
			$this->getCacher()->setPrefix($this->cachePrefix);
		}

		$success = $this->getCacher()->clear();
		//set prefix again to the value from constructor
		$this->getCacher()->setPrefix($this->cachePrefix);

		return $success;
	}


	/**
	 * Expire access token (logout)
	 *
	 * @param  mixed $grant
	 * @param  array $options
	 * @throws IdentityProviderException
	 * @return AccessTokenInterface
	 */
	public function expireAccessToken($token)
	{
		try {
			$request = $this->getAuthenticatedRequest(self::METHOD_GET, $this->getLogoutUrl(), $token);

			if ($this->getResponse($request)->getStatusCode() === 200) {
				return true;
			}
		}
		catch (\Exception $e) {
			if (!empty($this->logger)) {
				$this->logger->write(
					'oauth_provider_' . $this->cachePrefix,
					"Failed to expire access token \n" .
					"\t \t Exception class: " .get_class($e) . "\n" .
					"\t \t msg: " . $e->getMessage() . "\n" .
					"\t \t code: " . $e->getCode() . "\n"
				);
			}
		}

		return false;
	}

}
