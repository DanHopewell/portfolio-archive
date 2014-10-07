<?php

class ImagickExtended extends Imagick
{
    public function writeImageBlob()
    {
        ob_start();
		echo $this;
		$str = ob_get_contents();
		ob_end_clean();
		return $str;
    }
}

?>