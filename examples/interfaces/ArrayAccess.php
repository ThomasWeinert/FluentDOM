<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <p>Hello</p>
  <p>cruel</p>
  <p>World</p>
</body>
</html>
XML;

require_once('../../FluentDOM.php');

$dom = FluentDOM($xml)->find('//p');
echo $dom[0]->textContent, ' ', $dom[2]->textContent;
?>