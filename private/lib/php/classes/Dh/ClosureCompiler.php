<?php

class Dh_ClosureCompiler
{

	public $delivery = 'js_code';
	public $compilation_level = 'SIMPLE_OPTIMIZATIONS';
	public $output_format = 'text';
	public $output_info = 'compiled_code';
	public $js_externs;
	public $externs_url;
	public $exclude_default_externs;
	public $output_file_name;
	public $formatting;
	public $use_closure_library;
	public $warning_level;
	public $language;

	private $_dataLimit = 200000;
	private $_required = array(
		'compilation_level',
		'output_format',
		'output_info'
	);
	private $_optional = array(
		'js_externs',
		'externs_url',
		'exclude_default_externs',
		'output_file_name',
		'formatting',
		'use_closure_library',
		'warning_level',
		'language'
	);



	public function __construct($optionsArray = array())
	{
		foreach ($optionsArray as $key => $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}
	}


	public function compress($js)
	{
		$query = $this->_query($js);
		if (strlen($query) > $this->_dataLimit) {
			return false;
		}
		$result = $this->_request($query);
		return $result;
	}



	private function _query($js)
	{	
		$query = '';

		foreach ($this->_required as $p) {
			$query .= $p;
			$query .= '=';
			$query .= $this->$p;
			$query .= '&';
		}

		foreach ($this->_optional as $p) {
			if (isset($this->$p)) {
				$query .= $p;
				$query .= '=';
				$query .= $this->$p;
				$query .= '&';
			}
		}

		$query .= $this->delivery;
		$query .= '=';
		$query .= urlencode($js);

		return $query;
	}


	private function _request($query)
	{
		$ch = curl_init('http://closure-compiler.appspot.com/compile');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8'));

		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}


}


?>