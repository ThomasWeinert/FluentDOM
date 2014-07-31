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
    <title>Examples: FluentDOM\Query::removeAttr()</title>
  </head>
  <body>
    <div>
      <p index="0">Hello</p>
      <p index="1">cruel</p>
      <p index="2">World</p>
    </div>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//p')
  ->removeAttr('index');
