<?php
// +------------------------------------------------------------------------+
// | PHP Melody ( www.phpsugar.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.phpsugar.com or if you have received
// | this software from someone who is not a representative of
// | PHPSUGAR, you are involved in an illegal activity.
// | ---
// | In such case, please contact: support@phpsugar.com.
// +------------------------------------------------------------------------+
// | Developed by: PHPSUGAR (www.phpsugar.com) / support@phpsugar.com
// | Copyright: (c) 2004-2016 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

// Not sure how to configure? Please read the Installation Manual PDF
// +------------------------------------------------------------------------+


/**
 * A simple php object caching feature 
 * 
 * The main purpose is to save repetitive trips to mysql during a 
 * reques/php script execution. Data received from mysql (or other source)
 * can be stored and retrieved by using a unique key. 
 * 
 * Not the same as memcache, APC, etc.
 * 
 * @since 2.6
 */
class PhpmelodyCache
{
	private $cache = array(); 		// holds the cache
	private $cache_hits = 0; 
	public $cache_misses = 0;
	private $suspended = false;
	
	public function __construct() 
	{
		$this->cache = array();
		
		return true;
	}
	
	public function __destruct() 
	{
		return true;
	}
	
	/**
	 * Adds data to the cache if it doesn't already exist.
	 * 
	 * @param string $key
	 * @param mixed $data
	 * @return bool false if cache key already exists or additions are suspended, true on success
	 */
	public function add($key, $data)
	{
		if ($this->_exists($key) || $this->cache_is_suspended())
		{
			return false;
		}
		
		if (is_object($data))
		{
			$data = clone $data;
		}
		
		$this->cache[$key] = $data;
		
		return true;
	}
	
	/**
	 * Get the cache contents.
	 * 
	 * @param int|string $key
	 * @return bool|mixed false on failure, (mixed) data on success
	 */
	public function get($key)
	{
		if ($this->_exists($key))
		{
			$this->cache_hits++;
			if (is_object($this->cache[$key]))
			{
				return clone $this->cache[$key];
			}
			else
			{
				return $this->cache[$key];
			}
		}
		
		// key not found; count as a cache miss
		$this->cache_misses++;
		
		return false;
	}
	
	/**
	 * Delete a particular cached item.
	 * 
	 * @param int|string $key
	 * @return bool false if item not found, true on success
	 */
	public function delete($key)
	{
		if ( ! $this->_exists($key))
		{
			return false;
		}
		
		unset($this->cache[$key]);
		
		return true;
	}
	
	/**
	 * Replaces the contents in the cache for a particular object.
	 * If the item doesn't exist in the cache, it will create it. 
	 * 
	 * @param int|string $key
	 * @param object $data
	 * @return bool false if item not found, true on success
	 */
	function replace($key, $data) 
	{
		if ( ! $this->_exists($key))
		{
			return false;
		}
		
		$this->delete($key);
		
		return $this->add($key, $data);
	}
	
	/**
	 * Clears the entire cache of any data stored.
	 * 
	 * @return bool true
	 */
	public function flush()
	{
		$this->cache = array();

		return true;
	}
	
	public function stats() 
	{
		echo "<pre>";
		echo "<strong>Cache Hits:</strong> {$this->cache_hits}<br />";
		echo "<strong>Cache Misses:</strong> {$this->cache_misses}<br />";
		echo number_format( strlen( serialize( $cache ) ) / 1024, 2 ) . 'kb';
		
		// print_r($this->cache);
		
		echo "</pre>";
	}
	
	/**
	 * Checks if the key already exists.
	 * 
	 * @param int|string $key
	 * @return bool true if it exists, false if not
	 */
	protected function _exists($key) 
	{
		return (isset( $this->cache[$key] ) || array_key_exists($key, $this->cache));
	}
	
	public function suspend_cache_additions()
	{
		$this->suspended = true;
		
		return true;
	}
	
	public function resume_cache_additions()
	{
		$this->suspended = false;
		
		return true;
	}
	
	public function cache_is_suspended()
	{
		return $this->suspended;
	}
}
