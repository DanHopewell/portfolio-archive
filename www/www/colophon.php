<?php

require_once('loader.php');

$cache = new Dh_OutputCache();
if (!$cache->start('colophon', 82800)) :

$url = new Dh_UrlHelper;

?><!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Dan Hopewell | Colophon</title>
		<meta name="description" content="Dan Hopewell is an artist, designer and front-end developer in Chicago.">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<![if gt IE 8]>
		<link rel="stylesheet" href="<?=$url->css('reset_base_main.css','min')?>">
		<![endif]>
		<!--[if IE 8]>
		<link rel="stylesheet" href="<?=$url->css('reset_base_main_ie8.css')?>">
		<![endif]-->
		<!--[if lt IE 8]>
		<link rel="stylesheet" href="<?=$url->css('reset_base_ie-entropy.css')?>">
		<![endif]-->

		<!--[if lt IE 9]>
		<script src="<?=$url->js('vendor/html5shiv-min.js')?>"></script>
		<![endif]-->
		<script type="text/javascript" src="//use.typekit.net/yxl6fmq.js"></script>
		<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
	</head>
	<body>
		<!--[if lt IE 8]>
			<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
		<![endif]-->

		<header id="pagehead">
			<nav id="pagenav">
				<h1><a href="#pagehead">Dan Hopewell</a></h1>
				<ul>
					<li><a href="/">Work</a></li>
					<li><a href="#about">About</a></li>
					<li><a href="#contact">Contact</a></li>
				</ul>
			</nav>
		</header>

		<div id="colophon">

			<section id="colophon-type">
				<h2>Typography</h2>

				<p>Web fonts served by <a href="https://typekit.com/">Typekit</a>.</p>

				<p>Headings and display text are set in <a href="https://typekit.com/fonts/p22-underground">P22 Underground</a>, a digitization by <a href="https://www.p22.com">P22 Type Foundry</a> of Edward Johnston's <a href="http://en.wikipedia.org/wiki/Johnston_(typeface)">legendary London Underground typeface from 1916</a>. This is the direct typographical forerunner to Gill Sans, which is used in my personal wordmark and print&nbsp;materials.</p>

				<p>Body text is set in <a href="https://typekit.com/fonts/freight-sans-pro">Freight Sans</a>, a humanist sans-serif designed by <a href="http://www.dardenstudio.com">Joshua Darden</a> in&nbsp;2005.</p>
			</section>

			<section id="colophon-front">
				<h2>Front end</h2>

				<p>This site is built with hand-coded, standards-focused <abbr>HTML</abbr> and <abbr>CSS</abbr> on a skeleton provided by the <a href="http://html5boilerplate.com"><abbr>HTML5</abbr> Boilerplate</a> and <a href="http://meyerweb.com/eric/tools/css/reset/">Meyer <abbr>CSS</abbr> Reset</a>. Toeholds in the <abbr>DOM</abbr> are provided by <a href="http://jquery.com">jQuery</a>, a modicum of backwards compatibility provided by <a href="http://code.google.com/p/html5shiv/">the HTML5 Shiv</a>. All other code is my&nbsp;own.</p>

				<p>It has been designed responsively to be viewed in modern browsers and on a variety of devices, with minimal loss in <abbr>IE8</abbr> and less graceful degradation in <abbr>IE7</abbr> and&nbsp;<abbr>IE6</abbr>.</p>

				<p>I strive to structure my markup in as semantically correct a manner as is reasonable. The practical benefits of this <a href="http://www.smashingmagazine.com/2011/11/11/our-pointless-pursuit-of-semantic-value/">can arguably be minimal</a>, but I figure it also can't hurt. And sometimes it just feels nice to fight on the side of the angels against the forces of&nbsp;entropy.</p>
			</section>

			<section id="colophon-back">
				<h2>Back end</h2>

				<p>Site hosted by <a href="http://www.icdsoft.com">ICDSoft</a>.</p>

				<p>Portfolio content is served with custom <abbr>PHP</abbr> and managed via a series of <abbr>YAML</abbr> flat files parsed by <a href="http://www.symfony-project.org/api/1_4/sfYaml">sfYaml‎</a>, with typographic enhancements courtesy of <a href="http://michelf.ca/projects/php-markdown/"><abbr>PHP</abbr> Markdown</a>, <a href="http://michelf.ca/projects/php-smartypants/"><abbr>PHP</abbr> Smarty Pants</a> and <a href="http://shauninman.com/archive/2006/08/22/widont_wordpress_plugin">Shaun Inman's Widon't function</a>. The <abbr>PHP</abbr> output is cached server-side by a custom stand-alone <abbr>PHP</abbr> class heavily inspired by <a href="http://pear.php.net/package/Cache/"><abbr>PEAR</abbr>&nbsp;Cache</a>.</p>

				<p>Images, scripts and style sheets are served via custom <abbr>PHP</abbr> with pixel manipulations by <a href="http://www.php.net/manual/en/book.imagick.php">ImageMagick</a>, <abbr>PNG</abbr> compression by <a href="http://optipng.sourceforge.net">OptiPNG</a>, <abbr>CSS</abbr> minification by <a href="http://code.google.com/p/cssmin/">CssMin</a> and <abbr>JS</abbr> optimization via <a href="https://developers.google.com/closure/compiler/">Closure&nbsp;Compiler</a>.</p>

				<p>None of the above would have been possible without a steady diet of&nbsp;<a href="http://stackoverflow.com">StackOverflow</a>.</p>
			</section>

			<section id="colophon-desktop">
				<h2>Desktop</h2>

				<p>Ever a slave to Apple and Adobe, I spend much of my time on my MacBook with my nose buried in Adobe <abbr>CS</abbr>/<abbr>CC</abbr> products. Photoshop is my general weapon of choice. For web work, <abbr>SVG</abbr>s are crafted in Illustrator and the cruft edited out by hand in my text&nbsp;editor.</p>

				<p>Code and text edited in <a href="http://www.sublimetext.com/2">Sublime Text 2</a>, images optimized with <a href="http://imageoptim.com">ImageOptim</a>, files shuttled to and fro with&nbsp;<a href="http://panic.com/transmit/">Transmit</a>.</p>
			</section>

		</div>

		<footer id="pagefoot">
			<div id="about">
				<p>Dan Hopewell is an artist, designer and front-end developer in&nbsp;Chicago.</p>

				<p>Turn-ons include Swiss aesthetics, semantic markup and Kant&rsquo;s Third Critique. Turn-offs include Swiss dogmatism, car doors and the neutral zone&nbsp;trap.</p>

				<p>Feel free to <a href="#email">email him</a>, to <a href="#phone">call him</a> or to watch hockey and baseball with him <a href="#twitter">on&nbsp;Twitter</a>.</p>

				<ul class="links">
					<li><a href="/resume">Resume</a></li>
					<li><a href="#colophon">Colophon</a></li>
				</ul>
			</div>

			<address id="contact">
				<a id="email" href="mailto:dan@danhopewell.com">dan@danhopewell.com</a>
				<a id="phone" href="tel:+17736272096">(773) 627-2096</a>
				<a id="twitter" href="https://twitter.com/DanHopewell">@danhopewell</a>
			</address>
		</footer>        

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="/js/vendor/jquery-1.11.1.min.js"><\/script>')</script>

		<![if gt IE 8]>
		<script src="<?=$url->js('utilities_menu_preload-main.js','min')?>"></script>
		<![endif]>
		<!--[if IE 8]>
		<script src="<?=$url->js('utilities_menu_preload-ie8.js')?>"></script>
		<![endif]-->
		
	</body>
</html>
<?php $cache->end(); endif; ?>
