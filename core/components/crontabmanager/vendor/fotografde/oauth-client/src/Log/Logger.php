<?php
/**
 * Simple file log implementation of LoggerInterface
 */

namespace GetPhoto\Oauth2\Client\Log;
use GetPhoto\Oauth2\Client\Log\LoggerInterface;

class Logger implements  LoggerInterface {

	public static function write($type, $message) {
		return (bool)file_put_contents(sys_get_temp_dir() ."/" . $type . ".log", date('Y-m-d H:i:s') . "\t" . $message. PHP_EOL , FILE_APPEND | LOCK_EX);
	}
}
