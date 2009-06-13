<?php
/**
*
* @version $Id $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <p>I would like to say: <b>HELLO</b></p>
  <b>HELLO</b>

  <div>Another list of childNodes</div>
</body>
</html>
XML;


require_once('../FluentDOM.php');
$dom = FluentDOM($xml);
echo $dom
  ->find('//p')
  ->add('//p/b')
  ->toggleClass('inB');

echo "\n\n";

$dom = FluentDOM($xml);
echo $dom
  ->find('//p')
  ->add(
    $dom->find('//div')
  )
  ->toggleClass('inB');

echo "\n\n";

$dom = FluentDOM($xml);
echo $dom
  ->add(
    $dom->find('//div')
  )
  ->toggleClass('inB');

echo "\n\n";

$dom = FluentDOM($xml);
echo $dom
  ->add('//div')
  ->toggleClass('inB');

?>