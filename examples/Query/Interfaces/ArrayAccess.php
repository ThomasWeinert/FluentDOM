<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query ArrayAccess interface</title>
  </head>
<body>
  <p>Hello</p>
  <p>cruel</p>
  <p>World</p>
</body>
</html>
XML;

require_once '../../../vendor/autoload.php';

$fd = FluentDOM($xml)->find('//p');
echo $fd[0], ' ', $fd[2];
