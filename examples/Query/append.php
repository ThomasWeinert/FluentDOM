<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
header('Content-type: text/plain');
require_once '../../vendor/autoload.php';

$html = <<<HTML
<html>
  <head>
    <title>Examples: FluentDOM\Query::append()</title>
  </head>
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
HTML;

echo FluentDOM($html)
  ->find('//p')
  ->append('<strong>Hello</strong>')
  ->formatOutput();

echo "\n\n";

$fd = FluentDOM($html);
$items = $fd->find('//group/item');
echo $fd
  ->find('//html/div')
  ->append($items)
  ->formatOutput();