<?php
/**
* Example file for property 'css'
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2010-2014 FluentDOM Contributors
*/
require __DIR__.'/../../../vendor/autoload.php';

header('Content-type: text/plain');

$html = <<<HTML
<html>
 <head>
  <title>FluentDOM project page</title>
 </head>
 <body>
  <p style="color: red;">
   Always nice to visit
   <a href='http://fluentdom.org' style='color: red; font-weight: bold; text-decoration: none;'>here</a> or
   <a href='http://github.org/FluentDOM' style='color: blue; text-decoration: none;'>here.</a>
  </p>
 </body>
</html>
HTML;

echo "Example for property 'css' - remove properties:\n\n";
$fd = FluentDOM($html, 'text/html')->find('/html/body//*');
unset($fd->css[['color', 'font-weight']]);
echo (string)$fd;