<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');
require_once('../../vendor/autoload.php');

$html = <<<HTML
<html>
  <head>
    <title>Examples: FluentDOM\Query::wrapInner()</title>
  </head>
  <body>
    <p>Hello</p>
    <p>cruel</p>
    <p>World</p>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//p')
  ->wrapInner('<b></b>');
