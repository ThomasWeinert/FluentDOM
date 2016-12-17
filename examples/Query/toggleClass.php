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
    <title>Examples: FluentDOM\Query::toggleClass()</title>
  </head>
  <body>
    <p class="blue">foo</p>
    <p class="blue highlight">bar</p>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//p')
  ->toggleClass('highlight');
