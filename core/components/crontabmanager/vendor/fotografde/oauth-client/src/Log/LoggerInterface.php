<?php
/**
 * Logger interface
 */

namespace GetPhoto\Oauth2\Client\Log;

interface LoggerInterface {

	/**
	 * Write to log
	 *
	 * @param  string $type
	 * @param  string $message
	 * @return bool success
	 */
	public static function write($type, $message);
}
