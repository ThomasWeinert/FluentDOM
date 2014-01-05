<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
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
XML;

require_once('../src/FluentDOM.php');
echo FluentDOM::Query($xml)
  ->find('//p')
  ->removeAttr('index');
