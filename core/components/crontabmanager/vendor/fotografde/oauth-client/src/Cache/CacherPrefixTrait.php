<?php
/**
 * Cacher prefix trait
 */
namespace GetPhoto\Oauth2\Client\Cache;

trait CacherPrefixTrait {

	protected $prefix = '';

	public function setPrefix($prefix) {
		if (substr($prefix, -1) != '_') {
			$prefix .= '_';
		}
		$this->prefix = $prefix;
	}

	public function getPrefix() {
		return $this->prefix;
	}

}
