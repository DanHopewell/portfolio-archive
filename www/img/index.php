<?php

require_once('loader.php');

class MyServer extends Dh_ImageServer
{

	protected $_resizeMax = 2000;



	protected function _svg_fill($matches)
	{
		$fill = $matches[1];
		$css = '.dh-fill {fill: #' . $fill . ' !important;}';
		$svg = new SimpleXMLExtended($this->_img->data);
		if (isset($svg->style)) {
			$svg->style[0] .= PHP_EOL . "\t" . "\t" . $css . PHP_EOL . "\t";
		} else {
			$svg->prependChild('style', PHP_EOL . $css . PHP_EOL);
		}
		$this->_img->data = $svg->asXML();
		$this->_img->syncObject();
	}



	protected function _jpg_resize($matches)
	{
		return $this->_raster_resize($matches);
	}

	protected function _png_resize($matches)
	{
		return $this->_raster_resize($matches);
	}

	protected function _raster_resize($matches)
	{
		$od = $this->_img->object->getImageGeometry(); 
		$ow = $od['width'];
		$oh = $od['height'];
		$orat = $ow / $oh;

		$nd = $matches[0];
		$nw = ( preg_match('/(\d*)w/i', $nd, $m) ? $m[1] : false );
		$nh = ( preg_match('/(\d*)h/i', $nd, $m) ? $m[1] : false );
		if ( preg_match('/(\d*)max/i', $nd, $m) ) {
			if ($ow >= $oh) {
				$nw = $m[1];
			} else {
				$nh = $m[1];
			}
		}
		if ( preg_match('/(\d*)min/i', $nd, $m) ) {
			if ($ow <= $oh) {
				$nw = $m[1];
			} else {
				$nh = $m[1];
			}
		}

		$nrat = ($nw && $nh) ? ($nw / $nh) : $orat;
		$crop = preg_match('/c$/i', $nd) * !($orat == $nrat);

		if ($crop) {
			if ($orat > $nrat) { // original is wider
				$cw = $oh * $nrat;
				$ch = $oh;
				$x = ($ow - $cw)/2;
				$y = 0;
			} else { // original is taller
				$cw = $ow;
				$ch = $ow / $nrat;
				$x = 0;
				$y = ($oh - $ch)/2;
			}
			$this->_img->object->cropImage($cw, $ch, $x, $y);
		}

		if ( $nw && ($orat <= $nrat) ) {
			$w = ($nw < $this->_resizeMax) ? $nw : $this->_resizeMax;
			$h = 0;
		} else {
			$w = 0;
			$h = ($nh < $this->_resizeMax) ? $nh : $this->_resizeMax;
		}

		// $img->scaleImage($w, $h);
		$this->_img->object->resizeImage($w, $h, Imagick::FILTER_LANCZOS, 1);

		$this->_img->syncData();

	}



	protected function _jpg_bw($matches)
	{
		return $this->_raster_bw($matches);
	}

	protected function _png_bw($matches)
	{
		return $this->_raster_bw($matches);
	}

	protected function _raster_bw($matches = NULL)
	{
		$this->_img->object->modulateImage(100,0,100);
		$this->_img->syncData();
		
	}



	protected function _jpg_inv($matches)
	{
		return $this->_raster_inv($matches);
	}

	protected function _png_inv($matches)
	{
		return $this->_raster_inv($matches);
	}

	protected function _raster_inv($matches)
	{
		$this->_img->object->negateImage(0);
		$this->_img->syncData();

	}



	protected function _jpg_norm($matches)
	{
		return $this->_raster_norm($matches);
	}

	protected function _png_norm($matches)
	{
		return $this->_raster_norm($matches);
	}

	protected function _raster_norm($matches)
	{
		// $this->_img->object->normalizeImage(Imagick::CHANNEL_ALL);
		
		$r = $this->_img->object->getImageChannelExtrema(Imagick::CHANNEL_RED);
		$g = $this->_img->object->getImageChannelExtrema(Imagick::CHANNEL_GREEN);
		$b = $this->_img->object->getImageChannelExtrema(Imagick::CHANNEL_BLUE);

		$min = min($r['minima'],$g['minima'],$b['minima']);
		$max = min($r['maxima'],$g['maxima'],$b['maxima']);

		$this->_img->object->levelImage ($min, 1.0, $max);

		$this->_img->syncData();

	}



	protected function _jpg_fill($matches)
	{
		return $this->_raster_fill($matches);
	}

	protected function _png_fill($matches)
	{
		return $this->_raster_fill($matches);
	}

	protected function _raster_fill($matches)
	{
		// $this->_raster_bw();
		$this->_img->object->modulateImage(100,0,100);

		$hex = $matches[1];
		$fill = array();

		if(strlen($hex) == 3) {
			$fill['r'] = hexdec( substr($hex,0,1) . substr($hex,0,1) );
			$fill['g'] = hexdec( substr($hex,1,1) . substr($hex,1,1) );
			$fill['b'] = hexdec( substr($hex,2,1) . substr($hex,2,1) );
		} else {
			$fill['r'] = hexdec( substr($hex,0,2) );
			$fill['g'] = hexdec( substr($hex,2,2) );
			$fill['b'] = hexdec( substr($hex,4,2) );
		}

		$rows = $this->_img->object->getPixelIterator();

		// replace white with color
		// foreach($rows as $cols) {
		// 	foreach($cols as $pixel) {

		// 		$tint = $pixel->getColorValue(Imagick::COLOR_RED);

		// 		$pixel->setColorValue( Imagick::COLOR_RED, ($fill['r']/255 * $tint) );
		// 		$pixel->setColorValue( Imagick::COLOR_GREEN, ($fill['g']/255 * $tint) );
		// 		$pixel->setColorValue( Imagick::COLOR_BLUE, ($fill['b']/255 * $tint) );

		// 		$rows->syncIterator();
		// 	}
		// }

		// replace black with color
		foreach($rows as $cols) {
			foreach($cols as $pixel) {

				$tint = $pixel->getColorValue(Imagick::COLOR_RED);
				$r = $tint * (255 - $fill['r']) + $fill['r'];
				$g = $tint * (255 - $fill['g']) + $fill['g'];
				$b = $tint * (255 - $fill['b']) + $fill['b'];

				$pixel->setColorValue(Imagick::COLOR_RED, $r/255);
				$pixel->setColorValue(Imagick::COLOR_GREEN, $g/255);
				$pixel->setColorValue(Imagick::COLOR_BLUE, $b/255);

				$rows->syncIterator();
			}
		}

		$this->_img->syncData();

	}



	protected function _png_trans($matches)
	{
		// $this->_img->object->setImageFormat('png32');
		// $this->_img->syncData();
		// $this->_img->syncObject();

		$this->_img->object->modulateImage(100,0,100);
		$this->_img->object->negateImage(0);
		// $img = $this->_raster_bw();

		$fill = $matches[1] ? '#' . $matches[1] : '#fff';

		// $this->_img->object->setImageAlphaChannel(Imagick::ALPHACHANNEL_SET);
		$this->_img->object->setImageAlphaChannel(Imagick::ALPHACHANNEL_TRANSPARENT);
		$rows = $this->_img->object->getPixelIterator();
		foreach($rows as $cols) {
			foreach($cols as $pixel) {
				$tint = $pixel->getColorValue(Imagick::COLOR_RED);

				$pixel->setColor($fill);
				$pixel->setColorValue(Imagick::COLOR_ALPHA, ($tint));

				$rows->syncIterator();
			}
		}

		$this->_img->syncData();

	}



	protected function _jpg_compression($matches)
	{
		if($matches[1] <= 100) {
			$this->jpegCompression = $matches[1];
		}
	}


}

//	protected function _[format]_[action]([array of regex matches])
//	{
//		$this->_img->object = [...]
//		$this->_img->syncData();
//		
//		$this->_img->data = [...]
//		$this->_img->syncObject();
//		
//	}

$img = new MyServer;

$img->options = array(
	'fill' => '/^hex([\dA-F]{3}|[\dA-F]{6})$/i', // e.g., hexf908ac, hexf00
	// 'resize' => '/(^\d+w\d+hc?$)|(^\d+h\d+wc?$)|(^\d+[w|h]$)/i', // e.g., 400w300h, 60h60wc, 4000w, 30h
	'resize' => '/(^\d+w\d+hc?$)|(^\d+h\d+wc?$)|(^\d+(w|h|max|min)$)/i', // e.g., 400w300h, 60h60wc, 4000w, 30h, 640max, 320min
	'trans' => '/^trans([\dA-F]{3}|[\dA-F]{6}|)$/i', // e.g., trans21ac97, transfff, trans
	'bw' => '/^bw$/i',
	'inv' => '/^inv$/i',
	'norm' => '/^norm$/i',
	'compression' => '/^c(\d{1,3})$/i' // e.g., c100, c85, c5
);

$img->serve();



?>