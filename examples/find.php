<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <p><span>Hello</span>, how are you?</p>
  <p>Me? I'm <span>good</span>.</p>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//p')
  ->find('span')
  ->addClass('red')
  ->document
  ->saveXML();
?>