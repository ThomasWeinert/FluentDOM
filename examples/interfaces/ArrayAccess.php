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

require_once('../../src/FluentDOM.php');

$dom = FluentDOM::Query($xml)->find('//p');
echo $dom[0]->textContent, ' ', $dom[2]->textContent;
