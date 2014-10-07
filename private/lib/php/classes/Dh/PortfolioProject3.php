<?php

class Dh_PortfolioProject3
{

	public $projectDir;
	public $shortName;
	public $fullName;
	public $blocks;
	public $metaArray;
	public $modTime;
	
	
	
	public function __construct($optionsArray)
	{
		foreach ($optionsArray as $option => $value) {
			$this->$option = $value;
		}
		
		$path = $this->projectDir . $this->shortName . '.yml';

		$dataArray = sfYaml::load($path);
		$this->modTime = filemtime($path);
		
		$this->fullName = $dataArray['name'];
				
		$this->metaArray = $dataArray['meta'];

		$this->blocks = array();

		if (array_key_exists('blocks', $dataArray)) {
			foreach ($dataArray['blocks'] as $name => $block) {
				$this->_buildBlock($block, $name);
			}
		} else {
			$this->_buildBlock($dataArray);
		}
	
	}



	private function _buildBlock($block, $name = 'main')
	{
		$this->blocks[$name] = array();

		$this->blocks[$name]['body'] = $block['body'];
		
		if (array_key_exists('images', $block)) {
			$imageSets = $this->_getImages($block['images']);
		}

		if (array_key_exists('imagesets', $block)) {
			$imageSets = array();
			foreach ($block['imagesets'] as $set => $dataArray) {
				$imageSets = $this->_getImages($dataArray, $imageSets, $set);
			}
		}

		$this->blocks[$name]['imageSets'] = $imageSets;

	}

	private function _getImages($dataArray, $imageSets = array(), $set = 'images')
	{
		foreach ($dataArray as $key => $data) {
			$imageSets[$set][$key] = $this->_getImageData($data);
		}
		return $imageSets;

	}
	
	
	private function _getImageData($data)
	{
		if (is_array($data)) {
			$image = key($data);
			if (is_array($data[$image])) {
				return array_merge($data[$image], array('image' => $image));
			} else {
				return array('image' => $image, 'caption' => $data[$image]);
			}
		} else {
			return array('image' => $data);
		}
	
	}

}

?>
