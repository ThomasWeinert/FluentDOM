<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::remove()</title>
  </head>
  <body>
    <p class="first">Hello One</p>
    <p>Hello Two</p>
  </body>
</html>
XML;

require_once('../src/FluentDOM.php');
echo FluentDOM::Query($xml)
  ->find('//p[@class = "first"]')
  ->remove();
