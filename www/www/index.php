<?php

require_once('loader.php');

$cache = new Dh_OutputCache();
if (!$cache->start('main', 82800)) :

$portfolio = new Dh_Portfolio3('port');

$url = new Dh_UrlHelper;

?><!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Dan Hopewell</title>
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
    <body id="home">
        <!--[if lt IE 8]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <header id="pagehead">
            <nav id="pagenav">
                <h1><a href="#pagehead">Dan Hopewell</a></h1>
                <ul>
                    <li><a href="#work">Work</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
        </header>

        <div id="work">

<?=$portfolio->output()?>

        </div>

        <footer id="pagefoot">
            <div id="about">
                <p>Dan Hopewell is an artist, designer and front-end developer in&nbsp;Chicago.</p>

                <p>Turn-ons include Swiss aesthetics, semantic markup and Kant&rsquo;s Third Critique. Turn-offs include Swiss dogmatism, car doors and the neutral zone&nbsp;trap.</p>

                <p>Feel free to <a href="#email">email him</a>, to <a href="#phone">call him</a> or to watch hockey and baseball with him <a href="#twitter">on&nbsp;Twitter</a>.</p>

                <ul class="links">
                    <li><a href="/resume">Resume</a></li>
                    <li><a href="/colophon">Colophon</a></li>
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
        <script src="<?=$url->css('utilities_menu_preload-main.js','min')?>"></script>
        <![endif]>
        <!--[if IE 8]>
        <script src="<?=$url->css('utilities_menu_preload-ie8.js')?>"></script>
        <![endif]-->
        <script src="<?=$url->css('viewer.js','min')?>"></script>
        
    </body>
</html>
<?php $cache->end(); endif; ?>
