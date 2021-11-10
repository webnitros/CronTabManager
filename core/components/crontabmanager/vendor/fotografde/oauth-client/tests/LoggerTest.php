<?php

use GetPhoto\Oauth2\Client\Log\Logger as Logger;

class LoggerTest extends PHPUnit_Framework_TestCase {

	protected function setUp() {
		$this->logger = new Logger;
	}

	public function tearDown() {
		unset($this->logger);
		parent::tearDown();
	}

	public function testWrite() {
		$type = 'someType';
		$this->assertTrue($this->logger->write($type, 'someMsg'));
		$this->assertTrue(file_exists(sys_get_temp_dir() ."/" . $type . ".log"));
		$content = file_get_contents(sys_get_temp_dir() ."/" . $type . ".log");
		$this->assertTrue(strpos($content, 'someMsg') !== false);
	}
}
