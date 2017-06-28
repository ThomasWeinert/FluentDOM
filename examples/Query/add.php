<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::add()</title>
  </head>
  <body>
    <p>I would like to say: <b>HELLO</b></p>
    <b>HELLO</b>

    <div>Another list of childNodes</div>
  </body>
</html>
XML;

require('../../vendor/autoload.php');
$fd = FluentDOM($xml);
echo $fd
  ->find('//p')
  ->add('//p/b')
  ->toggleClass('inB');

echo "\n\n";

$fd = FluentDOM($xml);
echo $fd
  ->find('//p')
  ->add(
    $fd->find('//div')
  )
  ->toggleClass('inB');

echo "\n\n";

$fd = FluentDOM($xml);
echo $fd
  ->add(
    $fd->find('//div')
  )
  ->toggleClass('inB');

echo "\n\n";

$fd = FluentDOM($xml);
echo $fd
  ->add('//div')
  ->toggleClass('inB');
