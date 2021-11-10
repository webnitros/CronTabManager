<?php

use GetPhoto\Oauth2\Client\Provider\OauthProvider;
use Mockery as m;
use GetPhoto\Oauth2\Client\Cache\CacherInterface;
use GetPhoto\Oauth2\Client\Log\LoggerInterface;


//TO DO: Test other grant types

class OauthProviderTest extends PHPUnit_Framework_TestCase {


	protected $provider;

	protected function setUp() {
		$this->provider = new OauthProvider(
			[
				'clientId' => 'mock_client_id',
				'clientSecret' => 'mock_secret',
				'redirectUri' => 'none'
			]
		);
	}

	public function tearDown() {
		unset($this->provider);
		m::close();
		parent::tearDown();
	}

	private function getAccessToken() {
		return $this->getMockBuilder('League\OAuth2\Client\Token\AccessToken')
			->disableOriginalConstructor()
			->getMock();
	}

	public function testGetAuthorizationUrl() {
		$url = $this->provider->getAuthorizationUrl();
		$uri = parse_url($url);
		parse_str($uri['query'], $query);
		$this->assertArrayHasKey('client_id', $query);
		$this->assertArrayHasKey('redirect_uri', $query);
		$this->assertArrayHasKey('state', $query);
		$this->assertArrayHasKey('scope', $query);
		$this->assertArrayHasKey('response_type', $query);
		$this->assertArrayHasKey('approval_prompt', $query);
		$this->assertNotNull($this->provider->getState());
	}

	public function testGetBaseAuthorizationUrl() {
		$url = $this->provider->getBaseAuthorizationUrl();
		$uri = parse_url($url);
		$this->assertEquals('/oauth/authorize', $uri['path']);
	}

	public function testGetBaseAccessTokenUrl() {
		$url = $this->provider->getBaseAccessTokenUrl([]);
		$uri = parse_url($url);
		$this->assertEquals('/oauth/access_token', $uri['path']);
	}


	public function testGetResourceOwnerDetailsUrl() {
		$url = $this->provider->getResourceOwnerDetailsUrl($this->getAccessToken());
		$uri = parse_url($url);
		$this->assertEquals('/oauth/resource_owner', $uri['path']);
	}


	public function testGetResourceOwner() {
		$userId = rand(1000, 9999);
		$name = uniqid();
		$email = uniqid();

		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=mock_access_token');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$userResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$userResponse->shouldReceive('getBody')->andReturn(
			'{
		  "resource_owner_type": "user",
		  "resource_owner_id": "' . $userId . '",
		  "client_id": "mock_client",
		  "scopes": {
		    "mock_scope": {
		      "id": "mock_scope",
		      "description": "some mock scope"
		    }
		  },
		  "user_data": {
		    "id": ' . $userId . ',
		    "name": "' . $name . '",
		    "email": "' . $email . '",
		    "created_at": "2016-02-29 15:03:36",
		    "updated_at": "2016-03-02 12:25:40"
		  }
		}'
		);
		$userResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
		$userResponse->shouldReceive('getStatusCode')->andReturn(200);

		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(2)
			->andReturn($postResponse, $userResponse);
		$this->provider->setHttpClient($client);

		$token = $this->provider->getAccessToken('client_credentials', ['scope' => 'mock_scope']);
		$user = $this->provider->getResourceOwner($token);

		$this->assertEquals($userId, $user->getId());
		$this->assertEquals($userId, $user->toArray()['id']);
		$this->assertEquals($name, $user->getName());
		$this->assertEquals($name, $user->toArray()['name']);
		$this->assertEquals($email, $user->getEmail());
		$this->assertEquals($email, $user->toArray()['email']);
	}

	public function testPasswordFtpGrant() {
		$userId = rand(1000, 9999);
		$name = uniqid();
		$email = uniqid();

		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=mock_access_token');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$userResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$userResponse->shouldReceive('getBody')->andReturn(
			'{
          "resource_owner_type": "user",
          "resource_owner_id": "' . $userId . '",
          "client_id": "mock_client",
          "scopes": {
            "mock_scope": {
              "id": "mock_scope",
              "description": "some mock scope"
            }
          },
          "user_data": {
            "id": ' . $userId . ',
            "name": "' . $name . '",
            "email": "' . $email . '",
            "created_at": "2016-02-29 15:03:36",
            "updated_at": "2016-03-02 12:25:40"
          }
        }'
		);
		$userResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
		$userResponse->shouldReceive('getStatusCode')->andReturn(200);

		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(2)
			->andReturn($postResponse, $userResponse);
		$this->provider->setHttpClient($client);

		$token = $this->provider->getAccessToken(
			'password_ftp',
			['scope' => 'mock_scope', 'username' => 'tt', 'password' => 'tt']
		);
		$this->assertEquals('mock_access_token', $token->getToken());
		$user = $this->provider->getResourceOwner($token);

		$this->assertEquals($userId, $user->getId());
		$this->assertEquals($userId, $user->toArray()['id']);
		$this->assertEquals($name, $user->getName());
		$this->assertEquals($name, $user->toArray()['name']);
		$this->assertEquals($email, $user->getEmail());
		$this->assertEquals($email, $user->toArray()['email']);
	}

	public function testImpersonateGrant() {
		$userId = rand(1000, 9999);
		$name = uniqid();
		$email = uniqid();

		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=mock_access_token');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$userResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$userResponse->shouldReceive('getBody')->andReturn(
			'{
          "resource_owner_type": "user",
          "resource_owner_id": "' . $userId . '",
          "client_id": "mock_client",
          "scopes": {
            "mock_scope": {
              "id": "mock_scope",
              "description": "some mock scope"
            }
          },
          "user_data": {
            "id": ' . $userId . ',
            "name": "' . $name . '",
            "email": "' . $email . '",
            "created_at": "2016-02-29 15:03:36",
            "updated_at": "2016-03-02 12:25:40"
          }
        }'
		);
		$userResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
		$userResponse->shouldReceive('getStatusCode')->andReturn(200);

		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(2)
			->andReturn($postResponse, $userResponse);
		$this->provider->setHttpClient($client);

		$token = $this->provider->getAccessToken(
			'impersonate',
			['scope' => 'mock_scope', 'username' => 'tt']
		);
		$this->assertEquals('mock_access_token', $token->getToken());
		$user = $this->provider->getResourceOwner($token);

		$this->assertEquals($userId, $user->getId());
		$this->assertEquals($userId, $user->toArray()['id']);
		$this->assertEquals($name, $user->getName());
		$this->assertEquals($name, $user->toArray()['name']);
		$this->assertEquals($email, $user->getEmail());
		$this->assertEquals($email, $user->toArray()['email']);
	}

	/**
	 * @expectedException League\OAuth2\Client\Provider\Exception\IdentityProviderException
	 **/
	public function testExceptionThrownWhenErrorObjectReceived() {
		$error_description = uniqid();
		$status = rand(400, 600);
		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn(
			'{"error":"access_denied","error_description":"' . $error_description . '"}'
		);
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
		$postResponse->shouldReceive('getStatusCode')->andReturn($status);
		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(1)
			->andReturn($postResponse);
		$this->provider->setHttpClient($client);
		$token = $this->provider->getAccessToken('client_credentials', ['scope' => 'mock_scopes']);
	}

	public function testConstructorSetsCacher() {
        $mockCacher = m::mock(CacherInterface::class);
        $provider = new OauthProvider(
			[
				'clientId' => 'mock_client_id',
				'clientSecret' => 'mock_secret',
				'redirectUri' => 'none'
			],
			['cacher' => $mockCacher]
		);
        $this->assertSame($mockCacher, $provider->getCacher());
    }

    public function testConstructorSetsLogger() {
        $mockLogger = m::mock(LoggerInterface::class);
        $provider = new OauthProvider(
			[
				'clientId' => 'mock_client_id',
				'clientSecret' => 'mock_secret',
				'redirectUri' => 'none'
			],
			['logger' => $mockLogger]
		);
        $this->assertSame($mockLogger, $provider->getLogger());
    }

    public function testGetAccessTokenSmart_cached() {
    	$this->provider->getCacher()->clear();

       	$userId = rand(1000, 9999);
		$name = uniqid();
		$email = uniqid();

		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=mock_access_token&expires_in=9999');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$userResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$userResponse->shouldReceive('getBody')->andReturn(
			'{
		  "resource_owner_type": "user",
		  "resource_owner_id": "' . $userId . '",
		  "client_id": "mock_client",
		  "scopes": {
		    "mock_scope": {
		      "id": "mock_scope",
		      "description": "some mock scope"
		    }
		  },
		  "user_data": {
		    "id": ' . $userId . ',
		    "name": "' . $name . '",
		    "email": "' . $email . '",
		    "created_at": "2016-02-29 15:03:36",
		    "updated_at": "2016-03-02 12:25:40"
		  }
		}'
		);
		$userResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
		$userResponse->shouldReceive('getStatusCode')->andReturn(200);

		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(1)
			->andReturn($postResponse, $userResponse);
		$this->provider->setHttpClient($client);

		$token = $this->provider->getAccessTokenSmart('client_credentials', ['scope' => 'mock_scope']);
		$this->assertEquals('mock_access_token', $token);

		$client->shouldReceive('send')
			->times(0);  //token is not requested again from server, cached one used
		$this->provider->setHttpClient($client);

		$token = $this->provider->getAccessTokenSmart('client_credentials', ['scope' => 'mock_scope']);
		$this->assertEquals('mock_access_token', $token); //we get same token as its cached (not yet expired)
		$this->provider->getCacher()->clear();
    }


    public function testGetAccessTokenSmart_expiredNotCached() {
    	$this->provider->getCacher()->clear();

       	$userId = rand(1000, 9999);
		$name = uniqid();
		$email = uniqid();

		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=mock_access_token&expires_in=1');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$userResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$userResponse->shouldReceive('getBody')->andReturn(
			'{
		  "resource_owner_type": "user",
		  "resource_owner_id": "' . $userId . '",
		  "client_id": "mock_client",
		  "scopes": {
		    "mock_scope": {
		      "id": "mock_scope",
		      "description": "some mock scope"
		    }
		  },
		  "user_data": {
		    "id": ' . $userId . ',
		    "name": "' . $name . '",
		    "email": "' . $email . '",
		    "created_at": "2016-02-29 15:03:36",
		    "updated_at": "2016-03-02 12:25:40"
		  }
		}'
		);
		$userResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
		$userResponse->shouldReceive('getStatusCode')->andReturn(200);

		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(1)
			->andReturn($postResponse, $userResponse);
		$this->provider->setHttpClient($client);

		$token = $this->provider->getAccessTokenSmart('client_credentials', ['scope' => 'mock_scope']);
		$this->assertEquals('mock_access_token', $token);

		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=new_access_token&expires_in=9999');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(1)
			->andReturn($postResponse, $userResponse);
		$this->provider->setHttpClient($client);

		$token = $this->provider->getAccessTokenSmart('client_credentials', ['scope' => 'mock_scope']);
		$this->assertEquals('new_access_token', $token); //we got new token as old cached one is expired
		$this->provider->getCacher()->clear();
    }


    public function testGetAccessTokenSmart_dontCacheOption() {
       	$userId = rand(1000, 9999);
		$name = uniqid();
		$email = uniqid();

		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=mock_access_token&expires_in=9999');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$userResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$userResponse->shouldReceive('getBody')->andReturn(
			'{
		  "resource_owner_type": "user",
		  "resource_owner_id": "' . $userId . '",
		  "client_id": "mock_client",
		  "scopes": {
		    "mock_scope": {
		      "id": "mock_scope",
		      "description": "some mock scope"
		    }
		  },
		  "user_data": {
		    "id": ' . $userId . ',
		    "name": "' . $name . '",
		    "email": "' . $email . '",
		    "created_at": "2016-02-29 15:03:36",
		    "updated_at": "2016-03-02 12:25:40"
		  }
		}'
		);
		$userResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
		$userResponse->shouldReceive('getStatusCode')->andReturn(200);

		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(1)
			->andReturn($postResponse, $userResponse);
		$this->provider->setHttpClient($client);

		$token = $this->provider->getAccessTokenSmart('client_credentials', ['scope' => 'mock_scope']);
		$this->assertEquals('mock_access_token', $token);

		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=new_access_token&expires_in=9999');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(1)
			->andReturn($postResponse, $userResponse);
		$this->provider->setHttpClient($client);

		$token = $this->provider->getAccessTokenSmart('client_credentials', ['scope' => 'mock_scope', 'cacheOn' => false]);
		$this->assertEquals('new_access_token', $token); //we got new token as we are not using cache
		$this->provider->getCacher()->clear();
    }

    public function testClearTokenCache() {
    	$userId = rand(1000, 9999);
		$name = uniqid();
		$email = uniqid();

		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=mock_access_token&expires_in=9999');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$userResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$userResponse->shouldReceive('getBody')->andReturn(
			'{
		  "resource_owner_type": "user",
		  "resource_owner_id": "' . $userId . '",
		  "client_id": "mock_client",
		  "scopes": {
		    "mock_scope": {
		      "id": "mock_scope",
		      "description": "some mock scope"
		    }
		  },
		  "user_data": {
		    "id": ' . $userId . ',
		    "name": "' . $name . '",
		    "email": "' . $email . '",
		    "created_at": "2016-02-29 15:03:36",
		    "updated_at": "2016-03-02 12:25:40"
		  }
		}'
		);
		$userResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
		$userResponse->shouldReceive('getStatusCode')->andReturn(200);


		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=new_access_token&expires_in=9999');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(1)
			->andReturn($postResponse, $userResponse);
		$this->provider->setHttpClient($client);

		$token_1 = $this->provider->getAccessTokenSmart('client_credentials', ['scope' => 'mock_scope', 'cachePrefix' => 'another']);

		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=new_access_token&expires_in=9999');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(0) //called 0 times as token is cached, so no new calls should be made
			->andReturn($postResponse, $userResponse);
		$this->provider->setHttpClient($client);

		$token_2 = $this->provider->getAccessTokenSmart('client_credentials', ['scope' => 'mock_scope', 'cachePrefix' => 'another']);

		$this->assertEquals($token_1, $token_2); //make sure tokens are equal, as we are fetching cached one

		$this->provider->clearTokenCache('another'); //clear cache

		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=different_access_token&expires_in=9999');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(1) //called 1 times as token cache is now cleared, new request is made
			->andReturn($postResponse, $userResponse);
		$this->provider->setHttpClient($client);

		$token_3 = $this->provider->getAccessTokenSmart('client_credentials', ['scope' => 'mock_scope', 'cachePrefix' => 'another']);

		$this->assertEquals('different_access_token', $token_3);

    }


    public function testGetAccessTokenSmart_cacheSafeInterval() {
    	$userId = rand(1000, 9999);
		$name = uniqid();
		$email = uniqid();

		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=mock_access_token&expires_in=9999');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$userResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$userResponse->shouldReceive('getBody')->andReturn(
			'{
		  "resource_owner_type": "user",
		  "resource_owner_id": "' . $userId . '",
		  "client_id": "mock_client",
		  "scopes": {
		    "mock_scope": {
		      "id": "mock_scope",
		      "description": "some mock scope"
		    }
		  },
		  "user_data": {
		    "id": ' . $userId . ',
		    "name": "' . $name . '",
		    "email": "' . $email . '",
		    "created_at": "2016-02-29 15:03:36",
		    "updated_at": "2016-03-02 12:25:40"
		  }
		}'
		);
		$userResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
		$userResponse->shouldReceive('getStatusCode')->andReturn(200);


		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=token_1&expires_in=600');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(1)
			->andReturn($postResponse, $userResponse);
		$this->provider->setHttpClient($client);

		$token1 = $this->provider->getAccessTokenSmart('client_credentials', ['scope' => 'mock_scope', 'cachePrefix' => 'some', 'cacheSafeInterval' => 10]);

		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=token_2&expires_in=600');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(0) //not calling server as token not expired in less than 5 mins (cacheSafeInterval)
			->andReturn($postResponse, $userResponse);
		$this->provider->setHttpClient($client);

		$token2 = $this->provider->getAccessTokenSmart('client_credentials', ['scope' => 'mock_scope', 'cachePrefix' => 'some', 'cacheSafeInterval' => 5]);

		$postResponse = m::mock('Psr\Http\Message\ResponseInterface');
		$postResponse->shouldReceive('getBody')->andReturn('access_token=token_3&expires_in=600');
		$postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
		$postResponse->shouldReceive('getStatusCode')->andReturn(200);

		$client = m::mock('GuzzleHttp\ClientInterface');
		$client->shouldReceive('send')
			->times(1) //calling server again as token expires in less than 10 mins (cacheSafeInterval)
			->andReturn($postResponse, $userResponse);
		$this->provider->setHttpClient($client);

		$token3 = $this->provider->getAccessTokenSmart('client_credentials', ['scope' => 'mock_scope', 'cachePrefix' => 'some', 'cacheSafeInterval' => 10]);

		$this->assertEquals($token1, $token2);
		$this->assertNotEquals($token1, $token3);
    }


    public function testGetAccessTokenSmart_tokenFetchRetry() {
    	$provider = $this->getMockBuilder('GetPhoto\Oauth2\Client\Provider\OauthProvider')->setMethods(['getAccessToken'])->getMock();
    	//getAccessToken will be called 2 as first attempt fails we do second retry
    	$provider->expects($this->exactly(2))->method('getAccessToken')->will($this->returnValue(false));

    	$provider->getAccessTokenSmart('client_credentials', ['scope' => 'mock_scope', 'cachePrefix' => 'some', 'cacheOn' => false]);
    }

}
