<?php

require_once('loader.php');

loadFunc('str_ends_with');
loadFunc('str_lreplace');


class Dh_ImageServer
{

	public $imgDir = 'img/';
	public $cacheDir = 'cache/';
	public $tempDir = 'temp/';
	public $tn = '-tn';
	public $methods = array('image' => 'i', 'data' => 'data', 'modified' => 'mod');
	public $options = array();
	public $progressive = TRUE;
	public $jpegCompression = 85;
	public $usm = array('radius' => 0, 'sigma' => 0.4, 'amount' => 1.5, 'threshold' => 0);
	public $pngOpt = '/usr/bin/optipng -o3 {file}';

	protected $_request; // server request uri; /script/dir/i/path/to/subdir/image_arg1_arg2.jpg.png
	protected $_requestMethod; // image wanted? 'i' modified time wanted? 'mod'; i
	protected $_requestPath; // relative path stripped of request root and method; path/to/subdir/
	protected $_requestBasename; // full filename from request; image_arg1_arg2.jpg.png
	protected $_inType; // extension of image on server; jpg
	protected $_outType; // extension of output image type; png
	protected $_file; // full path to file on server; img/path/to/subdir/image.jpg
	protected $_cacheFile; // full path to cache file; cache/i/path/to/subdir/image_arg1_arg2.jpg.png
	protected $_modTime; // last modified time of file

	protected $_args = array();
	protected $_params = array();
	protected $_img = '';



	public function __construct()
	{
		$this->_request = $_SERVER['REQUEST_URI'];
		$scriptPath = $_SERVER['SCRIPT_NAME'];
		$rootPath = substr($scriptPath, 0, -1 * strlen(basename($scriptPath)));

		$a = explode('.',$this->_request);
		array_shift($a);
		$this->_inType = $a[0];
		$this->_outType = $a[count($a)-1];
		if ($this->_outType == 'jpeg') {
			$this->_outType = 'jpg';
		}

		if ( ($this->_outType == 'svg')
		&& ($this->_inType != 'svg') ) {
			$this->_404();
		}

		$this->_requestBasename = basename($this->_request);
		$b =  substr( $this->_requestBasename, 0, strpos($this->_requestBasename,'.') );
		$this->_args = explode("_", $b);

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
		$imgName = array_shift($this->_args) . '.' . $this->_inType;
		$this->_file = $this->imgDir . $this->_requestPath . $imgName;

		$this->_checkFile();

		if ($this->_requestMethod == $this->methods['modified']) {
			header("Content-Type:text/plain; charset=utf-8");
			echo $this->_modTime;
			exit;
		}

		if ( ($this->_outType == 'gif')
		&& ($this->_inType == 'gif') ) {
			$this->_passThru();
		}

		if ( ($this->_outType == 'tif')
		&& ($this->_inType == 'tif') ) {
			$this->_passThru();
		}

		$this->_parseArgs();

		$this->_img = new Dh_ImageServer_Image($this->_file);
		$this->_process($this->_inType);

		$outFunc = '_' . $this->_outType . 'Out';
		$this->$outFunc();

		$this->_out($this->_img->data);

	}



	protected function _out($data)
	{
		if ($this->_requestMethod == $this->methods['data']) {
			$output = $this->_dataUri($data);
			header("Content-Type:text/plain; charset=utf-8");
			echo $output;
			$this->_saveCache($output);
		} elseif ($this->_requestMethod == $this->methods['image']) {
			$this->_serveImage($data);
			$this->_saveCache($data);
		}
		exit;

	}


	protected function _passThru()
	{
		$data = file_get_contents($this->_file);
		$this->_out($data);
	}


	protected function _serveImage($data)
	{
		$contentType = 'Content-type: ' . $this->_mimeType();

	    // Checking if the client is validating his cache and if it is current.
		if ( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $this->_modTime) ) {
			// Client's cache is current, respond '304 Not Modified'
			header( 'Last-Modified: ' . gmdate('D, d M Y H:i:s', $this->_modTime) . ' GMT', true, 304 );
		} else {
			// Image not cached or cache outdated, respond '200 OK' and output image
			header( 'Last-Modified: ' . gmdate('D, d M Y H:i:s', $this->_modTime) . ' GMT', true, 200 );
			header( 'Content-Length: ' . strlen($data) );
			header( $contentType );
			echo $data;
		}

	}


	protected function _saveCache($data)
	{
		$this->_cacheFile = $this->cacheDir . $this->_requestMethod . '/' . $this->_requestPath . $this->_requestBasename;
		if ($this->_requestMethod == $this->methods['data']) {
			$this->_cacheFile .= '.txt';
		}
		$pathparts = pathinfo($this->_cacheFile);
		$dir = $pathparts['dirname'];
		if (!is_dir($dir)) {
			if ( @mkdir($dir, 0711, true) === false ) {
				return false;
			}
		}
		if ( @file_put_contents($this->_cacheFile, $data, LOCK_EX) === false ) {
			return false;
		}
		@chmod($this->_cacheFile, 0644);
		@touch($this->_cacheFile, $this->_modTime);

	}


	protected function _dataUri($i)
	{
		return 'data:' . $this->_mimeType() . ';base64,' . base64_encode($i);

	}


	protected function _404()
	{
		header('HTTP/1.0 404 Not Found');
		exit;

	}


	protected function _checkFile()
	{
		if (!file_exists($this->_file)) {
			if ((!$this->tn)
			|| ( !str_ends_with($this->_file, $this->tn.'.'.$this->_inType) )
			|| ( !file_exists($this->_file = str_lreplace($this->tn.'.', '.', $this->_file)) )) {
				$this->_404();
			}	
		}
		$this->_modTime = filemtime($this->_file);

	}


	protected function _parseArgs()
	{
		foreach ($this->_args as $arg) {
			foreach ($this->options as $option => $regex) {
				if ( (!isset($this->_params[$option]))
				&& (preg_match($regex, $arg, $matches)) ) {
					$this->_params[$option] = $matches;
					break;
				}
			}
		}

	}


	protected function _process($ext)
	{
		if ($ext == 'jpeg') {
			$ext = 'jpg';
		}
		foreach ($this->_params as $option => $value) {
			$func = '_' . $ext . '_' . $option;
			if (method_exists($this, $func)) {
				$this->$func($value);
				if ($ext != 'svg') { // workaround for Imagick's inability to read svg style block
					unset($this->_params[$option]);
				}
			}
		}

	}


	protected function _mimeType()
	{
		$mimeMap = array(
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'png' => 'image/png',
			'svg' => 'image/svg+xml',
			'tif' => 'image/tiff'
		);
		return $mimeMap[$this->_outType];

	}


	protected function _svgOut()
	{
		// $this->_process('svg');

	}


	protected function _jpgOut()
	{
		$this->_process('jpg');

		$this->_img->object->setImageFormat('jpeg');

		$this->_img->object->setImageCompression(Imagick::COMPRESSION_JPEG);
		$this->_img->object->setImageCompressionQuality($this->jpegCompression);
		if ($this->progressive) {
			$this->_img->object->setInterlaceScheme(Imagick::INTERLACE_PLANE);
		}
		$this->_img->object->unsharpMaskImage($this->usm['radius'],$this->usm['sigma'],$this->usm['amount'],$this->usm['threshold']);

		$this->_img->object->stripImage();
		$this->_img->syncData();

	}


	protected function _pngOut()
	{
		$this->_process('png');

		$this->_img->object->setImageFormat('png');

		if (!is_dir($this->tempDir)) {
			if ( @mkdir($this->tempDir, 0711, true) === false ) {
				return false;
			}
		}
		$tmp = $this->tempDir . uniqid() . '.png';

		$this->_img->object->stripImage();
		$this->_img->object->writeImage($tmp);

		$cmd = str_replace('{file}', $tmp, $this->pngOpt);
		@shell_exec($cmd);

		$this->_img->loadImage($tmp);
		unlink($tmp);

	}


	protected function _gifOut()
	{
		$this->_process('gif');

		$this->_img->object->setImageFormat('gif');
		$this->_img->object->stripImage();
		$this->_img->syncData();

	}


}



class Dh_ImageServer_Image
{
	public $data;
	public $object;


	public function __construct($file)
	{
		$this->loadImage($file);

	}


	public function loadImage($file)
	{
		$this->data = file_get_contents($file);
		$this->object = new Imagick();
		$this->syncObject();

	}


	public function syncData()
	{
		$this->data = $this->object->getImageBlob();

	}


	public function syncObject()
	{
		$this->object->clear();
		$this->object->readImageBlob($this->data);
		if ($this->object->getImageFormat() == 'SVG') {
			$this->object->setImageFormat('tiff');
			// $this->object->setImageFormat('png');
		}

	}

}



?>
