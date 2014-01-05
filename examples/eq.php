<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::eq()</title>
  </head>
  <body>
    <div/>
    <div/>
    <div/>
    <div/>
    <div/>
    <div/>
  </body>
</html>
XML;

require_once('../src/FluentDOM.php');
echo FluentDOM::Query($xml, 'text/html')
  ->find('//div')
  ->eq(2)
  ->addClass('emphased');
