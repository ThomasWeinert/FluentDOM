<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
require __DIR__.'/../../vendor/autoload.php';

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
