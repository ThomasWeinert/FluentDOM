<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::nextAll()</title>
  </head>
  <body>
    <div>first</div>
    <div>sibling<div>child</div></div>
    <div>sibling</div>
    <div>sibling</div>
  </body>
</html>
XML;

require_once('../vendor/autoload.php');
echo FluentDOM($xml)
  ->find('//div[position() = 1]')
  ->nextAll()
  ->addClass('after');
