<?php
/**
 * Simple file cache implementation of CacherInterface
 */
namespace GetPhoto\Oauth2\Client\Cache;
use GetPhoto\Oauth2\Client\Cache\CacherInterface;
use GetPhoto\Oauth2\Client\Cache\CacherPrefixTrait;
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\File;

class Cacher implements CacherInterface {

	use CacherPrefixTrait;

	protected $cacheDir = '';

	private function cache() {
		$this->cacheDir = sys_get_temp_dir() . '/oauth-client/';
		$adapter = new File($this->cacheDir);
		$adapter->setOption('ttl', 4100);
		return new Cache($adapter);
	}

	public function write($key, $value){
		$this->cache()->set($this->getPrefix() . $key, $value);
		return true;
	}

	public function read($key){
		return $this->cache()->get($this->getPrefix() . $key);
	}

	public function delete($key){
		$this->cache()->delete($this->getPrefix() . $key);
		return true;
	}

	public function clear() {
		$directory = $this->cacheDir;
		$files = glob($directory . "__" . $this->getPrefix() . '*.*');
		foreach ($files as $file) {
			unlink($file);
		}
		return true;
	}

}
