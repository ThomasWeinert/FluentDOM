<?php
/**
* Example file for property 'attr'
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
  <p style="color: red;">
   Always nice to visit
   <a href='http://fluentdom.org' target="_blank">here</a> or
   <a href='http://github.org/FluentDOM' target="_blank">here.</a>
  </p>
 </body>
</html>
HTML;

echo "Example for property 'attr' - remove attributes:\n\n";
require_once('../../vendor/autoload.php');
$fd = FluentDOM($html, 'text/html')->find('/html/body//*');
// like $fd->removeAttr(array('style', 'target')); but array syntax
unset($fd->attr[array('style', 'target')]);
echo (string)$fd;