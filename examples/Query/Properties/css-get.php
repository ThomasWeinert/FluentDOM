<?php
/**
* Example file for property 'attr'
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2010-2014 FluentDOM Contributors
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
   <a href='http://fluentdom.org' style="color: red;">here</a> or
   <a href='http://github.org/FluentDOM' style="color: blue;">here.</a>
  </p>
 </body>
</html>
HTML;

echo "Example for property 'css' - reading color:\n\n";
require_once('../../../vendor/autoload.php');
$fd = FluentDOM($html, 'text/html')->find('//a');
foreach ($fd as $node) {
  $fdNode = FluentDOM($node);
  echo $fdNode->css['color'], "\n";
}