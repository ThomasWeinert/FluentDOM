<?php
/**
* Example file for property 'attr'
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2010-2014 FluentDOM Contributors
*/
require_once __DIR__.'../../../vendor/autoload.php';

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
$fd = FluentDOM($html, 'text/html')->find('/html/body//*');
// like $fd->removeAttr(['style', 'target']); but array syntax
unset($fd->attr[['style', 'target']]);
echo (string)$fd;