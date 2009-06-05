<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <p class="first">Hello One</p>
  <p>Hello Two</p>
</body>
</html>
XML;

require_once('../FluentDOM.php');
$dom = FluentDOM($xml);
echo $dom
  ->find('//p[@class = "first"]')
  ->empty();
?>