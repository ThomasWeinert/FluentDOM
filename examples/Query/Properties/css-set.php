<?php
/**
* Example file for property 'css'
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2010-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$html = <<<HTML
<html>
 <head>
  <title>FluentDOM project page</title>
 </head>
 <body>
  <p>
   Always nice to visit
   <a href='http://fluentdom.org'>here</a> or
   <a href='http://github.org/FluentDOM'>here.</a>
  </p>
 </body>
</html>
HTML;

echo "Example for property 'css'- setting text-decoration:\n\n";
require_once('../../../vendor/autoload.php');
$fd = FluentDOM($html, 'text/html');
$fd
  ->find('//a')
  ->css['text-decoration'] = 'none';
echo (string)$fd;