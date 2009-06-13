/**
*
* @version $Id $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*/
<?php
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
      <div class="test1 test2">class testing</div>
      <div class="test2">class testing</div>
    </html>
  </items>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//p')
  ->append('<strong>Hello</strong>');

echo "\n\n";

$dom = FluentDOM($xml)->find('//group/item');
echo $dom
  ->find('//html/div')
  ->append($dom);

?>