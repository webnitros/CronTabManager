<?php

namespace GetPhoto\Oauth2\Client\Grant;

/**
 *  Impersonate grant
 *  Grant for trusted client to impersonate user
 *
 * @author navihtot ivan.toth@fotograf.de
 */
class Impersonate extends \League\OAuth2\Client\Grant\AbstractGrant {
	/**
	 * @inheritdoc
	 */
	protected function getName() {
		return 'impersonate';
	}

	/**
	 * @inheritdoc
	 */
	protected function getRequiredRequestParameters() {
		return [
			'username',
		];
	}
}
