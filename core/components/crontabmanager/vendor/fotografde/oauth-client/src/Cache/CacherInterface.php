<?php
/**
 * Cacher interface
 */
namespace GetPhoto\Oauth2\Client\Cache;

interface CacherInterface {

	/**
	 * Set cache prefix
	 *
	 * @param  string $prefix
	 */
	public function setPrefix($prefix);

	/**
	 * Get cache prefix
	 *
	 * @return string
	 */
	public function getPrefix();

	/**
	 * Write to cache
	 *
	 * @param string $key Identifier for the data
	 * @param mixed $value Data to be cached - anything except a resource
	 * @return bool True if the data was successfully cached, false on failure
	 */
	public function write($key, $value);

	/**
	 * Read from cache
	 *
	 * @param string $key Identifier for the data
	 * @return mixed The cached data, or false if the data doesn't exist, has expired, or if there was an error fetching it
	 */
	public function read($key);

	/**
	 * Delete from cache
	 *
	 * @param string $key Identifier for the data
	 * @return bool success
	 */
	public function delete($key);

	/**
	 * Clear all cached vars
	 *
	 * @return bool success
	 */
	public function clear();
}
