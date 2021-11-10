<?php

namespace GetPhoto\Oauth2\Client\Grant;

/**
 *  Represents a password ftp grant
 *  Special grant required for ftp_users
 *  implementation of new grant based on: https://github.com/TheNetworg/oauth2-azure
 *
 * @author navihtot ivan.toth@fotograf.de
 */
class PasswordFtp extends \League\OAuth2\Client\Grant\AbstractGrant {
	/**
	 * @inheritdoc
	 */
	protected function getName() {
		return 'password_ftp';
	}

	/**
	 * @inheritdoc
	 */
	protected function getRequiredRequestParameters() {
		return [
			'username',
			'password',
		];
	}
}
