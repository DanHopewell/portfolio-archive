<?php

require_once('loader.php');

loadFunc('str_freplace');

$url = ( isset($url) && is_object($url) ) ? $url : new Dh_UrlHelper();

if (!function_exists(templateImageClass)) {
	function templateImageClass($image)
	{
		$colW = 195;
		$rowH = 195;
		$gutter = 16;
		$pixelRatio = 2;

		if ( !isset($image['class']) ) {
			$image['class'] = 'single';
		}

		$h = $rowH * $pixelRatio;
		switch ($image['class']) {
			case 'triple':
				$w = ($colW * 3 + $gutter * 2) * $pixelRatio;
				break;
			case 'double':
				$w = ($colW * 2 + $gutter) * $pixelRatio;
				break;
			case 'single':
			default:
				$w = $colW * $pixelRatio;
		}

		$slug = '_'.$w.'w'.$h.'hc'.'.';
		$image['thumbPath'] = str_freplace('.', $slug, $image['thumbPath']);

		return $image;
	}
}

if ( (isset($client))
|| (isset($director))
|| (isset($team))
|| (isset($consultant))
|| (isset($roles))
|| (isset($deliverables))
|| (isset($tools)) ) {
	$meta = true;
} else {
	$meta = false;
}


?>
			<section id="<?=$shortName?>" class="project">

				<header>
					<h1><a href="#<?=$shortName?>" rel="bookmark"><?=$fullName?></a></h1>
					<ul class="projectnav">
<?php	if (isset($prevName)) : ?>
						<li><a href="#<?=$prevName?>" class="prev" rel="prev">Previous</a></li>
<?php	endif; ?>
<?php	if (isset($nextName)) : ?>
						<li><a href="#<?=$nextName?>" class="next" rel="next">Next</a></li>
<?php	endif; ?>
					</ul>
				</header>

<?php 	
		foreach ($blocks as $name => $block) :
			$i++;
			$blockId = $shortName . '-' . $name;
?>
				<section id="<?=$blockId?>">
					<div class="images">
<?php 	
			foreach ($block['images'] as $key => $image) :
				$image = templateImageClass($image);
				$key = $key+1;
?>
						<figure id="<?=$blockId.'-'.$key?>" class="<?=$image['class']?> thumb"><a href="<?php $url->img($image['imagePath']); ?>">
							<img title="<?=$image['caption']?>" alt="<?=$image['caption']?>" src="<?php $url->img($image['thumbPath']); ?>" />
						</a></figure>
<?php 		endforeach; ?>
					</div>
<?php		if ( ($meta)
			|| ( !empty($block['body']) ) ) : ?>
					<div class="text">

<?php			if ($meta) : 
					$meta = false; ?>
						<dl>
<?php				if (isset($client)) : ?>
							<dt>Client</dt>
							<dd><?=$client?></dd>
<?php				endif; ?>
<?php				if (isset($director)) : ?>
							<dt>Creative Direction</dt>
							<dd><?=$director?></dd>
<?php				endif; ?>
<?php				if (isset($team)) : ?>
							<dt>Team</dt>
							<dd><?=$team?></dd>
<?php				endif; ?>
<?php				if (isset($consultant)) : ?>
							<dt>Consulting</dt>
							<dd><?=$consultant?></dd>
<?php				endif; ?>
<?php				if (isset($roles)) : ?>
							<dt>Role</dt>
							<dd><?=$roles?></dd>
<?php				endif; ?>
<?php				if (isset($deliverables)) : ?>
							<dt>Deliverables</dt>
							<dd><?=$deliverables?></dd>
<?php				endif; ?>
<?php				if (isset($tools)) : ?>
							<dt>Tools</dt>
							<dd><?=$tools?></dd>
<?php				endif; ?>
						</dl>

<?php			endif; ?>
<?php			if ( !empty($block['body']) ) : ?>
					<?=$block['body']?>
<?php			endif; ?>

					</div>
<?php		endif; ?>
				</section>
<?php 	endforeach; ?>

			</section>
