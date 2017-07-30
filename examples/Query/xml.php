<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
header('Content-type: text/plain');
require_once '../../vendor/autoload.php';

$html = <<<HTML
<html>
  <head>
    <title>Examples: FluentDOM\Query::xml()</title>
  </head>
  <body>
    <p>Hello</p>
    <p>cruel</p>
    <p>World</p>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//p[position() = last()]')
  ->xml('<b>New</b>World');
