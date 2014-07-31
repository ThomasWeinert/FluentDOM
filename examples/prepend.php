<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');
require_once('../vendor/autoload.php');

$html = <<<HTML
<html>
  <head>
    <title>Examples: FluentDOM\Query::prepend()</title>
  </head>
  <body>
    <p> he said. </p>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//p')
  ->prepend('<strong>Hello</strong>');
