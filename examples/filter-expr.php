<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::filter() with expression</title>
  </head>
  <body>
    <div/>
    <div class="middle"/>
    <div class="middle"/>
    <div class="middle"/>
    <div class="middle"/>
    <div/>
  </body>
</html>
XML;

require_once('../vendor/autoload.php');
echo FluentDOM($xml, 'text/html')
  ->find('//div')
  ->attr('border', 1)
  ->filter('@class = "middle"')
  ->attr('style', 'text-align: center;');
