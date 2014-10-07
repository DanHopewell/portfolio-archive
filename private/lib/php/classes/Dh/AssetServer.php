<?php

require_once('loader.php');

loadFunc('php_get_contents');
loadFunc('modified_since_header');

class Dh_AssetServer
{

	public $cacheDir = 'cache/';
	public $methods = array('original' => 'o', 'minified' => 'min', 'modified' => 'mod');
	public $cacheBuster = "/^v\-([\dA-Z]{6})$/i";

	private $_request; // server request uri; /script/dir/min/path/to/subdir/file1_file2.js
	private $_requestMethod; // original file: 'o' minified: 'min' modified time: 'mod'; min
	private $_requestPath; // relative path stripped of request root and method; path/to/subdir/
	private $_requestBasename; // full filename from request; file1_file2.js
	private $_ext; // extension of file; js
	private $_files = array(); // full paths to files on server; js/path/to/subdir/file1.js
	private $_cacheFile; // full path to cache file; cache/min/path/to/subdir/file1_file2.js
	private $_modTime = 0; // last modified time of file(s)
	private $_version;



	public function __construct()
	{
		$this->_request = $_SERVER['REQUEST_URI'];
		$scriptPath = $_SERVER['SCRIPT_NAME'];
		$rootPath = substr($scriptPath, 0, -1 * strlen(basename($scriptPath)));

		$reqParts = pathinfo($this->_request);
		$this->_ext = $reqParts['extension'];

		$b = basename($this->_request, '.' . $this->_ext);
		$this->_requestBasename = $b . '.' . $this->_ext;
		$this->_files = explode("_", $b);
		if ( (count($this->_files) > 1)
		&& (preg_match($this->cacheBuster, $this->_files[count($this->_files)-1], $m)) ) {
			$this->_version = array_pop($this->_files);
		}

		$f = substr($this->_request, 0, -1 * strlen($this->_requestBasename));
		$f = substr($f, strlen($rootPath));
		$a = explode('/', $f);
		$this->_requestMethod = array_shift($a);
		if (!in_array($this->_requestMethod, $this->methods)) {
			$this->_404();
		}
		$this->_requestPath = implode('/', $a);
	}


	public function serve()
	{
		foreach ($this->_files as $key => $file) {
			$fname = $file . '.' . $this->_ext;
			$fpath = $this->_ext . '/' . $this->_requestPath . $fname;
			if (!file_exists($fpath)) {
				$this->_404();
			}
			$this->_files[$key] = $fpath;
			$this->_modTime = max(filemtime($fpath), $this->_modTime);
		}

		if ($this->_requestMethod == $this->methods['modified']) {
			header("Content-Type:text/plain; charset=utf-8");
			echo $this->_modTime;
			exit;
		}

		$this->_cacheFile = $this->cacheDir . $this->_requestMethod . '/' . $this->_requestPath . $this->_requestBasename;

		
		$output = $this->_loadFiles();
		if ($this->_requestMethod == $this->methods['minified']) {
			$output = $this->_minify($output);
		}

		$this->_serveFile($output);
		$this->_saveCache($output);
	}



	private function _serveFile($output)
	{
		$mimeMap = array(
			'js' => 'application/javascript',
			'css' => 'text/css'
		);
		$contentType = 'Content-type: ' . $mimeMap[$this->_ext] . '; charset=utf-8';

	    // Checking if the client is validating his cache and if it is current
		if ( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $this->_modTime) ) {
			// Client's cache IS current, so we just respond '304 Not Modified'.
			header( 'Last-Modified: ' . gmdate('D, d M Y H:i:s', $this->_modTime) . ' GMT', true, 304 );
		} else {
			// File not cached or cache outdated, we respond '200 OK' and output
			header( 'Last-Modified: ' . gmdate('D, d M Y H:i:s', $this->_modTime) . ' GMT', true, 200 );
			header( 'Content-Length: ' . strlen($output) );
			header( $contentType );
			echo $output;
		}
	}


	private function _404()
	{
		header('HTTP/1.0 404 Not Found');
		exit;
	}


	private function _saveCache($output)
	{
		$pathparts = pathinfo($this->_cacheFile);
		$dir = $pathparts['dirname'];
		if (!is_dir($dir)) {
			if ( @mkdir($dir, 0711, true) === false ) {
				return false;
			}
		}
		if ( @file_put_contents($this->_cacheFile, $output, LOCK_EX) === false ) {
			return false;
		}
		@chmod($this->_cacheFile, 0644);
		@touch($this->_cacheFile, $this->_modTime);
	}


	private function _loadFiles()
	{
		$str = '';

		foreach ($this->_files as $key => $file) {

			$str .= php_get_contents($file);
			if ( ($key + 1) < count($this->_files) ) {
				if ($this->_ext == 'js') {
					$str .= ';';
				}
				$str .= PHP_EOL;
			}
		}
		return $str;
	}


	private function _minify($str)
	{
		switch ($this->_ext) {
			case 'js':
				$m = new Dh_ClosureCompiler();
				$newstr = $m->compress($str); 
				break;
			case 'css':
				$m = new CSSmin();
				$newstr = $m->run($str, 1500);
				break;
		}
		if ($newstr) {
			$str = $newstr;
		}
		return $str;
	}




}





?>
