<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <p> is what I said... </p><div id="foo">FOO!</div>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//p')
  ->insertAfter('//div[@id = "foo"]');
?>