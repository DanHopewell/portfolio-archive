# Possibly the most over-engineered single-page portfolio site I could have possibly built.

Portfolio content is served with custom PHP and managed via a series of YAML flat files parsed by [sfYaml‎](http://www.symfony-project.org/api/1_4/sfYaml), with typographic enhancements courtesy of [PHP Markdown](http://michelf.ca/projects/php-markdown/), [PHP Smarty Pants](http://michelf.ca/projects/php-smartypants/) and [Shaun Inman's Widon't function](http://shauninman.com/archive/2006/08/22/widont_wordpress_plugin). The PHP output is cached server-side by a custom stand-alone PHP class heavily inspired by [PEAR Cache](http://pear.php.net/package/Cache/).

Images, scripts and style sheets are served via custom PHP with pixel manipulations by [ImageMagick](http://www.php.net/manual/en/book.imagick.php), PNG compression by [OptiPNG](http://optipng.sourceforge.net), CSS minification by [CssMin](http://code.google.com/p/cssmin/) and JS optimization via [Closure Compiler](https://developers.google.com/closure/compiler/).
