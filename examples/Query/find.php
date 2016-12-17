<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::find()</title>
  </head>
  <body>
    <p><span>Hello</span>, how are you?</p>
    <p>Me? I'm <span>good</span>.</p>
  </body>
</html>
XML;

require_once('../../vendor/autoload.php');
echo FluentDOM($xml)
  ->find('//p')
  ->find('span')
  ->addClass('red');
