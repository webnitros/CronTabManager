
[![Build Status](https://travis-ci.org/fotografde/oauth-client.svg?branch=master)](https://travis-ci.org/fotografde/oauth-client)

[![Coverage Status](https://coveralls.io/repos/github/fotografde/oauth-client/badge.svg?branch=master)](https://coveralls.io/github/fotografde/oauth-client?branch=master)

# Getphoto Provider for OAuth 2.0 Client

This package provides Getphoto OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```sh
$ composer require getphoto/oauth-client
```

## Usage

**! NOTE !** For common use case with caching check [Caching tokens](#caching-tokens)


Usage is the same as The League's OAuth client, using `\Getphoto\Oauth2\OauthProvider` as the provider. 
Simple frontend client app using `Getphoto\oauth-client` with different grant types implemented can be found [here](https://dev.getphoto.io/ivan.toth/oauth-client-test)

### Client credentials grant

```php
<?php

$provider = new Getphoto\Oauth2\OauthProvider([
    'clientId'                => 'testclient',
    'clientSecret'            => 'testclient'
]);


try {
    // Try to get an access token using the client credentials grant.
    $token=$provider->getAccessToken( 'client_credentials', ['scope'=>'testscope'] );
    
} catch (\Exception $e) {
    // Failed to get the access token
    exit($e->getMessage());
}

?>
```

### Password grant

```php
<?php

$provider = new Getphoto\Oauth2\OauthProvider([
    'clientId'                => 'testclient',
    'clientSecret'            => 'testclient'
]);


try {
    // Try to get an access token using the password grant.
    $token=$provider->getAccessToken( 'password', [
                    'scope'=>'testscope',
                    'username' => 'test@test.com',
                    'password' => 'password'
                ]));                    
    
} catch (\Exception $e) {
    // Failed to get the access token
    exit($e->getMessage());
}

//we can then use getResorceOwner to get user data
$data['resource_owner']=$provider->getResourceOwner($token);                
$username=$data['resource_owner']->getName();

?>
```

### Password Ftp grant

```php
<?php

$provider = new Getphoto\Oauth2\OauthProvider([
    'clientId'                => 'testclient',
    'clientSecret'            => 'testclient'
]);


try {
    // Try to get an access token using the password grant.
    $token=$provider->getAccessToken( 'password_ftp', [
                    'scope'=>'testscope',
                    'username' => 'test',
                    'password' => 'password'
                ]));                    
    
} catch (\Exception $e) {
    // Failed to get the access token
    exit($e->getMessage());
}

//we can then use getResorceOwner to get user data
$data['resource_owner']=$provider->getResourceOwner($token);                
$username=$data['resource_owner']->getName();

?>
```

### Authorization code grant

```php
<?php

$provider=new OauthProvider([
    'clientId'                => 'testclient',
    'clientSecret'            => 'testclient',
    'redirectUri'             => 'here_goes_current_url'               
]);


// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {

    // Fetch the authorization URL from the provider; this returns the
    // urlAuthorize option and generates and applies any necessary parameters
    // (e.g. state).
    $authorizationUrl = $provider->getAuthorizationUrl(['scope' => 'testscope']);

    // Get the state generated for you and store it to the session.
    $_SESSION['oauth2state'] = $provider->getState();

    // Redirect the user to the authorization URL.
    header('Location: ' . $authorizationUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    // State is invalid, possible CSRF attack in progress
    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    try {
        // Try to get an access token using the authorization code grant.
        $token=$provider->getAccessToken( 'authorization_code', [
            'scope' => 'testscope',
            'code'  => $_GET['code']
        ]);

    } catch (\Exception $e) {
        // Failed to get the access token
        exit('ERROR: '.$e->getMessage());
    }
}

//we can then use getResorceOwner to get user data
$data['resource_owner']=$provider->getResourceOwner($token);                
$username=$data['resource_owner']->getName();

?>
```

For more detailed description about this grants and use cases please read core package [documentation](https://github.com/thephpleague/oauth2-client).

### Getting the resource owner data
There is a convinient way to get resource owner data (user data) using `ResourceOwner` class:
```php
<?php

$resource_owner=$provider->getResourceOwner($token);                
$user_name=$resource_owner->getName();
$user_email=$resource_owner->getEmail();
$user_id=$resource_owner->getId();
$user_data=$resource_owner->getUserData(); //gets related user data
/*
 [
   "id" => 7053
   "name" => "tre"
   "email" => "test@test.com"
   "created_at" => null
   "updated_at" => "2017-05-15 10:03:10"
   "core_user_id" => 20128
   "photographer_id" => 47911
 ]
*/

$user_scopes=$resource_owner->getScopes(); //get scopes token hass access to
/*
 [
   "payment.settings.read" => [
     "id" => "payment.settings.read"
     "description" => "Some nice description"
   ]
 ]
*/
?>
```



### Caching Tokens
From several use cases we've seen that caching token logic is common and really similar. This is why this package now provides convenient method for caching tokens - `getAccessTokenSmart` (which also does one additional fallback call if token fetch fails and loggs errors)

#### OauthProvider Constructor options
```php
<?php
public function __construct(array $options = [], array $collaborators = [])
?>
```

Via constructor you can set new options:
- `cacheOn` bool - wheather to use cache or not in getAccessTokenSmart (default is true)
- `cacheSafeInterval` float - how many minutes before expiration to renew token (default is 5)
- `cachePrefix` string - important to set if using provider across same system but different use case (default is '')

And also additional collaborators:
- `logger` - instance of a logging class that implements [LoggerInterface](/src/Log/LoggerInterface.php) - default is simple file logger [Logger](/src/Log/Logger.php)
- `cacher` - instance of a caching class that implements [CacherInterface](/src/Cache/CacherInterface.php) - default is simple file cacher [Cacher](/src/Cache/Cacher.php)

#### getAccessTokenSmart method
Same args as in `getAccessToken` with additional options so you can override options set in constructor (just for this call) - `cacheOn`, `cacheSafeInterval`, `cachePrefix`.

#### clearTokenCache method
```php
<?php
public function clearTokenCache($prefix = '')
?>
```
Use this method when you want to forget cached token. With `$prefix` (for which to clear tokens) param you can override constructor setting for this call.

#### Example usage of getAccessTokenSmart:
Here is example usage from our core system which calls oauth server to sync user changes:

**1. Constructing provider:**
```php
<?php
$this->oauthProvider = new OauthProvider(
    [
        'clientId' => 'some_clinet',
        'clientSecret' => 'some_secret',
        'cachePrefix' => 'userApi'
    ],
    [
        'cacher' => new OauthCakeCacher(), //implemented using native cake cache
        'logger' => new OauthCakeLogger()  //implemented using native cake log
    ]
);
?>
```

**2. Getting token:**
```php
<?php
$token = $this->oauthProvider->getAccessTokenSmart('client_credentials', [
    'scope' => 'some_scope'
]);
?>
```

**3. Calling protected API and clearing token cache if invalid token**
```php
<?php
... //some call to protected API with your token goes here
$response = $request->send();
...

//clear token if invalid
if ($response->getStatusCode() == 403 || $response->getStatusCode() == 401) {
    //forget invalid token
    $this->oauthProvider->clearTokenCache();
}
?>
```

**NOTE:** For example of session implementation of CacherInterface check Oauth lib in core system. And usage in Jobs plugin ApiComponent



## Logout
There is a logout method now to expire the token, you should implement it in your logout flow to save some resources on our authentication server:
```php

$this->oauthProvider->expireAccessToken("g2WvRwXDQrIEmi0Qkcs0Qt11ch4AbkW2Yakh8BqI");
```


## Testing

```sh
$ ./vendor/bin/phpunit vendor/getphoto/oauth-client
```
