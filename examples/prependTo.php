<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <div id="foo">Yellow! </div>
  <span>He thought </span>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//span')
  ->prependTo('//div[@id = "foo"]');
?>