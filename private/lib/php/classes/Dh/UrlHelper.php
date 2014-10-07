<?php

require_once('loader.php');

loadFunc('str_freplace');

class Dh_UrlHelper
{

	public $imgAccess = 'http';
	public $imgWebPath = 'http://img.danhopewell.com/';
	public $imgWebDir = 'i/';
	public $imgWebDataDir = 'data/';
	public $imgWebModDir = 'mod/';
	public $imgServerPath = '';

	public $jsAccess = 'http';
	public $jsWebPath = 'http://assets.danhopewell.com/';
	public $jsWebDir = 'o/';
	public $jsWebMinDir = 'min/';
	public $jsWebModDir = 'mod/';
	public $jsServerPath = '';

	public $cssAccess = 'http';
	public $cssWebPath = 'http://assets.danhopewell.com/';
	public $cssWebDir = 'o/';
	public $cssWebMinDir = 'min/';
	public $cssWebModDir = 'mod/';
	public $cssServerPath = '';

	public $fontAccess = 'local';
	public $fontWebPath = '';
	public $fontServerPath = '';


	private $_c = array();
	private $_cacheBusterPrefix = 'v-';



	public function __construct($optionsArray = array())
	{

	}


	public function img()
	{
		return $this->_output(func_get_args(), img);
	}


	public function js()
	{
		return $this->_output(func_get_args(), js);
	}


	public function css()
	{
		return $this->_output(func_get_args(), css);
	}


	public function font()
	{
		return $this->_output(func_get_args(), font);
	}



	private function _output($opts, $type)
	{
		$file = ltrim(array_shift($opts), '/');
		$data = in_array('data', $opts);
		$min = in_array('min', $opts);
		$cache = !in_array('nocache', $opts);
		$str = in_array('str', $opts);
		
		$a = $type . 'Access';
		$w = $type . 'WebPath';
		$d = $type . 'WebDir';
		$da = $type . 'WebDataDir';
		$mn = $type . 'WebMinDir';
		$md = $type . 'WebModDir';
		$s = $type . 'ServerPath';
		
		$this->_c['access'] = $this->$a;
		$this->_c['webRoot'] = $this->$w;
		$this->_c['webDir'] = $this->$d;
		if ($this->$da) {
			$this->_c['webDataDir'] = $this->$da;
		}
		if ($this->$mn) {
			$this->_c['webMinDir'] = $this->$mn;
		}
		$this->_c['webModDir'] = $this->$md;
		$this->_c['serverRoot'] = $this->$s;

		if ($data) {
			$url = $this->_dataUri($file);
		} else {
			if ($cache) {
				$file = $this->_cacheBuster($file);
			}

			if ($min) {
				$file = $this->_c['webMinDir'] . $file;
			} else {
				$file = $this->_c['webDir'] . $file;
			}

			$url = $this->_c['webRoot'] . $file;
		}

		$this->_c = array();
		if ($str) {
			return $url;
		} else {
			echo $url;
		}
		
	}


	private function _cacheBuster($file)
	{
		if ( $time = $this->_modTime($file) ) {
			$time = str_pad( substr( base_convert($time, 10, 36) ,0,6) , 6, "0", STR_PAD_LEFT);
			$slug = '_' . $this->_cacheBusterPrefix . $time . '.';
			return str_freplace('.', $slug, $file);
		} else {
			return $file;
		}
	}


	private function _modTime($file)
	{
		switch ($this->_c['access']) {
			case 'http':
				$f = $this->_c['webRoot'] . $this->_c['webModDir'] . $file;
				return $this->_curl($f);
				break;
			
			case 'local':
				$f = $this->_c['serverRoot'] . $file;
				return filemtime($f);
				break;

			default:
				break;
		}
	}


	private function _dataUri($file)
	{
		switch ($this->_c['access']) {
			case 'http':
				$f = $this->_c['webRoot'] . $this->_c['webDataDir'] . $file;
				return $this->_curl($f);
				break;
			
			case 'local':
				$f = $this->c['serverRoot'] . $file;
				$mime = function_exists('mime_content_type') ? mime_content_type($f) : '';
				return 'data:' . $mime . ';base64,' . base64_encode( file_get_contents($f) );
				break;

			default:
				break;
		}
	}


	private function _curl($f)
	{
		$ch = curl_init($f);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}



}

?>
