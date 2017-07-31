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
    <title>Examples: FluentDOM\Query::addBack()</title>
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
  ->find('.//b')
  ->addBack()
  ->toggleClass('inB');

echo "\n\n";

$fd = FluentDOM($xml);
echo $fd
  ->find('//p')
  ->find(
    $fd->find('//div')
  )
  ->addBack()
  ->toggleClass('inB');

