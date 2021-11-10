<?php

use Mockery as m;
use GetPhoto\Oauth2\Client\Cache\Cacher as Cacher;

class CacherTest extends PHPUnit_Framework_TestCase {

	protected function setUp() {
		$this->cacher = new Cacher;
	}

	public function tearDown() {
		unset($this->cacher);
		parent::tearDown();
	}

	public function testWriteReadDelete() {
		$this->assertTrue($this->cacher->write('test', 'someValue'));
		$this->assertEquals('someValue', $this->cacher->read('test'));
		$this->assertTrue($this->cacher->delete('test'));
		$this->assertEmpty($this->cacher->read('test'));
	}

	public function testClear() {
		$this->assertTrue($this->cacher->write('test', 'someValue'));
		$this->assertTrue($this->cacher->clear());
		$this->assertEmpty($this->cacher->read('test'));
	}

}
