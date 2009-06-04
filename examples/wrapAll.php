<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <div>
    <p>Hello</p>
    <p>cruel</p>
  </div>
  <div>
    <p>World</p>
  </div>
</body>
</html>
XML;

require_once('../FluentDOM.php');

echo FluentDOM($xml)
  ->find('//p')
  ->wrapAll('<div class="wrapper"/>')
  ->document
  ->saveXML();
?>