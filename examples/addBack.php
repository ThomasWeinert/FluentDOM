<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::addBack()</title>
  </head>
  <body>
    <p>I would like to say: <b>HELLO</b></p>
    <b>HELLO</b>

    <div>Another list of childNodes</div>
  </body>
</html>
XML;

require('../vendor/autoload.php');
$dom = FluentDOM($xml);
echo $dom
  ->find('//p')
  ->find('.//b')
  ->addBack()
  ->toggleClass('inB');

echo "\n\n";

$dom = FluentDOM($xml);
echo $dom
  ->find('//p')
  ->find(
    $dom->find('//div')
  )
  ->addBack()
  ->toggleClass('inB');

