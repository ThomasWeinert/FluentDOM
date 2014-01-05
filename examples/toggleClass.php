<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::toggleClass()</title>
  </head>
  <body>
    <p class="blue">foo</p>
    <p class="blue highlight">bar</p>
  </body>
</html>

XML;

require_once('../src/FluentDOM.php');
echo FluentDOM::Query($xml)
  ->find('//p')
  ->toggleClass('highlight');
