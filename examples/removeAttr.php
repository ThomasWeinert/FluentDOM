<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <div>
    <p index="0">Hello</p>
    <p index="1">cruel</p>
    <p index="2">World</p>
  </div>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//p')
  ->removeAttr('index');
?>