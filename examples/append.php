<?php
/**
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <p>I would like to say: </p>
  <items>
    <group>
      <item index="0">text1</item>
      <item index="1">text2</item>
      <item index="2">text3</item>
    </group>
    <html>
      <div class="test1 test2"><b>class testing</b></div>
      <div class="test2"><b>class testing</b></div>
    </html>
  </items>
</body>
</html>
XML;

require_once('../src/FluentDOM.php');
echo FluentDOM($xml)
  ->find('//p')
  ->append('<strong>Hello</strong>')
  ->formatOutput();

echo "\n\n";

$dom = FluentDOM($xml);
$items = $dom->find('//group/item');
echo $dom
  ->find('//html/div')
  ->append($items)
  ->formatOutput();

?>