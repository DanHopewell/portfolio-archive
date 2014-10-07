<?php

require_once('loader.php');

loadFunc('Markdown');
loadFunc('MarkdownUrl');
loadFunc('SmartyPants');
loadFunc('amputator');
loadFunc('widont');
loadFunc('str_freplace');

if (isset($smartypants_attr)) {
	$smartypants_attr = '2';
}

class Dh_Portfolio3
{

	private $_portfolioDir = 'portfolio';
	private $_projectDir = 'projects';
	private $_imageDir = 'images';
	private $_thumbDir = 'images/thumbs';
	private $_thumbSuffix = '_tn';
	private $_templateDir = 'templates';
	private $_defaultTemplatePath;
	private $_formatRules;
	private $_projectList;
	private $_projects;
	private $_config;
	
	
	
	public function __construct($dir = NULL)
	{
		if (isset($dir)) $this->_portfolioDir = $this->_trailingSlash($dir);
		
		$this->_init();
	
	}

	public function modTime()
	{
		$modTime = 0;
		foreach ($this->_projects as $key => $project) {
			$modTime = max($project->modTime, $modTime);
		}
		return $modTime;

	}
	
	
	public function output($templateName = NULL)
	{
		$templatePath = $this->_getTemplatePath($templateName);
		
		$out = '';
		
		// loop through projects
		foreach ($this->_projects as $key => $project) {
		
			$optionsArray = array(
				'project' => $project, // project object
				'templatePath' => $templatePath
			);
			if ($key > 0) $optionsArray['prevName'] = $this->_projects[$key-1]->shortName;
			if ($key < count($this->_projectList)) $optionsArray['nextName'] = $this->_projects[$key+1]->shortName;
			
			// apply template and append output
			$out .= $this->_getTemplate($optionsArray);
			
		}
		
		return $out;
		
	}

	
	
	private function _init()
	{
		$configArray = $this->_loadConfig();
		
		$this->_projectList = $configArray['projectlist'];
		
		if (array_key_exists('projectdir', $configArray)) $this->_projectDir = $configArray['projectdir'];
		if (array_key_exists('imagedir', $configArray)) $this->_imageDir = $configArray['imagedir'];
		if (array_key_exists('thumbdir', $configArray)) $this->_thumbDir = $configArray['thumbdir'];
		if (array_key_exists('templatedir', $configArray)) $this->_templateDir = $configArray['templatedir'];
		
		$this->_projectDir = $this->_getDirPath($this->_projectDir);
		$this->_imageDir = $this->_getDirPath($this->_imageDir);
		$this->_thumbDir = $this->_getDirPath($this->_thumbDir);
		$this->_templateDir = $this->_getDirPath($this->_templateDir);
		
		if (array_key_exists('thumbsuffix', $configArray)) $this->_thumbSuffix = $configArray['thumbsuffix'];
		
		$this->_defaultTemplatePath = $this->_getTemplatePath($configArray['template']);
		
		$this->_formatRules = $configArray['formatrules'];
		
		$this->_loadProjects();
	
	}
	
	
	private function _loadConfig()
	{
		$path = $this->_portfolioDir . 'config.yml';
		return sfYaml::load($path);
	
	}
	
	
	private function _loadProjects()
	{
		$this->_projects = array();
		foreach ($this->_projectList as $key => $shortName) {			
			$optionsArray = array(
				'projectDir' => $this->_projectDir,
				'shortName' => $shortName
			);
			
			$this->_projects[] = new Dh_PortfolioProject3($optionsArray);
		}
	
	}
	
	
	private function _getTemplate($optionsArray)
	{	
		$format = true; // default
		
		foreach ($optionsArray as $option => $value) {
			$$option = $value;
		}
		
		ob_start();
		
			// prepare template variables
			// if $format = true, format text
			
			$shortName = $project->shortName;
			
			if ($format) {
				$fullName = $this->_formatText('name', $project->fullName);
				// $body = $this->_formatText('body', $project->body);
			} else {
				$fullName = $project->fullName;
				// $body = $project->body;
			}
			
			foreach ($project->metaArray as $field => $value) {
				if ($format) {
					$$field = $this->_formatText($field, $value);
				} else {
					$$field = $value;
				}
			}

			$blocks = array();

			foreach ($project->blocks as $name => $block) {

				if ($format) {
					$body = $this->_formatText('body', $block['body']);
				} else {
					$body = $block['body'];
				}

				$images = array();

				// loop through image sets
				foreach ($block['imageSets'] as $set => $imageSet){
					// loop through image/caption arrays within each image set
					foreach ($imageSet as $key => $imageData) {
						$images[$set][$key] = $imageData;
						unset($images[$set][$key]['image']);
						$images[$set][$key]['imagePath'] = $this->_getImagePath($imageData['image']);
						$images[$set][$key]['thumbPath'] = $this->_getThumbPath($imageData['image']);
						if ( ($format)
						&& (array_key_exists('caption', $images[$set][$key])) ) {
							$images[$set][$key]['caption'] = $this->_formatText('caption', $images[$set][$key]['caption']);
						}
					} // end loop
				} // end loop

				// if the only image set is 'images', collapse $images['images'] to $images
				if ( count($images) == 1
				&& array_key_exists('images', $images) ) {
					$images = $images['images'];
				}

				$blocks[$name] = array('body' => $body, 'images' => $images);
			}
			
			// load template
			require($templatePath);
		
		$out .= ob_get_contents();
		ob_end_clean();
		$out .= PHP_EOL . PHP_EOL;
		
		return $out;
	}
	
	
	private function _formatText($tag, $content)
	{	
		// we fallback to default rules if rules not found for $tag
		if ( !array_key_exists($tag, $this->_formatRules) ) $tag = 'default';
		
		// search format rules array for 'list' format rule and save array key if found
		foreach ($this->_formatRules[$tag] as $key => $item) {
			if ( is_array($item) && array_key_exists('list', $item) ) {
				$listkey = $key;
				break;
			}
		}
		
		// save $content to $text, imploding it to a string first if it's an array
		// for implode, use specified delimiter if found or default to ', '
		if (is_array($content)) {
			if ( (!isset($listkey)) || (empty($this->_formatRules[$tag][$listkey]['list'])) ) {
				$delimiter = ', ';
			} else {
				$delimiter = $this->_formatRules[$tag][$listkey]['list'];
			}
			$text = implode($delimiter, $content);
		} else {
			$text = $content;
		}
		
		// run though format rules in order
		if (in_array('no-format', $this->_formatRules[$tag])) {
			return $text; // 'no-format' overrides all other text formatting
		} else {
			if (in_array('markdown', $this->_formatRules[$tag])) $text = Markdown($text);
			if (in_array('url', $this->_formatRules[$tag])) $text = MarkdownUrl($text);
			if (in_array('smart', $this->_formatRules[$tag])) $text = SmartyPants($text);
			$text = amputator($text);
			if (in_array('widows', $this->_formatRules[$tag])) {
				if(preg_match("/<p>(.*)<\/p>/i", $text)) {
				    preg_match_all("/<p>(.*?)<\/p>/i", $text, $widowarr);
				    $text = '';
				    foreach ($widowarr[0] as $graf) {
				    	$text .= widont($graf);
				    }
				} else {
					$text = widont($text);
				}	
			}
			
			return $text;
		}

	}
	
	
	private function _getDirPath($dir)
	{
		// if specified directory is not an absolute path, locate within default directory
		if ( (substr($dir, 0, 1) != '/')
		&& (substr($dir, 0, 7) != 'http://')
		&& (substr($dir, 0, 8) != 'https://') ) {
			$dir = $this->_portfolioDir . $dir;
		}
		return $this->_trailingSlash($dir);
	
	}
	
	
	private function _getTemplatePath($templateName = NULL)
	{
		if (isset($templateName)) {
			$templatePath = $this->_templateDir . $templateName . '.php';
		} else {
			if (isset($this->_defaultTemplatePath)) {
				$templatePath = $this->_defaultTemplatePath;
			} else {
				$templatePath = $this->_templateDir . 'template.php';
			}
		}
		
		return $templatePath;
	}
	
	
	private function _getImagePath($imageName)
	{
		return $this->_imageDir . $imageName;
	
	}
	
	
	private function _getThumbPath($imageName)
	{
		return $this->_thumbDir . str_freplace('.', $this->_thumbSuffix . '.', $imageName);
	
	}


	private function _trailingSlash($p)
	{
		if (substr($p, -1) != '/') {
			$p .= '/';
		}
		return $p;
	}

}

?>
