<?php

require_once('loader.php');
loadFunc('file_get_contents_flock');

class Dh_OutputCache
{

	private $_cacheOn = true;
	private $_defaultLifetime = 3600;  // 1 hour in seconds
	private $_includeQueryString = false; // include query string in cache key??
	private $_cacheDir = 'cache/'; // defaults to same directory as calling script
	private $_cacheLogLocation;  // set in constructor to 'cachelog.txt' in cache directory
	private $_errorLogLocation; // set in constructor to 'logs/dh_cacheerrors.txt' in web root
	private $_verifyReads = true; // check cache read against crc/hash??
	private $_verifyWrites = true; // read back cache write and check against data??
	private $_hashType = 'crc32';  // hash type for read verification: crc32, md5 or sha256
	private $_hashLength;  // set in constructor based on hashType
	
	private $_configurable = array( // list of user-configurable options
		'cacheOn',
		'defaultLifetime',
		'includeQueryString',
		'cacheDir',
		'verifyReads',
		'verifyWrites',
		'hashType'
	);

	private $_currentCacheKey;  // set in start(), unset upon cache completion
	private $_currentCacheLocation;  // set in start(), unset upon cache completion



	public function __construct($optionsArray = array())
	{
		if (defined('DH_CACHE_ON')) $this->_cacheOn = DH_CACHE_ON;

		foreach($optionsArray as $key => $value) {
			if (in_array($key, $this->_configurable)) {
				$var = '_'.$key;
				$this->$var = $value;
			}
		}

		$this->_errorLogLocation = $_SERVER['DOCUMENT_ROOT'] . 'logs/dh_cacheerrors.txt';

		if (substr($this->_cacheDir, -1) != '/') $this->_cacheDir .= '/';

		if (!is_dir($this->_cacheDir)) {
			if (! @mkdir($this->_cacheDir, 0700)) {
				$this->_cacheOn = false;
				$this->_logError("Cache Failed. Can't find/create cache directory: " . $this->_cacheDir);
			}
		}

		if (!is_writable($this->_cacheDir)) {
			$this->_cacheOn = false;
			$this->_logError("Cache Failed. Cache directory is not writable: " . $this->_cacheDir);
		}

		$this->_cacheLogLocation = $this->_cacheDir . 'cachelog.txt';

		if ( !file_exists($this->_cacheLogLocation) ) {
			if ( !$this->_saveLog(array()) ) {
				$this->_cacheOn = false;
				$this->_logError("Cache Failed. Can't find/create cache log file: " . $this->_cacheLogLocation);
			} else {			
				chmod($this->_cacheLogLocation, 0600);
			}
		}

		if (!is_writable($this->_cacheLogLocation)) {
			$this->_cacheOn = false;
			$this->_logError("Cache Failed. Cache log file is not writable: " . $this->_cacheLogLocation);
		}

		switch ($this->_hashType) {
			case 'crc23':
				$this->_hashLength = 8;
			case 'md5':
				$this->_hashLength = 32;
			case 'sha256':
				$this->_hashLength = 64;
		}

	}



	public function start($key = NULL, $lifetime = NULL, $abs = false)
	{
		if ($this->_cacheOn) {

			$this->_currentCacheKey = $this->_setKey($key, $abs);

			// set current cache file location and lifetime
			$this->_currentCacheLocation = $this->_cacheDir . $this->_currentCacheKey;
			if ($lifetime == NULL) $lifetime = $this->_defaultLifetime;

			// if cache exists, is fresh, and hasn't been set to 'delete'
			if ( (file_exists($this->_currentCacheLocation))
			&& (time() - filemtime($this->_currentCacheLocation) < $lifetime)
			&& ($this->_checkLog()) ) { 
				// attempt to read
				if ( is_bool($data = $this->_getCache()) === false  || $data === true ) {
					// output, clean up, return true
					echo $data;
					$this->_unsetter();
					return true;
				}
			}

			// otherwise: start output buffer
			ob_start();
		}

		return false;

	}



	public function end()
	{
		if ($this->_cacheOn) {
			$data = ob_get_contents();
			ob_end_flush();
			$res = $this->_saveCache($data);
			$this->_unsetter();
			return $res;
		} else {
			return false;
		}

	}



	private function _getCache()
	{
		if ( is_bool($data = @file_get_contents_flock($this->_currentCacheLocation)) === true && $data === false) {
			$this->_logError("Can't read cache file: " . $this->_currentCacheLocation);
			return false;
		}

		if ($this->_verifyReads) {

			// separate error detection hash from data (format: hash.data)
			$controlHash = substr($data, 0, $this->_hashlength);
			$data = substr($data, $this->_hashlength);

			$dataHash = $this->_hash($data);

			if ($controlHash != $dataHash) {
				$this->_deleteCurrentCache();
				return false;
			}
		}

		return $data;

	}



	private function _saveCache($data)
	{
		if ($this->_verifyReads) {
			$hash = $this->_hash($data);
			$data = $hash . $data;
		}

		if ( @file_put_contents($this->_currentCacheLocation, $data, LOCK_EX) === false ) {
			$this->_logError("Can't write cache file: " . $this->_currentCacheLocation);
			return false;
		}

		chmod($this->_currentCacheLocation, 0600);

		if ($this->_verifyWrites) {
			if ( is_bool($readdata = $this->_getCache()) === true  && $readdata === false ) {
				return false;
			}
			if ( ($readdata != $data) ) {
				$this->_deleteCurrentCache();
				return false;
			}
		}

		$this->_saveCacheToLog();

		return true;

	}



	private function _setKey($key, $abs)
	{
		if (isset($key)) {
			if ($abs) {
				return $this->_encodeKey($key);
			} else {
				if ($this->_includeQueryString) {
					return $this->_encodeKey($_SERVER['REQUEST_URI'] . '##' . $key);
				} else {
					return $this->_encodeKey(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH) . '##' . $key);
				}
			}
		} else {
			if ($this->_includeQueryString) {
					return $this->_encodeKey($_SERVER['REQUEST_URI']);
				} else {
					return $this->_encodeKey(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH));
				}
		}

	}



	private function _encodeKey($key)
	{
		return base64_encode($key);

	}



	private function _hash($data)
	{
		switch ($this->_hashType) {
			case 'crc23':
				return sprintf("%08x", crc32($data));
			case 'md5':
				return md5($data);
			case 'sha256':
				return hash('sha256', $data);
		}

	}



	private function _loadLog()
	{
		if ( is_bool($logfile = @file_get_contents_flock($this->_cacheLogLocation)) === true  && $logfile === false ) {
			// if result is boolean and is false
			$this->_logError("Can't read cache log file: " . $this->_cacheLogLocation);
			return false;
		} else {
			// if result is boolean (isn't false)
			$logarray = unserialize($logfile);
			if ($logarray == '') $logarray = array();
			return $logarray;
		}
	}



	private function _saveLog($logarray)
	{
		$logfile = serialize($logarray);
		if ( @file_put_contents($this->_cacheLogLocation, $logfile, LOCK_EX) === false ) {
			$this->_logError("Can't write cache log file: " . $this->_cacheLogLocation);
			return false;
		} else {
			return true;
		}

	}



	private function _checkLog()
	{
		if ( is_bool($logarray = $this->_loadLog()) === false || $logarray === true ) {
			if ( isset($logarray[$this->_currentCacheKey]) ) {
				return $logarray[$this->_currentCacheKey];
			}
		}
		return false;

	}



	private function _saveCacheToLog()
	{
		if ( is_bool($logarray = $this->_loadLog()) === false || $logarray === true ) {
			$logarray[$this->_currentCacheKey] = 1;
			if ($this->_saveLog($logarray)) {
				return true;
			}
		}

		return false;

	}



	private function _deleteCacheByKey($key)
	{
		if ( is_bool($logarray = $this->_loadLog()) === false || $logarray === true ) {
			$logarray[$key] = 0;
			if ($this->_saveLog($logarray)) {
				return true;
			}
		}

		return false;

	}



	private function _deleteCurrentCache()
	{
		return $this->_deleteCacheByKey($this->_currentCacheKey);

	}



	public function deleteCache($key = NULL, $abs = NULL)
	{
		$cacheKey = $this->_setKey($key, $abs);
		return $this->_deleteCacheByKey($cacheKey);

	}



	public function deleteAll()
	{
		$logarray = array();

		if ($this->_saveLog($logarray)) {
			return true;
		} else {
			return false;
		}

	}



	public function cleanCache()
	{
		if ( is_bool($logarray = $this->_loadLog()) === false || $logarray === true ) {
			$newArray = array();
			foreach ($logarray as $key => $value) {
				if ($value == 1) $newArray[$key] = $value;
			}
			if ($this->_saveLog($newArray)) {
				return true;
			}
		}

		return false;

	}



	private function _logError($error)
	{
		$errortext = PHP_EOL . time() . ' ' . $_SERVER['SCRIPT_NAME'] . $error;
		@file_put_contents($this->_errorLogLocation, $errortext, FILE_APPEND|LOCK_EX);

	}



	private function _unsetter()
	{
		if (isset($this->_currentCacheKey)) unset($this->_currentCacheKey);
		if (isset($this->_currentCacheLocation)) unset($this->_currentCacheLocation);

	}


}

?>