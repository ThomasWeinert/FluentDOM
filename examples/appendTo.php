<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <span>I have nothing more to say... </span>
  <div id="foo">FOO! </div>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//span')
  ->appendTo('//div[@id = "foo"]');
?>