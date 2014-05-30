<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::children()</title>
  </head>
  <body>
    <span>I have nothing more to say... </span>
    <div id="foo">FOO! </div>
  </body>
</html>
XML;

require_once('../vendor/autoload.php');
echo FluentDOM($xml)
  ->find('//span')
  ->clone()
  ->appendTo('//div[@id = "foo"]');